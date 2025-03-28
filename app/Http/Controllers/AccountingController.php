<?php

namespace App\Http\Controllers;

use App\Models\GarbageEntry;
use App\Models\MachineMater;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class AccountingController extends Controller
{
    //

    protected $_M_GarbageEntry;
    protected $_M_User;
    protected $_M_Machine;

    function __construct()
    {
        $this->_M_GarbageEntry = new GarbageEntry();
        $this->_M_User = new User();
        $this->_M_Machine = new MachineMater();
    }

    public function garbageVerification(Request $request){
        if($request->ajax()){
            $data = $this->_M_GarbageEntry->select("garbage_entries.*","c.client_name",
                        DB::raw("((garbage/ (CASE WHEN roll_weight=0 THEN 1 ELSE roll_weight END))*100) AS garbage_per")
                    )
                    ->join("client_detail_masters as c","c.id","garbage_entries.client_id")
                    ->where("garbage_entries.lock_status",false)
                    ->where("garbage_entries.is_verify",false)
                    ->orderBY(DB::raw("(garbage/ (CASE WHEN roll_weight=0 THEN 1 ELSE roll_weight END))*100"),"DESC");
            $user = $this->_M_User->all();
            $machine = $this->_M_Machine->all();
            $list = DataTables::of($data)
                ->addIndexColumn()                
                ->addColumn('operator_name', function ($val) use($user) {  
                    return $user->where("id",$val->operator_id)->first()->name??"";
                })
                ->addColumn('helper_name', function ($val) use($user) {  
                    return $user->where("id",$val->helper_id)->first()->name??"";
                })
                ->addColumn("cutting_date",function($val){
                    return $val->cutting_date ? Carbon::parse($val->cutting_date)->format("d-m-Y"):"";
                })
                ->addColumn("percent",function($val){
                    return roundFigure($val->roll_weight ? ($val->garbage/$val->roll_weight)*100 :0)." %";
                })
                ->addColumn("wip_percent",function($val){
                    return roundFigure($val->roll_weight ? ($val->wip_disbursed_in_kg/$val->roll_weight)*100 :0)." %";
                })
                ->addColumn("total_garbage",function($val){
                    return roundFigure($val->garbage +  $val->wip_disbursed_in_kg);
                })
                ->addColumn("machine",function($val) use($machine){
                    return $machine->where("id",$val->machine_id)->first()->name??"";                       
                })
                ->addColumn('action', function ($val){                    
                    $button = "";
                    $totalGarbege = ($val->roll_weight ? ($val->garbage/$val->roll_weight)*100 :0) + roundFigure($val->roll_weight ? ($val->wip_disbursed_in_kg/$val->roll_weight)*100 :0);
                    if((!is_between($totalGarbege,-2,2)) && $val->wip_disbursed_in_kg){
                        $button .= '<button class="btn btn-sm btn-warning" onClick="openModel('.$val->id.')" >Update</button>';
                    }                    
                    return $button;
                })
                ->rawColumns(['row_color', 'action'])
                ->make(true);
            return $list;

        }
        return view("Accounting/garbageVerification");
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
}
