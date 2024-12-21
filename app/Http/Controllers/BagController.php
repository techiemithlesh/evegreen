<?php

namespace App\Http\Controllers;

use App\Models\BagTypeMaster;
use Exception;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class BagController extends Controller
{
    //

    private $_M_BagType;
    function __construct()
    {
        $this->_M_BagType = new BagTypeMaster();
    }


    public function bagList(Request $request){
        if($request->ajax()){
            $data = $this->_M_BagType->where("lock_status",false);
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($val) {
                    return '<button class="btn btn-sm btn-primary" onClick="openModelEdit('.$val->id.')" >Edit</button>';
                })->rawColumns(['action'])
                ->make(true);
        }
        return view("Bag/list");
    }

    public function addBag(Request $request){
        try{
            if($request->id){
                $this->_M_BagType->edit($request);
            }else{
                $this->_M_BagType->store($request);
            }
            return responseMsgs(true,"New Client Add","");
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function getBagDtl($id,Request $request){
        try{
            $data = $this->_M_BagType->find($id);
            return responseMsgs(true,"Data Fetched",$data);
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }
}
