<?php

namespace App\Http\Controllers;

use App\Models\FareDetail;
use App\Models\GradeMaster;
use App\Models\RollQualityGradeMap;
use App\Models\RollQualityMaster;
use App\Models\StereoDetail;
use Exception;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class MasterController extends Controller
{
    //
    protected $_M_FareDetail;
    protected $_M_StereoDetail;
    protected $_M_GradeMaster;
    protected $_M_RollQualityMaster;
    protected $_M_RollQualityGradeMap;

    function __construct()
    {
        $this->_M_FareDetail = new FareDetail();
        $this->_M_StereoDetail = new StereoDetail();
        $this->_M_GradeMaster = new GradeMaster();
        $this->_M_RollQualityMaster = new RollQualityMaster();
        $this->_M_RollQualityGradeMap = new RollQualityGradeMap();
    }


    public function fareList(Request $request){
        if($request->ajax()){
            $data = $this->_M_FareDetail->where("lock_status",false)->orderBy("id","ASC");
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($val) {
                    return '<button class="btn btn-sm btn-primary" onClick="openModelEdit('.$val->id.')" >Edit</button>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view("Master/fare_list");
    }

    public function addFare(Request $request){
        try{
            $message = "New Fare Add";
            if($request->id){
                $this->_M_FareDetail->edit($request);
                $message = "Fare Update";
            }else{
                $this->_M_FareDetail->store($request);
            }
            return responseMsgs(true,$message,"");
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function fareDtl($id){
        try{
            $data = $this->_M_FareDetail->find($id);
            return responseMsgs(true,"Data Fetched",$data);
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    /**
     * Stereo
     */
    public function stereoList(Request $request){
        if($request->ajax()){
            $data = $this->_M_StereoDetail->where("lock_status",false)->orderBy("id","ASC");
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($val) {
                    return '<button class="btn btn-sm btn-primary" onClick="openModelEdit('.$val->id.')" >Edit</button>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view("Master/stereo_list");
    }

    public function addStereo(Request $request){
        try{
            $message = "New Stereo Add";
            if($request->id){
                $this->_M_StereoDetail->edit($request);
                $message = "Stereo Update";
            }else{
                $this->_M_StereoDetail->store($request);
            }
            return responseMsgs(true,$message,"");
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function stereoDtl($id){
        try{
            $data = $this->_M_StereoDetail->find($id);
            return responseMsgs(true,"Data Fetched",$data);
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    /**
     * Grade
     */
    public function gradeList(Request $request){
        if($request->ajax()){
            $data = $this->_M_GradeMaster->where("lock_status",false)->orderBy("id","ASC");
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($val) {
                    return '<button class="btn btn-sm btn-primary" onClick="openModelEdit('.$val->id.')" >Edit</button>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view("Master/grade_list");
    }

    public function addGrade(Request $request){
        try{
            $message = "New Grade Add";
            if($request->id){
                $this->_M_GradeMaster->edit($request);
                $message = "Grade Update";
            }else{
                $this->_M_GradeMaster->store($request);
            }
            return responseMsgs(true,$message,"");
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function gradeDtl($id){
        try{
            $data = $this->_M_GradeMaster->find($id);
            return responseMsgs(true,"Data Fetched",$data);
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }
}
