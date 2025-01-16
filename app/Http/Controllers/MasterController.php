<?php

namespace App\Http\Controllers;

use App\Models\FareDetail;
use App\Models\GradeMaster;
use App\Models\RateTypeMaster;
use App\Models\RollQualityGradeMap;
use App\Models\RollQualityMaster;
use App\Models\StereoDetail;
use App\Models\UserTypeMaster;
use App\Models\VendorDetailMaster;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class MasterController extends Controller
{
    //
    protected $_M_FareDetail;
    protected $_M_StereoDetail;
    protected $_M_GradeMaster;
    protected $_M_RollQualityMaster;
    protected $_M_RollQualityGradeMap;
    protected $_M_VendorDetail;
    protected $_M_RateType;
    protected $_M_UserTypeMaster;

    function __construct()
    {
        $this->_M_FareDetail = new FareDetail();
        $this->_M_StereoDetail = new StereoDetail();
        $this->_M_GradeMaster = new GradeMaster();
        $this->_M_RollQualityMaster = new RollQualityMaster();
        $this->_M_RollQualityGradeMap = new RollQualityGradeMap();
        $this->_M_VendorDetail = new VendorDetailMaster();
        $this->_M_RateType = new RateTypeMaster();
        $this->_M_UserTypeMaster = new UserTypeMaster();
    }


    public function fareList(Request $request){
        if($request->ajax()){
            $data = $this->_M_FareDetail->where("lock_status",false)->orderBy("id","ASC");
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

    public function deactivateFare($id,Request $request){
        try{
            $request->merge(["id",$id]);
            $this->_M_FareDetail->edit($request);
            return responseMsgs(true,"Fare Deactivated","");
        }catch(Exception $e){
            return responseMsgs(true,$e->getMessage(),"");
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
                    return <<<EOD
                            <i class="bi bi-pencil-square btn btn-sm" style ="color: #0d6efd" onClick="openModelEdit('$val->id')" ></i>
                            <i class="bi bi-trash3-fill btn btn-sm" style ="color:rgb(229, 37, 37)" onclick="showConfirmDialog('Are you sure you want to deactivate this item?', function() { deactivate('$val->id'); })" ></i>
                          EOD;
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

    public function deactivateStereo($id,Request $request){
        try{
            $request->merge(["id",$id]);
            $this->_M_StereoDetail->edit($request);
            return responseMsgs(true,"Stereo Deactivated","");
        }catch(Exception $e){
            return responseMsgs(true,$e->getMessage(),"");
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
                    return <<<EOD
                            <i class="bi bi-pencil-square btn btn-sm" style ="color: #0d6efd" onClick="openModelEdit('$val->id')" ></i>
                            <i class="bi bi-trash3-fill btn btn-sm" style ="color:rgb(229, 37, 37)" onclick="showConfirmDialog('Are you sure you want to deactivate this item?', function() { deactivate('$val->id'); })" ></i>
                          EOD;
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

    public function deactivateGrade($id,Request $request){
        try{
            $request->merge(["id",$id]);
            $this->_M_GradeMaster->edit($request);
            return responseMsgs(true,"Grade Deactivated","");
        }catch(Exception $e){
            return responseMsgs(true,$e->getMessage(),"");
        }
    }

    /**
     * Roll Quality
     */
    public function rollQualityList(Request $request){
        if($request->ajax()){
            $data = $this->_M_VendorDetail->where("lock_status",false)->orderBy("id","ASC");
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn("roll_quality",function($val){
                    return ($val->rollQualityList()->get())->pluck('quality')->implode(",","quality");
                })
                ->addColumn('action', function ($val) {
                    return '<i class="bi bi-pencil-square btn btn-sm" style ="color: #0d6efd" onClick="openModelEdit('.$val->id.')" ></i>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view("Master/roll_quality_list");
    }

    public function addRollQuality(Request $request){
        try{
            $rule = [
                "id"=>"required|exists:".$this->_M_VendorDetail->getTable().",id",
                "quality"=>"required|array",
                "quality.*.quality"=>"required"
            ];
            $Validator = Validator::make($request->all(),$rule);
            if($Validator->fails()){
                return validationError($Validator);
            }
            $request->merge(["vendorId"=>$request->id]);
            $message = "New Grade Add"; 
            $this->_M_RollQualityMaster->where("vendor_id",$request->vendorId)->update(["lock_status"=>true]);
            foreach($request->quality as $val){
                $newRequest = new Request($request->all());
                $newRequest->merge($val);
                $qualityId = $this->_M_RollQualityMaster->store($newRequest);
            }           
            return responseMsgs(true,$message,"");
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function rollQualityDtl($id){
        try{
            $data = $this->_M_VendorDetail->find($id);
            $quality = $data->rollQualityList()->get();
            $data->roll_quality = $quality;
            return responseMsgs(true,"Data Fetched",$data);
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function gradeListMap(Request $request){
        try{
            $data = $this->_M_GradeMaster->where("lock_status",false)->orderBy("grade","ASC")->get();         
            return responseMsgs(true,"Data Fetched",$data);
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function rollVenderListMap($venderId){
        try{
            $data = $this->_M_RollQualityMaster->where("vendor_id",$venderId)->where("lock_status",false)->orderBy("quality","ASC")->get();         
            return responseMsgs(true,"Data Fetched",$data);
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    /**
     * rate type
     */

    public function rateTypeList(Request $request){
        if($request->ajax()){
            $data = $this->_M_RateType->where("lock_status",false)->orderBy("id","ASC");
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
        return view("Master/rate_type_list");
    }

    public function addRateType(Request $request){
        try{
           
            $message = "New Rate Type"; 
            if($request->id){
                $message = "Rate Type Update"; 
                $this->_M_RateType->edit($request);
            }else{
                $this->_M_RateType->store($request);
            }       
            return responseMsgs(true,$message,"");
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function rateTypeDtl($id){
        try{
            $data = $this->_M_RateType->find($id);
            return responseMsgs(true,"Data Fetched",$data);
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    
    public function deactivateRateType($id,Request $request){
        try{
            $request->merge(["id",$id]);
            $this->_M_RateType->edit($request);
            return responseMsgs(true,"Rate Type Deactivated","");
        }catch(Exception $e){
            return responseMsgs(true,$e->getMessage(),"");
        }
    }

    /**
     * Grade
     */
    public function userTypeList(Request $request){
        if($request->ajax()){
            $data = $this->_M_UserTypeMaster->where("lock_status",false)->orderBy("id","ASC");
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($val) {
                    return '<button class="btn btn-sm btn-primary" onClick="openModelEdit('.$val->id.')" >Edit</button>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view("Master/user_type_list");
    }

    public function addUserType(Request $request){
        try{
            $message = "New User Type Add";
            if($request->id){
                $this->_M_UserTypeMaster->edit($request);
                $message = "User Type Update";
            }else{
                $this->_M_UserTypeMaster->store($request);
            }
            return responseMsgs(true,$message,"");
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function userTypeDtl($id){
        try{
            $data = $this->_M_UserTypeMaster->find($id);
            return responseMsgs(true,"Data Fetched",$data);
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }
}
