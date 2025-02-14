<?php

namespace App\Http\Controllers;

use App\Imports\OrderImport;
use App\Models\ClientDetailMaster;
use App\Models\FareDetail;
use App\Models\GradeMaster;
use App\Models\OrderPunchDetail;
use App\Models\RateTypeMaster;
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
    function __construct()
    {
        $this->_M_OrderPunchDetail = new OrderPunchDetail();
        $this->_M_ClientDetail = new ClientDetailMaster();
        $this->_M_GradeMaster = new GradeMaster();
        $this->_M_FareDetail = new FareDetail();
        $this->_M_StereoDetail = new StereoDetail();
        $this->_M_RateTypeMaster = new RateTypeMaster();
    }

    public function importOrders(Request $request){
        if($request->post()){
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
                    'estimate_delivery_date' => 'required|date',
                    'bag_type' => 'required|in:D,U,L,B',
                    'bag_quality' => 'nullable|in:NW,BOPP',
                    "bag_gsm"=>"required|int",
                    "units"=>"required|in:Kg,Piece",
                    "total_units"=>"required|numeric",
                    "rate_per_unit"=>"required|numeric",
                    "bag_w"=>"required|int",
                    "bag_l"=>"required|int",
                    "bag_g"=>"required_if:bag_type,B",
                    "rate_type" => "required|exists:".$this->_M_RateTypeMaster->getTable().",rate_type",
                    'fare_type' => "required|exists:".$this->_M_FareDetail->getTable().",fare_type",
                    'stereo_type' => "required|exists:".$this->_M_StereoDetail->getTable().",stereo_type",                    
                    "bag_printing_color"=>"nullable",
                    "agent_name"=>"nullable",
                    "is_delivered"=>"required|bool",
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
                    $validationErrors[$index] = ["Order no is repeated ".sizeof($val)." time"];
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
}
