<?php

namespace App\Http\Controllers;

use App\Models\BagPacking;
use App\Models\BagTypeMaster;
use App\Models\ClientDetailMaster;
use App\Models\PackTransport;
use App\Models\RollDetail;
use App\Models\TransportDetail;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class PackingController extends Controller
{
    private $_M_RollDetail;
    private $_M_ClientDetails;
    private $_M_BagType;

    private $_M_BagPacking;
    private $_M_PackTransport;
    private $_M_TransportDetail;

    function __construct()
    {
        $this->_M_RollDetail = new RollDetail();
        $this->_M_ClientDetails = new ClientDetailMaster();
        $this->_M_BagType = new BagTypeMaster();
        $this->_M_BagPacking = new BagPacking();
        $this->_M_PackTransport  = new PackTransport();
        $this->_M_TransportDetail = new TransportDetail();
    }

    public function packingEnter(Request $request){
        $roll = $this->_M_RollDetail
                    ->where("is_cut",true)
                    ->where("lock_status",false)
                    ->where("is_delivered",false)
                    ->get();
        $clientId = $roll->unique("client_detail_id")->pluck("client_detail_id");
        $data["clientList"] = $this->_M_ClientDetails->whereIn("id",$clientId)->get();
        $bagId =  $roll->unique("bag_type_id")->pluck("bag_type_id");
        $data["bagList"] = $this->_M_BagType->whereIn("id",$bagId)->get();
        return view("Packing/entry",$data);
    }

    public function searchRoll(Request $request){
        try{
            $roll=$this->_M_RollDetail->select("roll_details.*","bag_type_masters.bag_type")
                    ->join("bag_type_masters","bag_type_masters.id","roll_details.bag_type_id")
                    ->where("roll_details.lock_status",false)
                    ->where("roll_details.is_cut",true)
                    ->where("roll_details.is_delivered",false)
                    ->where("roll_details.client_detail_id",$request->clientId);
            if($request->l){
                $roll->where("roll_details.l",$request->l);
            }
            if($request->w){
                $roll->where("roll_details.w",$request->w);
            }
            if($request->bag_type_id){
                $roll->where("roll_details.bag_type_id",$request->bag_type_id);
            }
            $roll = $roll->get();
            return responseMsgs(true,"data Fetch",$roll);
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function packingEnterAdd(Request $request){        
        try{
            $rules = [
                "roll"=>"required|array",
                "roll.*.id"=>"required|exists:".$this->_M_RollDetail->getTable().",id",
                "roll.*.weight"=>"required",
                "roll.*.pieces"=> [                    
                    function ($attribute, $value, $fail) use($request){
                        $key = explode(".",$attribute)[1];
                        $roll = $this->_M_RollDetail->find($request->roll[$key]["id"]);
                        if($roll && $roll->bag_unit=="Pice" && (!$value))
                        {
                            $fail('The '.$attribute.' id required');
                        }
    
                    },
                ],
            ];
            
            $validate = Validator::make($request->all(),$rules);
            if($validate->fails()){
                return validationError($validate);
            }
            $user = Auth()->user();
            DB::beginTransaction();
            foreach($request->roll as $val){
                $newRequest = new Request();
                $newRequest->merge([
                    "packing_weight"=>$val["weight"],
                    "packing_bag_pieces"=>$val["pieces"],
                    "roll_id"=>$val["id"],
                    "user_id"=>$user->id,
                ]);

                $this->_M_BagPacking->store($newRequest);
            }
            DB::commit();
            return responseMsgs(true,"Bag Entry Successful","");
            
        }catch(Exception $e){
            DB::rollBack();
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function bagStock(Request $request){
        $user = Auth()->user();
        $user_type = $user->user_type;
        if($request->ajax()){
            $data = $this->_M_BagPacking->select("roll_details.*","bag_packings.*",
                        "bag_type_masters.bag_type","client_detail_masters.client_name"
                    )
                    ->join("roll_details","roll_details.id","bag_packings.roll_id")
                    ->join("client_detail_masters","client_detail_masters.id","roll_details.client_detail_id")
                    ->join("bag_type_masters","bag_type_masters.id","roll_details.bag_type_id")
                    ->where("bag_packings.packing_status",1)
                    ->where("bag_packings.lock_status",false);
            $list = DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('packing_date', function ($val) { 
                    return $val->packing_date ? Carbon::parse($val->packing_date)->format("d-m-Y") : "";
                })
                ->addColumn('bag_color', function ($val) { 
                    return collect(json_decode($val->printing_color,true))->implode(",") ;
                })
                ->addColumn('action', function ($val) {                    
                    $button = "";                    
                    // $button='<button class="btn btn-sm btn-info" onClick="openCuttingModel('.$val->id.')" >Update Cutting</button>';
                    return $button;
                })
                ->rawColumns(['row_color', 'action'])
                ->make(true);
            return $list;

        }
        return view("Packing/stock");
    }

    public function bagGodown(Request $request){
        $user = Auth()->user();
        $user_type = $user->user_type;
        if($request->ajax()){
            $data = $this->_M_BagPacking->select("roll_details.*","bag_packings.*",
                        "bag_type_masters.bag_type","client_detail_masters.client_name"
                    )
                    ->join("roll_details","roll_details.id","bag_packings.roll_id")
                    ->join("client_detail_masters","client_detail_masters.id","roll_details.client_detail_id")
                    ->join("bag_type_masters","bag_type_masters.id","roll_details.bag_type_id")
                    ->where("bag_packings.packing_status",2)
                    ->where("bag_packings.lock_status",false);
            $list = DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('packing_date', function ($val) { 
                    return $val->packing_date ? Carbon::parse($val->packing_date)->format("d-m-Y") : "";
                })
                ->addColumn('bag_color', function ($val) { 
                    return collect(json_decode($val->printing_color,true))->implode(",") ;
                })
                ->addColumn('action', function ($val) {                    
                    $button = "";                    
                    // $button='<button class="btn btn-sm btn-info" onClick="openCuttingModel('.$val->id.')" >Update Cutting</button>';
                    return $button;
                })
                ->rawColumns(['row_color', 'action'])
                ->make(true);
            return $list;

        }
        return view("Packing/godown");
    }

    public function reivingGodown(Request $request){
        if($request->ajax()){
            $data = $this->_M_PackTransport->select("pack_transports.id","pack_transports.bill_no","pack_transports.invoice_no","pack_transports.transport_date",
                        DB::raw(
                            "
                            COUNT(transport_details.bag_packing_id) AS total_bag,
                            COUNT( CASE WHEN transport_details.is_delivered = FALSE THEN transport_details.bag_packing_id END ) AS total_unverified_bag
                            "
                        )
                    )
                    ->join("transport_details","transport_details.pack_transport_id","pack_transports.id")
                    ->where("pack_transports.transport_status",3)
                    ->where("transport_details.lock_status",false)
                    ->where("pack_transports.is_fully_reviewed",false)
                    ->where("pack_transports.lock_status",false)
                    ->groupBy("pack_transports.id","pack_transports.bill_no","pack_transports.invoice_no","pack_transports.transport_date");
                
            $list = DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('transport_date', function ($val) { 
                    return $val->transport_date ? Carbon::parse($val->transport_date)->format("d-m-Y") : "";
                })
                ->addColumn('action', function ($val) {                 
                    return '<button class="btn btn-sm btn-info" onClick="openReceivingModel('.$val->id.')" >Verify</button>';
                })
                ->rawColumns(['row_color', 'action'])
                ->make(true);
            return $list;
        }
        return view("Packing/reivingGodown");
    }

    public function reivingTransport(Request $request){
        try{
            $data = $this->_M_TransportDetail->select("bag_packings.*","transport_details.*")
                    ->join("bag_packings","bag_packings.id","transport_details.bag_packing_id")
                    ->where("transport_details.pack_transport_id",$request->id)
                    ->where("transport_details.lock_status",false)
                    ->where("transport_details.is_delivered",false)
                    ->get();
            return responseMsgs(true,"Data Fetched",$data);
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function addInGodown(Request $request){
        try{
            $validate = Validator::make($request->all(),[
                "id"=>"required|exists:".$this->_M_TransportDetail->getTable().",id,is_delivered,false",
            ]);
            if($validate->fails()){
                return validationError($validate);
            }
            $currentDate = Carbon::now()->format("Y-m-d");
            $user = Auth()->user();

            $transportDtl = $this->_M_TransportDetail->find($request->id);
            $bagPackage = $this->_M_BagPacking->find($transportDtl->bag_packing_id);
            $transport = $this->_M_PackTransport->find($transportDtl->pack_transport_id);

            $transportDtl->is_delivered = true;
            $transportDtl->reiving_user_id = $user->id;
            $transportDtl->reiving_date = $currentDate;

            $bagPackage->packing_status = 2;
            
            DB::beginTransaction();
            $transportDtl->update();
            $bagPackage->update();
            $test = $this->_M_TransportDetail->where("is_delivered",false)->where("pack_transport_id",$transportDtl->pack_transport_id)->count("id");
            if($test==0){
                $transport->is_fully_reviewed = true;
                $transport->reiving_date = $currentDate;
                $transport->reiving_user_id = $user->id;
            }
            $transport->update();
            DB::commit();
            return responseMsgs(true,"Bag Verify","");
        }catch(Exception $e){
            DB::rollBack();
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function addBagInTransport(Request $request){        
        return view("Packing/inTransport");
    }

    public function searchPackingForTransport(Request $request){
        try{
            $packingStatus = [];
            if($request->status=="For Godown"){
                $packingStatus = [1];
            }
            if($request->status=="For Delivery"){
                $packingStatus = [1,2];
            }
            DB::enableQueryLog();
            $data = $this->_M_BagPacking
                    ->select("roll_details.*","bag_packings.*",
                        "bag_type_masters.bag_type","client_detail_masters.client_name"
                    )
                    ->join("roll_details","roll_details.id","bag_packings.roll_id")
                    ->join("client_detail_masters","client_detail_masters.id","roll_details.client_detail_id")
                    ->join("bag_type_masters","bag_type_masters.id","roll_details.bag_type_id")
                    ->where("bag_packings.packing_no",$request->packingNo)
                    ->whereIn("bag_packings.packing_status",$packingStatus)
                    ->whereIn("bag_packings.packing_status",[1,2])
                    ->where("bag_packings.lock_status",false)
                    ->first();
            // dd(DB::getQueryLog());
            if($data){
                $data->printing_color = collect(json_decode($data->printing_color,true))->implode(",");
            }
            return responseMsgs(true,"Data Fetch",$data);
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function transportAdd(Request $request){
        try{
            $user = Auth()->user();
            $rules = [
                "transPortType" => "required|in:For Godown,For Delivery", // Fixed 'id' to 'in' for a set of allowed values
                "dispatchedDate" => "required|date", // Ensures dispatchedDate is a valid date
                "invoiceNo" => "required", // Invoice number is mandatory
                "billNo" => "required_if:transPortType,For Delivery", // Bill number is required only if transport type is 'For Delivery'
                "packing" => "required|array", // Packing must be a non-empty array
                "packing.*.id" => [
                        "required",
                        Rule::exists($this->_M_BagPacking->getTable(), "id")
                            ->whereIn("packing_status", [1, 2]),
                    ], // Ensures the packing IDs exist with specific statuses
            ];
            $validate = Validator::make($request->all(),$rules);
            if($validate->fails()){
                return validationError($validate);
            }
            $transportStatus = Config::get("customConfig.transportType.".$request->transPortType);
            $request->merge(["userId"=>$user->id,"transportStatus"=>$transportStatus]);
            DB::beginTransaction();
            $tranId = $this->_M_PackTransport->store($request);
            foreach($request->packing as $val){
                $packing = $this->_M_BagPacking->find($val["id"]);
                $newRequest = new Request($val);
                $newRequest->merge([
                    "packTransportId"=>$tranId,
                    "bagPackingId"=>$packing->id,
                ]);
                $this->_M_TransportDetail->store($newRequest);
                $packing->packing_status = $transportStatus ;
                $packing->update();
            }
            DB::commit();
            return responseMsgs(true,"Dag is Dispatched","");
        }catch(Exception $e){
            DB::rollBack();
            return responseMsgs(false,$e->getMessage(),"");
        }
    }
}
