<?php

namespace App\Http\Controllers;

use App\Models\AutoDetail;
use App\Models\TransporterDetail;
use Exception;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class TransporterController extends Controller
{
    //

    protected $_M_AutoDetails;
    protected $_M_TransporterDetails;

    function __construct()
    {
        $this->_M_AutoDetails = new AutoDetail();
        $this->_M_TransporterDetails = new TransporterDetail();
    }

    public function autoList(Request $request){
        if($request->ajax()){
            $data = $this->_M_AutoDetails->where("lock_status",false)->orderBy("id","ASC");
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($val) {
                    return <<<EOD
                            <i class="bi bi-pencil-square btn btn-sm" style ="color: #0d6efd" onClick="openModelEdit('$val->id')" ></i>
                            <i class="bi bi-trash3-fill btn btn-sm" style ="color:rgb(229, 37, 37)" onclick="showConfirmDialog('Are you sure you want to deactivate this item?', function() { deactivate('$val->id'); })" ></i>
                        EOD;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view("Transporter/auto_list");
    }

    public function addAuto(Request $request){
        try{
            $message = "New Auto Add";
            if($request->id){
                $this->_M_AutoDetails->edit($request);
                $message = "Auto Update";
            }else{
                $this->_M_AutoDetails->store($request);
            }
            return responseMsgs(true,$message,"");
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function autoDtl($id){
        try{
            $data = $this->_M_AutoDetails->find($id);
            return responseMsgs(true,"Data Fetched",$data);
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function deactivateAuto($id,Request $request){
        try{
            $request->merge(["id",$id]);
            $this->_M_AutoDetails->edit($request);
            return responseMsgs(true,"Auto Deactivated","");
        }catch(Exception $e){
            return responseMsgs(true,$e->getMessage(),"");
        }
    }



    public function transporterList(Request $request){
        if($request->ajax()){
            $data = $this->_M_TransporterDetails->where("lock_status",false)->orderBy("id","ASC");
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($val) {
                    return <<<EOD
                            <i class="bi bi-pencil-square btn btn-sm" style ="color: #0d6efd" onClick="openModelEdit('$val->id')" ></i>
                            <i class="bi bi-trash3-fill btn btn-sm" style ="color:rgb(229, 37, 37)" onclick="showConfirmDialog('Are you sure you want to deactivate this item?', function() { deactivate('$val->id'); })" ></i>
                        EOD;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view("Transporter/transporter_list");
    }

    public function addTransporter(Request $request){
        try{
            $message = "New Transporter Add";
            if($request->id){
                $this->_M_TransporterDetails->edit($request);
                $message = "Transporter Update";
            }else{
                $this->_M_TransporterDetails->store($request);
            }
            return responseMsgs(true,$message,"");
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function transporterDtl($id){
        try{
            $data = $this->_M_TransporterDetails->find($id);
            return responseMsgs(true,"Data Fetched",$data);
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function deactivateTransporter($id,Request $request){
        try{
            $request->merge(["id",$id]);
            $this->_M_TransporterDetails->edit($request);
            return responseMsgs(true,"Transporter Deactivated","");
        }catch(Exception $e){
            return responseMsgs(true,$e->getMessage(),"");
        }
    }
}
