<?php

namespace App\Http\Controllers;

use App\Exceptions\MyException;
use App\Models\GarbageEntry;
use App\Models\MachineMater;
use App\Models\OrderPunchDetail;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class AccountingController extends Controller
{
    //

    protected $_M_GarbageEntry;
    protected $_M_User;
    protected $_M_Machine;
    protected $_M_OrderPunchDetail;

    function __construct()
    {
        $this->_M_GarbageEntry = new GarbageEntry();
        $this->_M_User = new User();
        $this->_M_Machine = new MachineMater();
        $this->_M_OrderPunchDetail = new OrderPunchDetail();
    }

    public function garbageVerification(Request $request){
        if($request->ajax()){
            
            $user = $this->_M_User->all();
            $machine = $this->_M_Machine->all();
            $data = $this->_M_GarbageEntry->select("garbage_entries.*","c.client_name",
                        DB::raw("((garbage/ (CASE WHEN roll_weight=0 THEN 1 ELSE roll_weight END))*100) AS garbage_per")
                    )
                    ->join("client_detail_masters as c","c.id","garbage_entries.client_id")
                    ->where("garbage_entries.lock_status",false)
                    ->where("garbage_entries.is_verify",false)
                    ->get()
                    ->map(function($val) use($user,$machine){
                        $val->operator_name = $user->where("id",$val->operator_id)->first()->name??"";
                        $val->helper_name = $user->where("id",$val->helper_id)->first()->name??"";
                        $val->machine = $machine->where("id",$val->machine_id)->first()->name??""; 
                        $val->cutting_date = $val->cutting_date ? Carbon::parse($val->cutting_date)->format("d-m-Y"):"";
                        $val->percent = roundFigure($val->roll_weight ? ($val->garbage/$val->roll_weight)*100 :0)." %";
                        $val->wip_percent = roundFigure($val->roll_weight ? ($val->wip_disbursed_in_kg/$val->roll_weight)*100 :0)." %";
                        $val->total_garbage = roundFigure($val->garbage +  $val->wip_disbursed_in_kg);
                        return $val;
                    })
                    ->sortBy([
                        ["total_garbage", "DESC"],
                        ["cutting_date", "asc"]
                    ]);
            $list = DataTables::of($data)
                ->addIndexColumn()                
                // ->addColumn('operator_name', function ($val) use($user) {  
                //     return $user->where("id",$val->operator_id)->first()->name??"";
                // })
                // ->addColumn('helper_name', function ($val) use($user) {  
                //     return $user->where("id",$val->helper_id)->first()->name??"";
                // })
                // ->addColumn("cutting_date",function($val){
                //     return $val->cutting_date ? Carbon::parse($val->cutting_date)->format("d-m-Y"):"";
                // })
                // ->addColumn("percent",function($val){
                //     return roundFigure($val->roll_weight ? ($val->garbage/$val->roll_weight)*100 :0)." %";
                // })
                // ->addColumn("wip_percent",function($val){
                //     return roundFigure($val->roll_weight ? ($val->wip_disbursed_in_kg/$val->roll_weight)*100 :0)." %";
                // })
                // ->addColumn("total_garbage",function($val){
                //     return roundFigure($val->garbage +  $val->wip_disbursed_in_kg);
                // })
                // ->addColumn("machine",function($val) use($machine){
                //     return $machine->where("id",$val->machine_id)->first()->name??"";                       
                // })
                ->addColumn('action', function ($val){                    
                    $button = "";
                    $totalGarbege = ($val->roll_weight ? ($val->garbage/$val->roll_weight)*100 :0) + roundFigure($val->roll_weight ? ($val->wip_disbursed_in_kg/$val->roll_weight)*100 :0);
                    if((!is_between($totalGarbege,-2,2)) && $val->wip_disbursed_in_kg){
                        $button .= '<button class="btn btn-sm btn-primary" onClick="openModel('.$val->id.')" >Verify</button>';
                        $button .= '<button class="btn btn-sm btn-danger" onClick="deleteConformation('.$val->id.')" >Delete</button>';
                    }                    
                    return $button;
                })
                ->rawColumns(['row_color', 'action'])
                ->make(true);
            return $list;

        }
        $data["remarks"] = Config::get("customConfig.garbageVerificationRemarks");
        return view("Accounting/garbageVerification",$data);
    }

    public function garbageClose(Request $request){
        try{
            $rules=[
                "id"=>"required|exists:".$this->_M_GarbageEntry->getTable().",id,is_verify,false",
            ];
            $validate = Validator::make($request->all(),$rules);
            if($validate->fails()){
                return validationError($validate);
            }
            $request->merge([
                "is_verify"=>true,
                "verify_by"=>Auth()->user()->id,
            ]);
            DB::beginTransaction();
            $this->_M_GarbageEntry->edit($request);
            DB::commit();
            return responseMsgs(true,"Garbage Accepted","");
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function deleteWIPGarbage(Request $request){
        try{
            $garbage  = $this->_M_GarbageEntry->find($request->id);
            if($garbage->is_verify){
                throw new MyException("This Garbage is verified. So can't delete it.");
            }
            $order = $this->_M_OrderPunchDetail->where("id",$garbage->order_id)->first();

            $garbage->wip_disbursed = null;
            $garbage->wip_disbursed_in_kg = null;

            $order->wip_disbursed_units = null;
            $order->wip_disbursed_by = null;
            $order->wip_disbursed_date = null;
            $order->is_wip_disbursed = false;
            $order->wip_disbursed_pieces = null;

            DB::beginTransaction();

            $order->update();
            $garbage->update();

            DB::commit();
            return responseMsg(true,"Wip Garbage Deleted","");
            
        }catch(MyException $e){
            return responseMsg(false,$e->getMessage(),"");
        }catch(Exception $e){
            return responseMsg(false,"Server Error!!","");
        }
    }
}
