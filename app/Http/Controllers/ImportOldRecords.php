<?php

namespace App\Http\Controllers;

use App\Imports\OrderImport;
use App\Imports\OrderRollMapImport;
use App\Models\ClientDetailMaster;
use App\Models\FareDetail;
use App\Models\GradeMaster;
use App\Models\OrderBroker;
use App\Models\OrderPunchDetail;
use App\Models\RateTypeMaster;
use App\Models\RollDetail;
use App\Models\RollTransit;
use App\Models\StereoDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;

class ImportOldRecords extends Controller
{
    //
    protected $_M_OrderPunchDetail;
    protected $_M_ClientDetail;
    protected $_M_GradeMaster;
    protected $_M_FareDetail;
    protected $_M_StereoDetail;
    protected $_M_RateTypeMaster;
    protected $_M_RollDetail;
    protected $_M_RollTransit;
    protected $_M_Broker;
    function __construct()
    {
        $this->_M_OrderPunchDetail = new OrderPunchDetail();
        $this->_M_ClientDetail = new ClientDetailMaster();
        $this->_M_GradeMaster = new GradeMaster();
        $this->_M_FareDetail = new FareDetail();
        $this->_M_StereoDetail = new StereoDetail();
        $this->_M_RateTypeMaster = new RateTypeMaster();
        $this->_M_RollDetail = new RollDetail();
        $this->_M_RollTransit = new RollTransit();
        $this->_M_Broker = new OrderBroker();
    }

    public function importOrders(Request $request){
        if($request->post()){
            ini_set('max_execution_time', 600);
            $validate = Validator::make($request->all(),["csvFile"=>"required|mimes:csv,xlsx"]);
            if($validate->fails()){
                return validationError($validate);
            }
            $file = $request->file('csvFile');
            $headings = (new HeadingRowImport())->toArray($file)[0][0];
            $expectedHeadings = Config::get("customConfig.orderImportCsvHeader");
            if (array_diff($expectedHeadings, $headings)) {
                return responseMsgs(false,"data in invalid Formate","");;
            }
            $rows = Excel::toArray([], $file);

            // Validate rows
            $validationErrors = [];
            foreach ($rows[0] as $index => $row) {
                // Skip the header row
                if ($index == 0) continue;
                // Validate each row
                $rowData = array_combine($headings, $row);
                if(strtolower($file->getClientOriginalExtension())=="xlsx")
                {
                    $rowData["order_date"] = is_int($rowData["order_date"])? getDateColumnAttribute($rowData['order_date']) : $rowData['order_date'];
                    $rowData["estimate_delivery_date"] = is_int($rowData["estimate_delivery_date"])? getDateColumnAttribute($rowData['estimate_delivery_date']) : $rowData['estimate_delivery_date'];
                }
                $validator = Validator::make($rowData, [
                    "order_no"=>"required",
                    'client_name' => "required",                   
                    'order_date' => 'required|date',
                    'estimate_delivery_date' => 'nullable|date',
                    'bag_type' => 'required|in:D,U,L,B,LBB',
                    'bag_quality' => 'nullable|in:NW,BOPP,LAM',
                    "bag_gsm"=>[
                        "required",
                        function($attribute, $value, $fail) use ($rowData) {
                            if (isset($rowData['bag_quality']) && $rowData['bag_quality'] === 'BOPP') {
                                if (!preg_match('/^\d+\+\d+\+\d+$/', $value)) {
                                    $fail("The $attribute format must be '35+13+12' (three numbers separated by '+').");
                                }
                            }
                            if (isset($rowData['bag_quality']) && $rowData['bag_quality'] === 'LAM') {
                                if (!preg_match('/^\d+\+\d+$/', $value)) {
                                    $fail("The $attribute format must be '35+13' (three numbers separated by '+').");
                                }
                            }
                            if ($rowData['bag_quality'] === 'NW') {
                                if (!(is_numeric($value) && floor($value) == $value)) {
                                    $fail("The $attribute must be an integer.");
                                }
                            }
            
                        }
                    ],
                    "units"=>"required|in:Kg,Piece",
                    "total_units"=>"nullable|numeric",
                    "booked_units"=>"nullable|numeric",
                    "rate_per_unit"=>"required|numeric",
                    "bag_w"=>"required|numeric",
                    "bag_l"=>"required|numeric",
                    "bag_g"=>"required_if:bag_type,B|numeric",
                    "rate_type" => "nullable|exists:".$this->_M_RateTypeMaster->getTable().",rate_type",
                    'fare_type' => "nullable|exists:".$this->_M_FareDetail->getTable().",fare_type",
                    'stereo_type' => "nullable|exists:".$this->_M_StereoDetail->getTable().",stereo_type",                    
                    "bag_printing_color"=>"nullable",
                    "agent_name"=>"nullable|exists:".$this->_M_Broker->getTable().",broker_name",
                    "is_delivered"=>"nullable|bool",
                ]);

                if ($validator->fails()) {
                    $validationErrors[$index] = $validator->errors()->all();
                }
                $dataWithHeadings[] = $rowData; 
            }

            $group = collect($dataWithHeadings)->groupBy("order_no")->filter(function($val){
                return $val->count()>1;
            });
            
            if($group->count()>0){
                foreach($group as $index=>$val){
                    $validationErrors[] = ["Order no $index is repeated ".sizeof($val)." time"];
                }
            }
            if (!empty($validationErrors)) {
                return responseMsgs(false,"Validation Error",$validationErrors);
            }   
            $clineName = collect($dataWithHeadings)->pluck("client_name")->unique();     
            
            // Import the CSV file using the RollDetailsImport class
            DB::beginTransaction();
            foreach($clineName as $val){
                if(!$this->_M_ClientDetail->where("client_name",$val)->first()){
                    $clientRequest = new Request(["client_name"=>$val]);
                    $this->_M_ClientDetail->store($clientRequest);
                }
            }            
            Excel::import(new OrderImport, $file);
            DB::commit();
            return responseMsgs(true,"data import","");
        }
        return view("import/order");
    }

    public function orderRollMap(Request $request){
        if($request->post()){
            $validate = Validator::make($request->all(),["csvFile"=>"required|mimes:csv,xlsx"]);
            if($validate->fails()){
                return validationError($validate);
            }
            $file = $request->file('csvFile');
            $headings = (new HeadingRowImport())->toArray($file)[0][0];
            $expectedHeadings = Config::get("customConfig.orderRollMapCsvHeader");
            if (array_diff($expectedHeadings, $headings)) {
                return responseMsgs(false,"data in invalid Formate","");;
            }
            $rows = Excel::toArray([], $file);

            // Validate rows
            $validationErrors = [];
            foreach ($rows[0] as $index => $row) {
                // Skip the header row
                if ($index == 0) continue;
                // Validate each row
                $rowData = array_combine($headings, $row);                
                $validator = Validator::make($rowData, [
                    "order_no"=>"required|exists:".$this->_M_OrderPunchDetail->getTable().",order_no,is_delivered,false",
                    'roll_no' => [
                        "required",
                        function($attribute, $value, $fail)use ($rowData,$index ){
                            $rollExists = $this->_M_RollDetail
                                ->where("roll_no", $value)
                                ->whereNull("client_detail_id")
                                ->exists();

                            $transitExists = $this->_M_RollTransit
                                ->where("roll_no", $value)
                                ->whereNull("client_detail_id")
                                ->exists();

                            if (!$rollExists && !$transitExists) {
                                $fail("The $attribute ($value) is invalid.");
                            }
                        },
                    ],
                ]);
                if ($validator->fails()) {           
                    $validationErrors[$index] = $validator->errors()->all();
                }
                $dataWithHeadings[] = $rowData; 
            }

            $group = collect($dataWithHeadings)->groupBy("roll_no")->filter(function($val){
                return $val->count()>1;
            });
            
            if($group->count()>0){
                foreach($group as $index=>$val){
                    $validationErrors[] = ["Roll No $index is repeated ".sizeof($val)." time"];
                }
            }
            if (!empty($validationErrors)) {
                return responseMsgs(false,"Validation Error",$validationErrors);
            }  
            
            // Import the CSV file using the RollDetailsImport class
            DB::beginTransaction();            
            Excel::import(new OrderRollMapImport, $file);
            DB::commit();
            return responseMsgs(true,"data import","");
        }
        return view("import/orderRollMap");
    }
}
