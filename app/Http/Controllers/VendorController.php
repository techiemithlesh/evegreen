<?php

namespace App\Http\Controllers;

use App\Models\VendorDetailMaster;
use Exception;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class VendorController extends Controller
{
    //
    private $_M_VendorDetail;
    function __construct()
    {
        $this->_M_VendorDetail = new VendorDetailMaster();
    }

    public function addVendor(Request $request){
        try{
            if($request->id){
                $this->_M_VendorDetail->edit($request);
            }else{
                $this->_M_VendorDetail->store($request);
            }
            return responseMsgs(true,"New Vendor Add","");
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function vendorList(Request $request){
        try{
            if($request->ajax()){
                $data = $this->_M_VendorDetail->where("lock_status",false);
                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function ($val) {
                        return '<button class="btn btn-sm btn-primary" onClick="openModelEdit('.$val->id.')" >Edit</button>
                               <button class="btn btn-sm btn-info" onClick="deactivateMenu('.$val->id.')" >Delete</button>';
                    })->rawColumns(['menu_icon', 'action'])
                    ->make(true);
            }
            return view("vendor/list");
        }catch(Exception $e){
            flashToast("message","Internal Server Error");
            return redirect()->back();
        }
    }
    
    public function getVenderDtl($id,Request $request){
        try{
            $data = $this->_M_VendorDetail->find($id);
            return responseMsgs(true,"Data Fetched",$data);
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function deactivate($id,Request $request){
        try{
            $request->merge(["id",$id]);
            $this->_M_VendorDetail->edit($request);
            // dd(DB::getQueryLog());
            return responseMsgs(true,"Menu Deactivated","");
        }catch(Exception $e){
            return responseMsgs(true,$e->getMessage(),"");
        }
    }
}
