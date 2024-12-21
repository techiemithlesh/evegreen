<?php

namespace App\Http\Controllers;

use App\Models\ColorMaster;
use App\Models\RollColorMaster;
use Exception;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ColorController extends Controller
{
    //

    private $_M_RollColor;
    private $_M_Color;
    function __construct()
    {
        $this->_M_RollColor = new RollColorMaster();
        $this->_M_Color = new ColorMaster();
    }

    public function rollColorList(Request $request){
        if($request->ajax()){
            $data = $this->_M_RollColor->where("lock_status",false);
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($val) {
                    return '<button class="btn btn-sm btn-primary" onClick="openModelEdit('.$val->id.')" >Edit</button>';
                })
                ->addColumn('html', function ($val) {
                    return '<div class="btn btn-sm color-box" style="background-color:'.$val->color.'"></div>';
                })
                ->rawColumns(['action','html'])
                ->make(true);
        }
        return view("Color/list");
    }

    public function addRollColor(Request $request){
        try{
            if($request->id){
                $this->_M_RollColor->edit($request);
            }else{
                $this->_M_RollColor->store($request);
            }
            return responseMsgs(true,"New Color Add","");
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function rollColorDtl($id){
        try{
            $data = $this->_M_RollColor->find($id);
            return responseMsgs(true,"New Color Add",$data);
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function colorList(Request $request){
        if($request->ajax()){
            $data = $this->_M_Color->where("lock_status",false);
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($val) {
                    return '<button class="btn btn-sm btn-primary" onClick="openModelEdit('.$val->id.')" >Edit</button>';
                })
                ->addColumn('html', function ($val) {
                    return '<div class="btn btn-sm color-box" style="background-color:'.$val->color.'"></div>';
                })
                ->rawColumns(['action','html'])
                ->make(true);
        }
        return view("Color/client_color");
    }

    public function addColor(Request $request){
        try{
            if($request->id){
                $this->_M_Color->edit($request);
            }else{
                $this->_M_Color->store($request);
            }
            return responseMsgs(true,"New Color Add","");
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function colorDtl($id){
        try{
            $data = $this->_M_Color->find($id);
            return responseMsgs(true,"New Color Add",$data);
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }
}
