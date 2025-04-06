<?php

namespace App\Http\Controllers;

use App\Exports\ExportRoll;
use App\Imports\RollDetailsImport;
use App\Models\BagType;
use App\Models\BagTypeMaster;
use App\Models\ClientDetail;
use App\Models\ClientDetailMaster;
use App\Models\ColorMaster;
use App\Models\CuttingScheduleDetail;
use App\Models\FareDetail;
use App\Models\GarbageAcceptRegister;
use App\Models\GarbageNotAcceptRegister;
use App\Models\GradeMaster;
use App\Models\LoopDetail;
use App\Models\LoopStock;
use App\Models\LoopUsageAccount;
use App\Models\MachineMater;
use App\Models\OrderBroker;
use App\Models\OrderPunchDetail;
use App\Models\OrderRollBagType;
use App\Models\PendingOrderBagType;
use App\Models\PrintingMachine;
use App\Models\PrintingScheduleDetail;
use App\Models\RateTypeMaster;
use App\Models\RollColorMaster;
use App\Models\RollDetail;
use App\Models\RollPrintColor;
use App\Models\RollQualityGradeMap;
use App\Models\RollQualityMaster;
use App\Models\RollTransit;
use App\Models\StereoDetail;
use App\Models\User;
use App\Models\VendorDetail;
use App\Models\VendorDetailMaster;
use App\Traits\Formula;
use App\Traits\Rolls;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Excel as ExcelExcel;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;
use PhpOffice\PhpSpreadsheet\Calculation\TextData\Replace;
use PhpOffice\PhpSpreadsheet\Shared\Date;

use Yajra\DataTables\Facades\DataTables;

class RollController extends Controller
{
    use Formula;
    use Rolls;

    private $_M_RollDetail;
    private $_M_VendorDetail;
    private $_M_PrintingScheduleDetail;
    private $_M_ClientDetails;
    private $_M_BagType;
    private $_M_Machine;

    private $_M_RollTransit;
    private $_M_RollColor;
    private $_M_CuttingScheduleDetail;
    private $_M_Color;
    private $_M_OrderPunches;
    private $_M_OrderRollBagType;
    private $_M_PendingOrderBagType;
    protected $_M_User;
    protected $_M_GarbageAcceptRegister;
    protected $_M_GarbageNotAcceptRegister;

    protected $_M_GradeMaster;
    protected $_M_FareDetail;
    protected $_M_StereoDetail;
    protected $_M_RateTypeMaster;
    protected $_M_RollQualityMaster;
    protected $_M_RollQualityGradeMap;
    protected $_M_LoopStock;
    protected $_M_LoopDetail;
    protected $_M_LoopAccount;
    protected $_M_OrderBroker;

    function __construct()
    {
        $this->_M_User = new User();
        $this->_M_RollTransit = new RollTransit();
        $this->_M_RollColor = new RollColorMaster();
        $this->_M_VendorDetail= new VendorDetailMaster();
        $this->_M_ClientDetails = new ClientDetailMaster();
        $this->_M_BagType = new BagTypeMaster();
        $this->_M_RollDetail = new RollDetail();
        $this->_M_PrintingScheduleDetail = new PrintingScheduleDetail();
        $this->_M_Machine = new MachineMater();
        $this->_M_CuttingScheduleDetail = new CuttingScheduleDetail();
        $this->_M_Color = new ColorMaster();
        $this->_M_OrderPunches = new OrderPunchDetail();
        $this->_M_OrderRollBagType = new OrderRollBagType();
        $this->_M_PendingOrderBagType = new PendingOrderBagType();
        $this->_M_GarbageAcceptRegister = new GarbageAcceptRegister();
        $this->_M_GarbageNotAcceptRegister = new GarbageNotAcceptRegister();
        $this->_M_GradeMaster = new GradeMaster();
        $this->_M_FareDetail = new FareDetail();
        $this->_M_StereoDetail = new StereoDetail();
        $this->_M_RateTypeMaster = new RateTypeMaster();
        $this->_M_RollQualityMaster = new RollQualityMaster();
        $this->_M_RollQualityGradeMap = new RollQualityGradeMap();
        $this->_M_LoopDetail = new LoopDetail();
        $this->_M_LoopStock = new LoopStock();
        $this->_M_LoopAccount = new LoopUsageAccount();
        $this->_M_OrderBroker = new OrderBroker();
    }

    #================ Roll Transit =====================

    public function addTransitRoll(Request $request){
        try{
            
            if($request->getMethod()=="POST"){ 
                $rule = [
                    "purchaseDate"=>"nullable|",
                    "venderId"=>"required|exists:".$this->_M_VendorDetail->getTable().",id,lock_status,false",
                    "size"=>"required|numeric|min:0.1",
                    "gsm"=>"required|numeric|min:0.01",
                    "rollColor"=>"required|exists:".$this->_M_RollColor->getTable().",color,lock_status,false",
                    "length"=>"required|numeric|min:0.1",
                    "netWeight"=>"required|numeric|min:0.1",
                    "grossWeight"=>"required|numeric|min:0.1",
                    "forClientId"=>"nullable".($request->forClientId?"|exists:".$this->_M_ClientDetails->getTable().",id":""),
                    "estimatedDespatchDate"=>"required_with:forClientId",
                ];
                $validate = Validator::make($request->all(),$rule);
                if($validate->fails()){
                    return validationError($validate);
                }    
                $request->merge([
                    "rollType"=>"NW",
                    "estimateDeliveryDate"=>$request->estimatedDespatchDate,
                    "bagTypeId"=>$request->bookingBagTypeId,
                    "bagUnit"=>$request->bookingBagUnits,
                    "printingColor"=>$request->bookingPrintingColor,
                ]);  
                DB::beginTransaction();   
                $id = $this->_M_RollTransit->store($request);                
                $roll = $this->_M_RollTransit->find($id);
                if($roll->client_detail_id){
                    $newRequest = new Request($roll->toArray());
                    $orderId = $this->_M_OrderPunches->store($request);
                    $newRequest->merge(["order_id"=>$orderId,"roll_id"=>$roll->id]);
                    $this->_M_OrderRollBagType->store($newRequest);
                }
                DB::commit();
                flashToast("message","New Roll Add");
                return responseMsgs(true,"New Roll Added",["rollDtl"=>$roll]);
            }
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function transitList(Request $request){        
        $flag= $request->flag;
        $user_type = Auth()->user()->user_type_id??"";
        $data["user_type"] = $user_type;
        $data["flag"]=$flag;
        $data["items"] = $this->_M_RollTransit
                        ->select("roll_transits.vender_id","roll_transits.purchase_date","roll_transits.vehicle_no",
                            "vendor_detail_masters.vendor_name",
                            DB::raw("count(roll_transits.id) as total_count,
                                    count(CASE WHEN roll_transits.size <=2 then roll_transits.id END) as total_loop,
                                    count( CASE WHEN roll_transits.client_detail_id IS NOT NULL THEN roll_transits.id END ) as total_book 
                            ")
                        )
                        ->join("vendor_detail_masters","vendor_detail_masters.id","roll_transits.vender_id")
                        ->where("roll_transits.lock_status",false)
                        ->groupBy("roll_transits.vender_id","roll_transits.purchase_date","vendor_detail_masters.vendor_name","roll_transits.vehicle_no")
                        ->orderBy("roll_transits.purchase_date")
                        ->get()
                        ->map(function($val){
                            $val->purchase_date = Carbon::parse($val->purchase_date)->format("d-m-Y");
                            $val->deletesAction = "
                                showConfirmDialog('Are You Sure Want To Delete', function(){ deleteTransit('".$val->vender_id."','".$val->purchase_date."','".$val->vehicle_no."');});
                            ";
                            return $val;
                        }); 
        return view("Roll/transit",$data);
    }

    public function transitDtl($vendor_id,Request $request){
        $user_type = Auth()->user()->user_type_id;        
        $data =[];
        $data["user_type"] = $user_type;
        $data["addToRollInStock"] = false;
        if(in_array($user_type,[1,2])){
            $data["addToRollInStock"] = true;
        }
        if($request->ajax()){                            
                $data = $this->_M_RollTransit->select("roll_transits.*","vendor_detail_masters.vendor_name",
                            "client_detail_masters.client_name","bag_type_masters.bag_type",
                            DB::raw("roll_transits.gsm_variation * 100 as gsm_variation")
                        )                            
                        ->join("vendor_detail_masters","vendor_detail_masters.id","roll_transits.vender_id")
                        ->leftJoin("client_detail_masters","client_detail_masters.id","roll_transits.client_detail_id")
                        ->leftJoin("bag_type_masters","bag_type_masters.id","roll_transits.bag_type_id")
                        ->where("roll_transits.lock_status",false)
                        ->orderBy("roll_transits.id","DESC");
                if($request->purchase_date){
                    $request->merge(["purchase_date"=>Carbon::parse($request->purchase_date)->format("Y-m-d")]);
                    $data->where("roll_transits.purchase_date",$request->purchase_date);
                }
                if($request->vehicle_no){
                    $data->where("roll_transits.vehicle_no",$request->vehicle_no);
                }
                if($vendor_id){
                    $data->where("roll_transits.vender_id",$vendor_id);
                } 
                               
                if ($request->has('export')) {
                    // Skip pagination when exporting
                    $data = $data->get();
                    return Excel::download(new ExportRoll($data), 'roll.xlsx');
                }

                // Handling search
                if ($request->has('search')) {
                    $search = $request->search['value'];  // search term from DataTables
                    
                    $data = $data->where(function ($query) use ($search) {

                        $query->where("roll_transits.roll_no","LIKE", "%$search%")
                            ->orWhere("roll_transits.purchase_date","LIKE", "%$search%")
                            ->orWhere("roll_transits.size","LIKE", "%$search%")
                            ->orWhere("roll_transits.gsm","LIKE", "%$search%")
                            ->orWhere("roll_transits.roll_color","LIKE", "%$search%")
                            ->orWhere("roll_transits.length","LIKE", "%$search%")
                            ->orWhere("roll_transits.net_weight","LIKE", "%$search%")
                            ->orWhere("roll_transits.gross_weight","LIKE", "%$search%")
                            ->orWhere('vendor_detail_masters.vendor_name', 'LIKE', "%$search%")
                            ->orWhere('client_detail_masters.client_name', 'LIKE', "%$search%")
                            ->orWhere('bag_type_masters.bag_type', 'LIKE', "%$search%");  // Assuming ststop is a field to search
                    });
                }
                $list = DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn("check",function ($val) {
                        return "<input type='checkbox' name='transitId[]' value='" . $val->id . "' /> " ;
                    })
                    ->addColumn('row_color', function ($val) {
                        $color = "";
                        $gsmVariationPer = $val->gsm_variation;
                        if(!is_between($gsmVariationPer,-8,8)){
                            $color="tr-gsm_variation_danger";
                        }
                        elseif(!is_between($gsmVariationPer,-4,4)){
                            $color="tr-gsm_variation";
                        }
                        
                        return $color;
                    })
                    ->addColumn("bag_size",function ($val) {
                        return $val->bag_type_id ? ((float)$val->w." x ".(float)$val->l.($val->g?(" x ".(float)$val->g):"")):null;
                    })
                    ->addColumn("size",function ($val) {
                        return $val->size>2 ? $val->size:"Loop";
                    })
                    ->addColumn("grade",function($val){
                        $quality = RollQualityMaster::find($val->quality_id);
                        $grade = $quality ? $quality->getGrade()->first()->grade??"":"";
                        return $grade ;                        
                    })
                    ->addColumn("quality",function($val){
                        $quality = RollQualityMaster::find($val->quality_id);
                        return $quality->quality??"" ;                        
                    })
                    ->addColumn('gsm_variation', function ($val) {                        
                        return roundFigure($val->gsm_variation)."%";
                    })
                    ->addColumn('color', function ($val) {
                        return collect(json_decode($val->printing_color,true))->implode(",");
                    })                    
                    ->addColumn("purchase_date",function($val){
                        return $val->purchase_date ? Carbon::parse($val->purchase_date)->format("d-m-Y") : "";                        
                    })
                    ->addColumn("estimate_delivery_date",function($val){
                        return $val->estimate_delivery_date ? Carbon::parse($val->estimate_delivery_date)->format("d-m-Y") : "";                        
                    })
                    ->addColumn("delivery_date",function($val){
                        return $val->delivery_date ? Carbon::parse($val->delivery_date)->format("d-m-Y") : "";                        
                    })
                    ->addColumn("printing_date",function($val){
                        return $val->printing_date ? Carbon::parse($val->printing_date)->format("d-m-Y") : "";                        
                    })
                    ->addColumn("cutting_date",function($val){
                        return $val->cutting_date ? Carbon::parse($val->cutting_date)->format("d-m-Y") : "";                        
                    })
                    ->addColumn("gsm_json",function($val){
                        return $val->gsm_json ? "(".collect(json_decode($val->gsm_json,true))->implode(",").")" : "";                        
                    })
                    ->addColumn('action', function ($val) {                    
                        $button = "";
                        if($val->is_roll_cut){
                            return $button;
                        }
                        if($val->size<=2){
                            return $button;
                        }
                        if(!$val->client_detail_id){
                            $button .= '<button class="btn btn-sm btn-warning" onClick="openModelBookingModel('.$val->id.')" >Book</button>';
                        }if($val->client_detail_id && !$val->is_printed){
                            $button .= '<button class="btn btn-sm btn-danger" onClick="openModelBookingModel('.$val->id.')" >Alter Booking</button>';
                            $button .= '<button class="btn btn-sm btn-warning" onClick="removeBooking('.$val->id.')" >Remove</button>';
                        }
                        return $button;
                    })
                    ->rawColumns(['action','color',"check"])
                    ->make(true);
                return $list;

        }
        $data["purchase_date"] = $request->purchase_date;
        $data["vender"]        = $this->_M_VendorDetail->find($vendor_id);
        return view("Roll/transitDtl",$data);
    }

    public function deleteTransit(Request $request){
        try{
            DB::beginTransaction();
            $this->_M_RollTransit
                ->where("vender_id",$request->vendorId)
                ->where("purchase_date",Carbon::parse($request->purchaseDate)->format("Y-m-d"))
                ->where("vehicle_no",$request->vehicleNo)
                ->update(["lock_status"=>true]);
            DB::commit();
            return responseMsgs(true,"Roll Delete","");
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }


    
    function rollTransitBook(Request $request){
        try{
            $rule=[
                "rollId"=>"required|exists:".$this->_M_RollTransit->getTable().",id,lock_status,false",
                "bookingForClientId"=>"required|exists:".$this->_M_ClientDetails->getTable().",id,lock_status,false",
                "bookingEstimatedDespatchDate"=>"required|date",
                "bookingBagUnits"=>"required|in:Kg,Piece",
                "bookingBagTypeId"=>"required|exists:".$this->_M_BagType->getTable().",id",
                "bookingPrintingColor"=>"required|array",
                "bookingPrintingColor.*"=>"required",
            ];
            $validate = Validator::make($request->all(),$rule);
            if($validate->fails()){
                return validationError($validate);
            }

            $request->merge([
                "clientDetailId"=>$request->bookingForClientId,
                "estimateDeliveryDate"=>$request->bookingEstimatedDespatchDate,
            ]);

            $roll = $this->_M_RollTransit->find($request->rollId);
            $roll->client_detail_id = $request->bookingForClientId;
            $roll->estimate_delivery_date = $request->bookingEstimatedDespatchDate;
            $roll->bag_type_id = $request->bookingBagTypeId;
            $roll->bag_unit = $request->bookingBagUnits;
            $roll->w = $request->w;
            $roll->l = $request->l;
            $roll->g = $request->g;
            $roll->printing_color = $request->bookingPrintingColor; 
            $roll->loop_color = $request->looColor;
                      
            $newRequest = new Request($roll->toArray());
            
            DB::beginTransaction();
            $roll->update();
            $orderId = $this->_M_OrderPunches->store($request);
            $newRequest->merge(["order_id"=>$orderId,"roll_id"=>$roll->id]);
            $this->_M_OrderRollBagType->store($newRequest);
            DB::commit();
            return responseMsgs(true,"Roll No. ".$roll->roll_no." is Booked","");
        }catch(Exception $e){
            DB::rollBack();
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    function rollTransitRollStock(Request $request){
        try{

            foreach($request->items as $val){
                $rollTransit = $this->_M_RollTransit->find($val);
    
                if($rollTransit){
                    DB::beginTransaction();
                    if($rollTransit->size>2){
                        $rollDtl =$rollTransit->replicate();
                        $rollDtl->setTable($this->_M_RollDetail->getTable());
                        $rollDtl->id =  $rollTransit->id;
                        $rollDtl->save();
                    }else{
                        $rollTransit->loop_color = $rollTransit->roll_color;
                        $loop = array_filter($rollTransit->getAttributes(), function ($value, $key) {
                            
                            return in_array($key,$this->_M_LoopDetail->getFillable()); // Example: Filter out null values
                        },ARRAY_FILTER_USE_BOTH);               
                        $rollDtl =$this->_M_LoopDetail->newInstance($loop);
                        $rollDtl->setTable($this->_M_LoopDetail->getTable());
                        $rollDtl->id =  $rollTransit->id;
                        $rollDtl->save(); 
                        $loopStock = $this->_M_LoopStock->where("loop_color",$rollDtl->loop_color)->first();
                        if(!$loopStock){
                            $newRequest = new Request(
                                [
                                    "loop_color"=>$rollDtl->loop_color,
                                ]
                            );
                            $loopStockId = $this->_M_LoopStock->store($newRequest);
                            $loopStock = $this->_M_LoopStock->find($loopStockId);
                        }
                        $newLoopAccRequest = new Request(
                            [
                                "loop_stock_id"=>$loopStock->id,
                                "loop_id"=>$rollDtl->id,
                                "description"=>"new roll add",
                                "opening_balance"=>$loopStock->balance,
                                "credit"=>0,
                                "debit"=>$rollDtl->net_weight,
                                "balance"=>$loopStock->balance +  $rollDtl->net_weight,
                                "user_id"=>Auth()->user()->id
                            ]
                        );
                        $this->_M_LoopAccount->store($newLoopAccRequest);
                        $loopStock->balance =  $loopStock->balance +  $rollDtl->net_weight;   
                        $loopStock->update();                  
                    }

                    $rollTransit->delete();
                    DB::commit();
                }
            }
            return responseMsgs(true,"Rolls Added On Stock","");
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }

    }

    #=========== end Roll Transit ======================

    public function downloadCsvTemplate()
    {
        
        // Define the CSV headers (columns)
        $headers = Config::get("customConfig.rollImportCsvHeader");

        // Sample data for the CSV
        $data=[];
        foreach($headers as $val){
            $data[0][]="";
            $data[1][]="";
        }

        // Open a temporary file in memory to write the CSV content
        $file = fopen('php://temp', 'w');

        // Write the header row to the CSV file
        fputcsv($file, $headers);

        // Write the data rows to the CSV file
        foreach ($data as $row) {
            fputcsv($file, $row);
        }

        // Move the pointer back to the start of the file
        rewind($file);

        // Get the content of the CSV file
        $csvContent = stream_get_contents($file);

        // Close the file
        fclose($file);

        // Return the CSV content with the correct MIME type and headers
        return response($csvContent)
            ->header('Content-Type', 'text/csv')  // Ensure correct MIME type for CSV
            ->header('Content-Disposition', 'attachment; filename="roll_template.csv"'); 
    }

    public function importRoll(Request $request){
        try{
            $dataWithHeadings=[];
            ini_set('max_execution_time', 600);
            $validate = Validator::make($request->all(),["csvFile"=>"required|mimes:csv,xlsx"]);
            if($validate->fails()){
                return validationError($validate);
            }
            $file = $request->file('csvFile');
            $headings = (new HeadingRowImport())->toArray($file)[0][0];
            $expectedHeadings = Config::get("customConfig.rollImportCsvHeader");
            // $expectedHeadings = ['vendor_name','vehicle_no', 'purchase_date',"quality", 'roll_size',"roll_type","hardness","roll_gsm","bopp","roll_color","roll_length","net_weight","gross_weight"];
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
                    $rowData["purchase_date"] = is_int($rowData["purchase_date"])? getDateColumnAttribute($rowData['purchase_date']) : $rowData['purchase_date'];
                }
                $validator = Validator::make($rowData, [
                    'vendor_name' => [
                        "required",
                        function($attribute, $value, $fail)use ($rowData,$index ){
                            if(!$this->_M_VendorDetail->where(DB::raw("UPPER(vendor_name)"),trim(strtoupper($value)))->first()){
                                $fail('The '.$attribute.' is invalid.');
                            }
                        },
                    ],
                    "quality"=>[
                        "required",
                        function ($attribute, $value, $fail)use ($rowData,$index )
                        {
                            $vendor = $this->_M_VendorDetail->where(DB::raw("upper(vendor_name)"),trim(strtoupper($rowData["vendor_name"])))->first();
                            $quality = $this->_M_RollQualityMaster->where("vendor_id",$vendor->id??0)->where(DB::raw("upper(quality)"),trim(strtoupper($value)))->first();
                            if($vendor && !$quality)
                            {
                                $fail('The '.$attribute.' is invalid.');
                            }

                        },
                    ],
                    'purchase_date' => 'required|date',
                    'roll_size' => 'required|numeric',
                    'roll_type' => 'nullable|in:NW,BOPP,LAM',
                    "hardness" => "nullable",
                    'roll_gsm' => 'required|numeric',
                    'bopp' => [
                        'required_if:roll_type,BOPP,LAM',
                        function ($attribute, $value, $fail)use ($rowData,$index )
                        {
                            $sumJson = $value ? array_sum(explode("/",$value)):null;
                            $gsm = $rowData["roll_gsm"];
                            if($sumJson && $sumJson!=$gsm)
                            {
                                $fail('The '.$attribute.' thickness is invalid.');
                            }
                            if($rowData["roll_type"]=="BOPP" && sizeof(explode("/",$value))!=3){
                                $fail('The '.$attribute.' thickness is invalid. like(GSM/lamination/BOPP)');
                            }
                            if($rowData["roll_type"]=="LAM" && sizeof(explode("/",$value))!=2){
                                $fail('The '.$attribute.' thickness is invalid. like(GSM/lamination)');
                            }

                        },

                    ],
                    'roll_color' => 
                    [
                       "required" ,
                       function($attribute, $value, $fail)use ($rowData,$index ){
                            $color = $this->_M_RollColor->where(DB::raw("UPPER(color)"),strtoupper(trim($value)))->first();
                            if($rowData["roll_size"]<=2){
                                $color = $this->_M_LoopStock->where(DB::raw("UPPER(loop_color)"),strtoupper(trim($value)))->first();
                            }
                            if(!$color){
                                $fail('The '.$attribute.' is invalid.');
                            }
                        },
                    ],
                    'roll_length' => 'required|int',
                    'net_weight' => 'required|numeric',
                    'gross_weight' => 'required|numeric',
                ]);

                if ($validator->fails()) {
                    $validationErrors[$index] = $validator->errors()->all();
                }
                $dataWithHeadings[] = $rowData; 
            }

            if($dataWithHeadings && isset($dataWithHeadings[0]["roll_no"])){
                $group = collect($dataWithHeadings)->groupBy("roll_no")->filter(function($val,$index){                    
                    return $index && $val->count()>1;
                });
                
                if($group->count()>0){
                    foreach($group as $index=>$val){                        
                        $validationErrors[] = ["Roll no $index is repeated ".sizeof($val)." time"];
                    }
                }

            }

            if (!empty($validationErrors)) {
                return responseMsgs(false,"Validation Error",$validationErrors);
            }

            // Import the CSV file using the RollDetailsImport class
            DB::beginTransaction();
            Excel::import(new RollDetailsImport, $file);
            DB::commit();
            return responseMsgs(true,"data import","");

        }catch(Exception $e){
            DB::rollBack();
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    function rollBook(Request $request){
        try{

            $rule=[
                "rollId"=>"required|exists:".$this->_M_RollDetail->getTable().",id,lock_status,false",
                "bookingForClientId"=>"required|exists:".$this->_M_ClientDetails->getTable().",id,lock_status,false",
                "bookingEstimatedDespatchDate"=>"required|date",
                "bookingBagUnits"=>"required|in:Kg,Piece",
                "bookingBagTypeId"=>"required|exists:".$this->_M_BagType->getTable().",id",
                "bookingPrintingColor"=>"required|array",
                "bookingPrintingColor.*"=>"required",
            ];
            $validate = Validator::make($request->all(),$rule);
            if($validate->fails()){
                return validationError($validate);
            }

            $request->merge([
                "clientDetailId"=>$request->bookingForClientId,
                "estimateDeliveryDate"=>$request->bookingEstimatedDespatchDate,
            ]);

            $roll = $this->_M_RollDetail->find($request->rollId);
            $roll->client_detail_id = $request->bookingForClientId;
            $roll->estimate_delivery_date = $request->bookingEstimatedDespatchDate;
            $roll->bag_type_id = $request->bookingBagTypeId;
            $roll->bag_unit = $request->bookingBagUnits;
            $roll->w = $request->w;
            $roll->l = $request->l;
            $roll->g = $request->g;
            $roll->printing_color = $request->bookingPrintingColor; 
            $roll->loop_color = $request->looColor;

            $newRequest = new Request($roll->toArray());

            DB::beginTransaction();
            $roll->update();

            $orderId = $this->_M_OrderPunches->store($request);
            $newRequest->merge(["order_id"=>$orderId,"roll_id"=>$roll->id]);
            $this->_M_OrderRollBagType->store($newRequest);

            DB::commit();
            return responseMsgs(true,"Roll No. ".$roll->roll_no." is Booked","");
        }catch(Exception $e){
            DB::rollBack();
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function rollList(Request $request){
        $flag= $request->flag;
        $user_type = Auth()->user()->user_type_id??"";
        if($request->ajax()){
            $data = $this->_M_RollDetail->select("roll_details.*","vendor_detail_masters.vendor_name",
                                "client_detail_masters.client_name",
                                "bag_type_masters.bag_type",
                                DB::raw("
                                    roll_details.gsm_variation * 100 as gsm_variation,
                                    TO_CHAR(roll_details.purchase_date, 'DD-MM-YYYY') as purchase_date ,
                                    TO_CHAR(roll_details.estimate_delivery_date, 'DD-MM-YYYY') as estimate_delivery_date ,
                                    TO_CHAR(roll_details.delivery_date, 'DD-MM-YYYY') as delivery_date ,
                                    TO_CHAR(roll_details.printing_date, 'DD-MM-YYYY') as printing_date ,
                                    TO_CHAR(roll_details.cutting_date, 'DD-MM-YYYY') as cutting_date ,    
                                    TO_CHAR(printing_schedule_details.printing_date, 'DD-MM-YYYY') as schedule_date_for_print ,
                                    TO_CHAR(cutting_schedule_details.cutting_date , 'DD-MM-YYYY') as schedule_date_for_cutting                                 
                                    ")
                                )
                    ->join("vendor_detail_masters","vendor_detail_masters.id","roll_details.vender_id")
                    ->leftJoin("client_detail_masters","client_detail_masters.id","roll_details.client_detail_id")
                    ->leftJoin("bag_type_masters","bag_type_masters.id","roll_details.bag_type_id")
                    ->leftJoin("printing_schedule_details",function($join){
                        $join->on("printing_schedule_details.roll_id","=","roll_details.id")
                        ->where("printing_schedule_details.lock_status",false);
                    })
                    ->leftJoin("cutting_schedule_details",function($join){
                        $join->on("cutting_schedule_details.roll_id","=","roll_details.id")
                        ->where("cutting_schedule_details.lock_status",false);
                    })
                    ->where("roll_details.lock_status",false);                    
            if($flag!="register"){
                $data->where("roll_details.is_cut",false);
            }
            if($flag=="register" ){
                $fromDate = $request->fromDate;
                $uptoDate = $request->uptoDate;
                if($fromDate && $uptoDate){              
                    $data->whereBetween("purchase_date",[$fromDate,$uptoDate]);
                }
                elseif($fromDate){
                    $data->where("purchase_date",">=",$fromDate);
                }
                elseif($uptoDate){
                    $data->where("purchase_date","<=",$uptoDate);
                }
            }

            if($flag=="schedule-printing"){
                if(!in_array($user_type,[11,12])){
                    $data->whereNotNull("roll_details.client_detail_id");
                }
                $data->where("roll_details.is_printed",false)
                    ->where(function($where){
                        $where->where("roll_details.is_schedule_for_print",false)
                        ->orWhere("roll_details.schedule_date_for_print","<",Carbon::now()->format("Y-m-d"));
                    })
                    ->orderBy("roll_details.despatch_date","ASC");
            }elseif($flag=="print"){
                $data->where("roll_details.is_printed",false)
                ->where("roll_details.is_schedule_for_print",true)
                ->orderBy("roll_details.schedule_date_for_print","ASC");
            }elseif($flag=="schedule-cutting"){
                if(!in_array($user_type,[11,12])){
                    $data->whereNotNull("roll_details.client_detail_id");
                }
                $data->where("roll_details.is_cut",false)
                    ->where(function($where){
                        $where->where("roll_details.is_schedule_for_cutting",false)
                        ->orWhere("roll_details.schedule_date_for_cutting","<",Carbon::now()->format("Y-m-d"));
                    })
                    ->orderBy("roll_details.schedule_date_for_cutting","ASC");
            }elseif($flag=="cutting"){
                $data->where("roll_details.is_cut",false)
                ->where("roll_details.is_schedule_for_cutting",true)
                ->orderBy("roll_details.schedule_date_for_cutting","ASC");
            }
            else{
                $data->orderBy("roll_details.id","DESC");
            }
            // if($flag=="booking"){
            //     $data->whereNull("roll_details.for_client_id");
            // }
            if ($request->has('export')) {
                // Skip pagination when exporting
                $data = $data->get();
                return Excel::download(new ExportRoll($data), 'roll.xlsx');
            }
            // Handling search
            if ($request->has('search')) {
                $search = $request->search['value'];  // search term from DataTables
                
                $data = $data->where(function ($query) use ($search) {

                    $query->where("roll_details.roll_no","LIKE", "%$search%")
                        ->orWhere("roll_details.purchase_date","LIKE", "%$search%")
                        ->orWhere("roll_details.size","LIKE", "%$search%")
                        ->orWhere("roll_details.gsm","LIKE", "%$search%")
                        ->orWhere("roll_details.roll_color","LIKE", "%$search%")
                        ->orWhere("roll_details.length","LIKE", "%$search%")
                        ->orWhere("roll_details.net_weight","LIKE", "%$search%")
                        ->orWhere("roll_details.gross_weight","LIKE", "%$search%")
                        ->orWhere('vendor_detail_masters.vendor_name', 'LIKE', "%$search%")
                        ->orWhere('client_detail_masters.client_name', 'LIKE', "%$search%")
                        ->orWhere('bag_type_masters.bag_type', 'LIKE', "%$search%");  // Assuming ststop is a field to search
                });
            }
            // DB::enableQueryLog();
            $list = DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('row_color', function ($val) use($flag) {
                    $color = "";
                    $gsmVariationPer = $val->gsm_variation;
                    if(!is_between($gsmVariationPer,-8,8)){
                        $color="tr-gsm_variation_danger";
                    }
                    elseif(!is_between($gsmVariationPer,-4,4)){
                        $color="tr-gsm_variation";
                    }
                    if($val->for_client_id && $val->is_printed){
                        $color="tr-client-printed";
                    }elseif($val->is_printed){
                        $color="tr-printed";
                    }
                    elseif($val->for_client_id){
                        $color="tr-client";
                    }
                    if($flag=="schedule-printing"){
                        $color="";
                        if($flag=="schedule" && $val->estimated_despatch_date ){
                            $dayDiff = Carbon::now()->diffInDays(Carbon::parse($val->estimated_despatch_date),false);
                            // dd($dayDiff,$val->despatch_date);
                            if($dayDiff<3){
                                $color="tr-primary-print";
                            }
                            if($dayDiff<2){
                                $color="tr-argent-print";
                            }
                            if($dayDiff<0){
                                $color="tr-expiry-print blink";
                            }
                        }
                    }
                    if($flag=="schedule-cutting"){
                        $color="";
                        if($flag=="schedule" && $val->estimated_despatch_date ){
                            $dayDiff = Carbon::now()->diffInDays(Carbon::parse($val->estimated_despatch_date),false);
                            // dd($dayDiff,$val->despatch_date);
                            if($dayDiff<3){
                                $color="tr-primary-print";
                            }
                            if($dayDiff<2){
                                $color="tr-argent-print";
                            }
                            if($dayDiff<0){
                                $color="tr-expiry-print blink";
                            }
                        }
                    }
                    
                    return $color;
                })
                ->addColumn('gsm_variation', function ($val) {                        
                    return roundFigure($val->gsm_variation)."%";
                })
                ->addColumn("grade",function($val){
                    $quality = RollQualityMaster::find($val->quality_id);
                    $grade = $quality ? $quality->getGrade()->first()->grade??"":"";
                    return $grade ;                        
                })
                ->addColumn("quality",function($val){
                    $quality = RollQualityMaster::find($val->quality_id);
                    return $quality->quality??"" ;                        
                })
                ->addColumn("gsm_json",function($val){
                    return $val->gsm_json ? "(".collect(json_decode($val->gsm_json,true))->implode(",").")" : "";                        
                })
                ->addColumn('print_color', function ($val) {                    
                    return collect(json_decode($val->printing_color,true))->implode(",");
                })
                ->addColumn("bag_size",function ($val) {
                    return $val->bag_type_id ? ((float)$val->w." x ".(float)$val->l.($val->g?(" x ".(float)$val->g):"")):null;
                })
                ->addColumn('action', function ($val) use($flag,$user_type) {                    
                    $button = "";
                    if($val->is_cut){
                        return $button;
                    }
                    if(!($val->is_cut || $val->is_printed)){
                        $button.='<button class="btn btn-sm btn-primary" onClick="editRoll('.$val->id.')" >Edit</button>';
                    }
                    if(in_array($user_type,[1,2]) && !$val->client_detail_id){
                        $button .= '<button class="btn btn-sm btn-warning" onClick="openModelBookingModel('.$val->id.')" >Book</button>';
                    }if(in_array($user_type,[1,2]) && $val->client_detail_id && !$val->is_printed){
                        $button .= '<button class="btn btn-sm btn-danger" onClick="openModelBookingModel('.$val->id.')" >Alter Booking</button>';
                        $button .= '<button class="btn btn-sm btn-warning" onClick="removeBooking('.$val->id.')" >Remove</button>';
                        // $button .= '<button class="btn btn-sm btn-danger" onClick="openModelAlterBookingModel('.$val->id.')" >Alter Booking</button>';
                    }
                    if($flag=="schedule-printing"){
                        $button='<button class="btn btn-sm btn-warning" onClick="openPrintingScheduleModel('.$val->id.')" >Schedule For Print</button>';
                        if($val->is_schedule_for_print){
                            $button='<button class="btn btn-sm btn-warning" onClick="openPrintingScheduleModel('.$val->id.')" >Re-Schedule For Print</button>';
                        }
                    }
                    if($flag=="print"){
                        $button='<button class="btn btn-sm btn-info" onClick="openPrintingModel('.$val->id.')" >Update Print</button>';
                    }
                    if($flag=="schedule-cutting"){
                        $button='<button class="btn btn-sm btn-warning" onClick="openCuttingScheduleModel('.$val->id.')" >Schedule For Cut</button>';
                        if($val->is_schedule_for_cutting){
                            $button='<button class="btn btn-sm btn-warning" onClick="openCuttingScheduleModel('.$val->id.')" >Re-Schedule For Cut</button>';
                        }
                    }
                    if($flag=="cutting"){
                        $button='<button class="btn btn-sm btn-info" onClick="openCuttingModel('.$val->id.')" >Update Cutting</button>';
                    }
                    return $button;
                })
                ->rawColumns(['row_color', 'action'])
                ->make(true);
                // dd(DB::getQueryLog());
            return $list;

        }
        $data["flag"]=$flag;
        return view("Roll/list",$data);
    }

    public function rollDtl($id,Request $request){
        try{
            $data = $this->_M_RollDetail->find($id);
            if($request->flag=="cutting"){
                $data->schedule_date = $data->getCuttingSchedule()->first();
            }elseif($request->flag=="printing"){
                $data->schedule_date = $data->getPrintingSchedule()->first();
            }
            return responseMsgs(true,"roll dtl fetched",$data);
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function rollDtlFull($id,Request $request){
        try{
            $data = $this->_M_RollDetail->find($id);
            if(!$data){
                $data = $this->_M_RollTransit->find($id);
            }
            if(!$data){
                throw new Exception("no data find");
            }
            $data->vender_name = $data->getVendor()->first()->vendor_name??"";
            return responseMsgs(true,"roll dtl fetched",$data);
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }


    #====== roll register =========================
    public function rollRegister(Request $request){
        $flag= $request->flag;
        $user_type = Auth()->user()->user_type_id??"";
        list($from,$upto)=explode("-",getFY());
        $data["fromDate"] = $from."-04-01";
        $data["uptoDate"] = Carbon::now()->format("Y-m-d");
        if($request->ajax()){
            // dd($request->ajax());
            $fromDate = $request->fromDate;
            $uptoDate = $request->uptoDate;
            $data = $this->_M_RollDetail->select("roll_details.*","vendor_detail_masters.vendor_name",
                                "client_detail_masters.client_name",
                                "bag_type_masters.bag_type",
                                DB::raw("printing_schedule_details.printing_date AS schedule_date_for_print , 
                                cutting_schedule_details.cutting_date AS schedule_date_for_cutting")
                                )
                    ->join("vendor_detail_masters","vendor_detail_masters.id","roll_details.vender_id")
                    ->leftJoin("client_detail_masters","client_detail_masters.id","roll_details.client_detail_id")
                    ->leftJoin("bag_type_masters","bag_type_masters.id","roll_details.bag_type_id")
                    ->leftJoin("printing_schedule_details",function($join){
                        $join->on("printing_schedule_details.roll_id","=","roll_details.id")
                        ->where("printing_schedule_details.lock_status",false);
                    })
                    ->leftJoin("cutting_schedule_details",function($join){
                        $join->on("cutting_schedule_details.roll_id","=","roll_details.id")
                        ->where("cutting_schedule_details.lock_status",false);
                    })
                    ->where("roll_details.lock_status",false)
                    ->orderBy("roll_details.id","DESC"); 

            if($fromDate && $uptoDate){              
                $data->whereBetween("purchase_date",[$fromDate,$uptoDate]);
            }
            elseif($fromDate){
                $data->where("purchase_date",">=",$fromDate);
            }
            elseif($uptoDate){
                $data->where("purchase_date","<=",$uptoDate);
            } 
            $list = DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('row_color', function ($val) use($flag) {
                    $color = "";                    
                    $gsmVariationPer = $val->gsm_variation;
                    if(!is_between($gsmVariationPer,-8,8)){
                        $color="tr-gsm_variation_danger";
                    }
                    elseif(!is_between($gsmVariationPer,-4,4)){
                        $color="tr-gsm_variation";
                    }
                    if($val->for_client_id && $val->is_printed){
                        $color="tr-client-printed";
                    }elseif($val->is_printed){
                        $color="tr-printed";
                    }
                    elseif($val->for_client_id){
                        $color="tr-client";
                    }                    
                    return $color;
                })
                ->addColumn('print_color', function ($val) {                    
                    return collect(json_decode($val->printing_color,true))->implode(",");
                })
                ->addColumn("loop_color",function($val){
                    return"";
                })
                ->addColumn("gsm_json",function($val){
                    return $val->gsm_json ? "(".collect(json_decode($val->gsm_json,true))->implode(",").")" : "";                        
                })
                ->rawColumns(['row_color', 'action'])
                ->make(true);
                // dd(DB::getQueryLog());
            return $list;

        }
        $data["flag"]=$flag;
        return view("Roll/register",$data);
    }

    public function loopRegister(Request $request){
        list($from,$upto)=explode("-",getFY());
        $data["fromDate"] = $from."-04-01";
        $data["uptoDate"] = Carbon::now()->format("Y-m-d");
        if($request->ajax()){
            $fromDate = $request->fromDate;
            $uptoDate = $request->uptoDate;
            $data = $this->_M_LoopDetail->select("loop_details.*","vendor_detail_masters.vendor_name",)
                    ->join("vendor_detail_masters","vendor_detail_masters.id","loop_details.vender_id")
                    ->where("loop_details.lock_status",false)
                    ->orderBy("loop_details.id","DESC"); 

            if($fromDate && $uptoDate){              
                $data->whereBetween("purchase_date",[$fromDate,$uptoDate]);
            }
            elseif($fromDate){
                $data->where("purchase_date",">=",$fromDate);
            }
            elseif($uptoDate){
                $data->where("purchase_date","<=",$uptoDate);
            } 
            $list = DataTables::of($data)
                ->addIndexColumn() 
                ->addColumn("purchase_date",function($val){
                    return Carbon::parse($val->purchase_date)->format("d-m-Y");                        
                })
                ->addColumn("gsm_json",function($val){
                    return $val->gsm_json ? "(".collect(json_decode($val->gsm_json,true))->implode(",").")" : "";                        
                })
                ->rawColumns(['row_color', 'action'])
                ->make(true);
            return $list;

        }
        return view("Roll/loopRegister",$data);
    }

    public function rollRegisterPrinting(Request $request){
        $flag= $request->flag;
        $machineId = $request->machineId;
        $user_type = Auth()->user()->user_type_id??"";
        list($from,$upto)=explode("-",getFY());
        $data["fromDate"] = $from."-04-01";
        $data["uptoDate"] = Carbon::now()->format("Y-m-d");
        if($request->ajax()){
            // dd($request->ajax());
            $fromDate = $request->fromDate;
            $uptoDate = $request->uptoDate;
            $data = $this->_M_RollDetail->select("roll_details.*","vendor_detail_masters.vendor_name",
                                "client_detail_masters.client_name",
                                "bag_type_masters.bag_type",
                                DB::raw("
                                    TO_CHAR(roll_details.purchase_date, 'DD-MM-YYYY') as purchase_date ,
                                    TO_CHAR(roll_details.estimate_delivery_date, 'DD-MM-YYYY') as estimate_delivery_date ,
                                    TO_CHAR(roll_details.delivery_date, 'DD-MM-YYYY') as delivery_date ,
                                    TO_CHAR(roll_details.printing_date, 'DD-MM-YYYY') as printing_date ,
                                    TO_CHAR(roll_details.cutting_date, 'DD-MM-YYYY') as cutting_date                                     
                                    ")
                                )
                    ->join("vendor_detail_masters","vendor_detail_masters.id","roll_details.vender_id")
                    ->leftJoin("client_detail_masters","client_detail_masters.id","roll_details.client_detail_id")
                    ->leftJoin("bag_type_masters","bag_type_masters.id","roll_details.bag_type_id")                    
                    ->where("roll_details.lock_status",false)
                    ->where("roll_details.printing_machine_id",$machineId)
                    ->orderBy("roll_details.id","DESC"); 

            if($fromDate && $uptoDate){              
                $data->whereBetween("printing_date",[$fromDate,$uptoDate]);
            }
            elseif($fromDate){
                $data->where("printing_date",">=",$fromDate);
            }
            elseif($uptoDate){
                $data->where("printing_date","<=",$uptoDate);
            } 
            DB::enableQueryLog();
            // $data->get();
            // dd(DB::getQueryLog());
            $list = DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('row_color', function ($val) use($flag) {
                    $color = "";
                    if($val->for_client_id && $val->is_printed){
                        $color="tr-client-printed";
                    }elseif($val->is_printed){
                        $color="tr-printed";
                    }
                    elseif($val->for_client_id){
                        $color="tr-client";
                    }                    
                    return $color;
                })
                ->addColumn('print_color', function ($val) {                    
                    return collect(json_decode($val->printing_color,true))->implode(",");
                })
                ->addColumn("loop_color",function($val){
                    return"";
                })
                ->addColumn("gsm_json",function($val){
                    return $val->gsm_json ? "(".collect(json_decode($val->gsm_json,true))->implode(",").")" : "";                        
                })
                ->rawColumns(['row_color', 'action'])
                ->make(true);
                // dd(DB::getQueryLog());
            return $list;

        }
        $data=[];
        $data["machine"] = $this->_M_Machine->find($machineId);
        return view("Roll/rollRegisterPrinting",$data);
    }

    public function rollRegisterCutting(Request $request){
        $flag= $request->flag;
        $machineId = $request->machineId;
        $user_type = Auth()->user()->user_type_id??"";
        list($from,$upto)=explode("-",getFY());
        $data["fromDate"] = $from."-04-01";
        $data["uptoDate"] = Carbon::now()->format("Y-m-d");
        if($request->ajax()){
            // dd($request->ajax());
            $fromDate = $request->fromDate;
            $uptoDate = $request->uptoDate;
            $data = $this->_M_RollDetail->select("roll_details.*","vendor_detail_masters.vendor_name",
                                "client_detail_masters.client_name",
                                "bag_type_masters.bag_type",
                                DB::raw("
                                    TO_CHAR(roll_details.purchase_date, 'DD-MM-YYYY') as purchase_date ,
                                    TO_CHAR(roll_details.estimate_delivery_date, 'DD-MM-YYYY') as estimate_delivery_date ,
                                    TO_CHAR(roll_details.delivery_date, 'DD-MM-YYYY') as delivery_date ,
                                    TO_CHAR(roll_details.printing_date, 'DD-MM-YYYY') as printing_date ,
                                    TO_CHAR(roll_details.cutting_date, 'DD-MM-YYYY') as cutting_date                                     
                                    ")
                                )
                    ->join("vendor_detail_masters","vendor_detail_masters.id","roll_details.vender_id")
                    ->leftJoin("client_detail_masters","client_detail_masters.id","roll_details.client_detail_id")
                    ->leftJoin("bag_type_masters","bag_type_masters.id","roll_details.bag_type_id")                    
                    ->where("roll_details.lock_status",false)
                    ->where("roll_details.cutting_machine_id",$machineId)
                    ->orderBy("roll_details.id","DESC"); 

            if($fromDate && $uptoDate){              
                $data->whereBetween("cutting_date",[$fromDate,$uptoDate]);
            }
            elseif($fromDate){
                $data->where("cutting_date",">=",$fromDate);
            }
            elseif($uptoDate){
                $data->where("cutting_date","<=",$uptoDate);
            } 
            DB::enableQueryLog();
            // $data->get();
            // dd(DB::getQueryLog());
            $list = DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('row_color', function ($val) use($flag) {
                    $color = "";
                    if($val->for_client_id && $val->is_printed){
                        $color="tr-client-printed";
                    }elseif($val->is_printed){
                        $color="tr-printed";
                    }
                    elseif($val->for_client_id){
                        $color="tr-client";
                    }                    
                    return $color;
                })
                ->addColumn('print_color', function ($val) {                    
                    return collect(json_decode($val->printing_color,true))->implode(",");
                })
                ->addColumn("loop_color",function($val){
                    return"";
                })
                ->addColumn("gsm_json",function($val){
                    return $val->gsm_json ? "(".collect(json_decode($val->gsm_json,true))->implode(",").")" : "";                        
                })
                ->rawColumns(['row_color', 'action'])
                ->make(true);
                // dd(DB::getQueryLog());
            return $list;

        }
        $data=[];
        $data["machine"] = $this->_M_Machine->find($machineId);
        return view("Roll/rollRegisterCutting",$data);
    }
    

    #===== roll schedule =========================

    public function rollSchedule(Request $request){
        try{

            $flag= $request->flag;
            $user_type = Auth()->user()->user_type_id??"";
            $currentDate = Carbon::now()->format("Y-m-d");
            
            if($request->ajax()){
                $data = $this->_M_RollDetail->select("roll_details.*","vendor_detail_masters.vendor_name",
                                    "client_detail_masters.client_name",
                                    "bag_type_masters.bag_type",
                                    DB::raw("
                                    TO_CHAR(roll_details.purchase_date, 'DD-MM-YYYY') as purchase_date ,
                                    TO_CHAR(roll_details.estimate_delivery_date, 'DD-MM-YYYY') as estimate_delivery_date ,
                                    TO_CHAR(roll_details.delivery_date, 'DD-MM-YYYY') as delivery_date ,
                                    TO_CHAR(roll_details.printing_date, 'DD-MM-YYYY') as printing_date ,
                                    TO_CHAR(printing_schedule_details.printing_date, 'DD-MM-YYYY') as schedule_date_for_print ,
                                    TO_CHAR(cutting_schedule_details.cutting_date, 'DD-MM-YYYY') as schedule_date_for_cutting                                      
                                    ")
                                    )
                        ->join("vendor_detail_masters","vendor_detail_masters.id","roll_details.vender_id")
                        ->Join("client_detail_masters","client_detail_masters.id","roll_details.client_detail_id")
                        ->leftJoin("bag_type_masters","bag_type_masters.id","roll_details.bag_type_id")
                        ->leftJoin("printing_schedule_details",function($join){
                            $join->on("printing_schedule_details.roll_id","=","roll_details.id")
                            ->where("printing_schedule_details.lock_status",false);
                        })
                        ->leftJoin("cutting_schedule_details",function($join){
                            $join->on("cutting_schedule_details.roll_id","=","roll_details.id")
                            ->where("cutting_schedule_details.lock_status",false);
                        })
                        ->where("roll_details.lock_status",false);
                if($flag=="printing"){
                    $data->where("roll_details.is_printed",false)
                    ->whereNotNull(DB::raw("json_array_length(roll_details.printing_color)"))
                    ->where(function($where)use($currentDate){
                        $where->where("printing_schedule_details.printing_date","<",$currentDate)
                        ->orWhereNull("printing_schedule_details.id");

                    });
                } 

                if($flag=="cutting"){
                    $data->where("roll_details.is_cut",false)
                    ->where(function($where)use($currentDate){
                        $where->where("cutting_schedule_details.cutting_date","<",$currentDate)
                        ->orWhereNull("cutting_schedule_details.id");

                    })
                    ->where(function($where){
                        $where->whereNull("printing_schedule_details.id")
                            ->orWhere(DB::raw(" CASE WHEN printing_schedule_details.id IS NOT NULL AND roll_details.is_printed = TRUE THEN TRUE ELSE FALSE END"),true);
                    })
                    ->whereNotNull("roll_details.client_detail_id");
                }

                $data->orderBy("roll_details.estimate_delivery_date","ASC");
                // DB::enableQueryLog();
                // $data->get();
                // dd(DB::getQueryLog());
                
                $list = DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('row_color', function ($val) use($flag) {
                        $color = "";
                        if($val->for_client_id && $val->is_printed){
                            $color="tr-client-printed";
                        }elseif($val->is_printed){
                            $color="tr-printed";
                        }
                        elseif($val->for_client_id){
                            $color="tr-client";
                        }                    
                        return $color;
                    })
                    ->addColumn('action', function ($val)use($flag) {                    
                        $button = "";
                        if($val->is_roll_cut){
                            return $button;
                        }
                        if($flag=="printing"){
                            if(!$val->schedule_date_for_print){
                                $button .= '<button class="btn btn-sm btn-warning" onClick="openPrintingScheduleModel('.$val->id.')" >Schedule</button>';
                            }elseif($val->schedule_date_for_print){
                                $button .= '<button class="btn btn-sm btn-danger" onClick="openPrintingScheduleModel('.$val->id.')" >Re-Schedule</button>';
                            }
                        }elseif($flag=="cutting"){
                            if(!$val->schedule_date_for_cutting){
                                $button .= '<button class="btn btn-sm btn-warning" onClick="openCuttingScheduleModel('.$val->id.')" >Schedule</button>';
                            }elseif($val->schedule_date_for_cutting){
                                $button .= '<button class="btn btn-sm btn-danger" onClick="openCuttingScheduleModel('.$val->id.')" >Re-Schedule</button>';
                            }
                        }
                        return $button;
                    })
                    ->addColumn('print_color', function ($val) {                    
                        return collect(json_decode($val->printing_color,true))->implode(",");
                    })
                    ->addColumn("loop_color",function($val){
                        return"";
                    })
                    ->addColumn("gsm_json",function($val){
                        return $val->gsm_json ? "(".collect(json_decode($val->gsm_json,true))->implode(",").")" : "";                        
                    })
                    ->rawColumns(['row_color', 'action'])
                    ->make(true);
                    // dd(DB::getQueryLog());
                return $list;
    
            }
            $data["flag"]=$flag;
            return view("Roll/schedule",$data);
        }catch(Exception $e){

        }
    }

    public function rollScheduleSet(Request $request){
        try{
            $flag =  $request->flag;
            $rules = [
                "scheduleDate" => "required|date|after_or_equal:" . Carbon::now()->format("Y-m-d"),
                "rolls" => "required|array",
                "rolls.*.id" => "required|exists:" . $this->_M_RollDetail->getTable() . ",id",
                "rolls.*.position" => "required|integer", // Use `integer` for clarity instead of `int`
            ];
            $validate = Validator::make($request->all(),$rules);
            if($validate->fails()){
                return validationError($validate);
            }
            DB::beginTransaction();
            foreach($request->rolls as $roll){
                $newRequest = new Request($roll);
                $newRequest->merge([
                    "roll_id"=>$roll["id"],
                    "cutting_date"=>$request->scheduleDate,
                    "printing_date"=>$request->scheduleDate,
                    "sl"=> $roll["position"],
                ]);
                if($flag=="printing"){
                    $this->_M_PrintingScheduleDetail->where("roll_id",$newRequest->roll_id)->update(["lock_status"=>true]);
                    $this->_M_PrintingScheduleDetail->store($newRequest);
                }elseif($flag=="cutting"){
                    $this->_M_CuttingScheduleDetail->where("roll_id",$newRequest->roll_id)->update(["lock_status"=>true]);
                    $this->_M_CuttingScheduleDetail->store($newRequest);
                }
            }
            DB::commit();
            return responseMsgs(true,"Roll Schedule for ".$flag,"");

        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function rollProduction(Request $request){
        $user_type = Auth()->user()->user_type_id??"";
        $machineId = $request->machineId;
        if($request->ajax())
        {
            // dd($request->ajax());
            $fromDate = $request->fromDate;
            $uptoDate = $request->uptoDate;
            if(!$fromDate){
                $fromDate = Carbon::now()->format("Y-m-d");
            }
            if(!$uptoDate){
                $uptoDate = Carbon::now()->format("Y-m-d");
            }
            $data = $this->_M_RollDetail->select(
                                "roll_details.*","vendor_detail_masters.vendor_name",
                                "client_detail_masters.client_name",
                                "bag_type_masters.bag_type",
                                DB::raw("
                                    TO_CHAR(roll_details.purchase_date, 'DD-MM-YYYY') as purchase_date ,
                                    TO_CHAR(roll_details.estimate_delivery_date, 'DD-MM-YYYY') as estimate_delivery_date ,
                                    TO_CHAR(roll_details.delivery_date, 'DD-MM-YYYY') as delivery_date ,
                                    TO_CHAR(roll_details.printing_date, 'DD-MM-YYYY') as printing_date ,
                                    TO_CHAR(printing_schedule_details.printing_date, 'DD-MM-YYYY') as schedule_date_for_print                                      
                                    ")
                    )
                    ->join("vendor_detail_masters","vendor_detail_masters.id","roll_details.vender_id")
                    ->leftJoin("client_detail_masters","client_detail_masters.id","roll_details.client_detail_id")
                    ->leftJoin("bag_type_masters","bag_type_masters.id","roll_details.bag_type_id")
                    ->Join("printing_schedule_details",function($join){
                        $join->on("printing_schedule_details.roll_id","=","roll_details.id")
                        ->where("printing_schedule_details.lock_status",false);
                    })
                    ->where("roll_details.lock_status",false)
                    ->where("roll_details.is_printed",false)
                    ->orderBy("printing_schedule_details.sl","ASC");
            if($machineId==1){                
                $data->where(function($where){
                    $where->where(DB::raw("json_array_length(roll_details.printing_color)"),"<=",2)
                        ->orWhereNull("roll_details.printing_color");
                });
            }                     

            if($fromDate && $uptoDate){             
                $data->whereBetween("printing_schedule_details.printing_date",[$fromDate,$uptoDate]);
            }

            elseif($fromDate){
                $data->where("printing_schedule_details.printing_date",">=",$fromDate);
            }
            elseif($uptoDate){
                $data->where("printing_schedule_details.printing_date","<=",$uptoDate);
            } 
            $list = DataTables::of($data)
                ->addIndexColumn()                
                ->addColumn('print_color', function ($val) {                    
                    return collect(json_decode($val->printing_color,true))->implode(",");
                })
                ->addColumn("loop_color",function($val){
                    return"";
                })
                ->addColumn("gsm_json",function($val){
                    return $val->gsm_json ? "(".collect(json_decode($val->gsm_json,true))->implode(",").")" : "";                        
                })
                ->addColumn('action', function ($val){                    
                    $button = "";
                    if(!$val->is_cut){
                        $button .= '<button class="btn btn-sm btn-warning" onClick="openPrintingUpdateModel('.$val->id.')" >Update</button>';
                    }                    
                    return $button;
                })
                ->rawColumns(['row_color', 'action'])
                ->make(true);
            return $list;

        }
        $data=[];
        $data["machine"] = $this->_M_Machine->find($machineId);
        $data["operator"] = $this->_M_User->getOperateList();
        $data["helper"] = $this->_M_User->getHelperList();
        $data["privDate"] = Carbon::now()->subDay()->format("Y-m-d");
        return view("Roll/rollProduction",$data);
    }

    public function rollProductionCutting(Request $request){
        $user_type = Auth()->user()->user_type_id??"";
        $machineId = $request->machineId;
        if($request->ajax())
        {
            // dd($request->ajax());
            $fromDate = $request->fromDate;
            $uptoDate = $request->uptoDate;
            $data = $this->_M_RollDetail->select("roll_details.*","vendor_detail_masters.vendor_name",
                                "client_detail_masters.client_name",
                                "bag_type_masters.bag_type",
                                DB::raw("
                                    TO_CHAR(roll_details.purchase_date, 'DD-MM-YYYY') as purchase_date ,
                                    TO_CHAR(roll_details.estimate_delivery_date, 'DD-MM-YYYY') as estimate_delivery_date ,
                                    TO_CHAR(roll_details.delivery_date, 'DD-MM-YYYY') as delivery_date ,
                                    TO_CHAR(roll_details.printing_date, 'DD-MM-YYYY') as printing_date ,
                                    TO_CHAR(cutting_schedule_details.cutting_date, 'DD-MM-YYYY') as schedule_date_for_cutting                                      
                                    ")
                                )
                    ->join("vendor_detail_masters","vendor_detail_masters.id","roll_details.vender_id")
                    ->leftJoin("client_detail_masters","client_detail_masters.id","roll_details.client_detail_id")
                    ->leftJoin("bag_type_masters","bag_type_masters.id","roll_details.bag_type_id")                    
                    ->Join("cutting_schedule_details",function($join){
                        $join->on("cutting_schedule_details.roll_id","=","roll_details.id")
                        ->where("cutting_schedule_details.lock_status",false);
                    })
                    ->where("roll_details.lock_status",false)
                    ->where("roll_details.is_cut",false)
                    ->orderBy("cutting_schedule_details.sl","ASC");                     

            if($fromDate && $uptoDate){             
                $data->whereBetween("cutting_schedule_details.cutting_date",[$fromDate,$uptoDate]);
            }

            elseif($fromDate){
                $data->where("cutting_schedule_details.cutting_date",">=",$fromDate);
            }
            elseif($uptoDate){
                $data->where("cutting_schedule_details.cutting_date","<=",$uptoDate);
            } 

            if($machineId==1){                
                $data->where(function($where){
                    $where->where("roll_details.bag_type_id",5);
                });
            }  
            $list = DataTables::of($data)
                ->addIndexColumn()                
                ->addColumn('print_color', function ($val) {                    
                    return collect(json_decode($val->printing_color,true))->implode(",");
                })
                ->addColumn("loop_color",function($val){
                    return"";
                })
                ->addColumn("gsm_json",function($val){
                    return $val->gsm_json ? "(".collect(json_decode($val->gsm_json,true))->implode(",").")" : "";                        
                })
                ->addColumn('action', function ($val){                    
                    $button = "";
                    if(!$val->is_cut){
                        $button .= '<button class="btn btn-sm btn-warning" onClick="openCuttingUpdateModel('.$val->id.')" >Update</button>';
                    }                    
                    return $button;
                })
                ->rawColumns(['row_color', 'action'])
                ->make(true);
            return $list;

        }
        $data=[];
        $data["machine"] = $this->_M_Machine->find($machineId);
        $data["operator"] = $this->_M_User->getOperateList();
        $data["helper"] = $this->_M_User->getHelperList();
        $data["privDate"] = Carbon::now()->subDay()->format("Y-m-d");
        return view("Roll/rollProductionCutting",$data);
    }

    public function rollSearchPrinting(Request $request){
        try{
            $machineId = $request->machineId;
            $machine = $this->_M_Machine->find($machineId);
            $data = $this->rollSearchPrintingOrm()
                    ->where("roll_details.roll_no",$request->rollNo)
                    ->first();
            $message="Data Fetch";
            if($data && !$data->printing_color){
                $message="Printing is not applicable on this roll";
                $data=null;
            }
            if($data && $machineId==1 && collect(json_decode($data->printing_color,true))->count()>2){
                $message=collect(json_decode($data->printing_color,true))->count()." Colors are not printed on this machine.";
                $data=null; 
            }
            if($data){
                $data->printing_color = collect(json_decode($data->printing_color,true))->implode(",");
            }
            return responseMsgs(true,$message,$data);
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function rollSearchPrintingRollList(Request $request){
        try{
            $machineId = $request->machineId;
            $data = $this->rollSearchPrintingOrm()
                    ->whereNotNull("roll_details.printing_color")
                    ->where("roll_details.roll_no","ILIKE",'%'.$request->rollNo.'%')
                    ->get();
            $data=collect($data);
            if($machineId==1){
                $data = $data->filter(function($val){
                    return collect(json_decode($val->printing_color,true))->count()<=2;
                })->values();
                $data = collect($data);
            }
            return responseMsgs(true,"suggested Roll",$data);
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function rollSearchCutting(Request $request){
        try{
            $data = $this->rollSearchCuttingOrm()
                    ->where("roll_details.roll_no",$request->rollNo)                    
                    ->first();
            if($data){
                $data->printing_color = collect(json_decode($data->printing_color,true))->implode(",");
                $data->operator = $this->_M_User->getOperateList();
                $data->helper = $this->_M_User->getHelperList();
            }
            $message = "Data Fetch";
            if(!$data){
                $roll = $this->_M_RollDetail->where("roll_details.roll_no",$request->rollNo)->first();
                if(!$roll){
                    $message = "Roll is not Fined";
                }
                elseif(!$roll->is_printed && sizeof(json_decode($roll->printing_color,true))>0){
                    $message = "Roll is not Printed";
                }elseif($roll->is_cut){
                    $message = "Roll Already Cut";
                }
            }
            return responseMsgs(true,$message,$data);
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function rollSearchCuttingRollList(Request $request){
        try{
            $data = $this->rollSearchCuttingOrm()
                    ->where("roll_details.roll_no","ILIKE","%".$request->rollNo."%")                    
                    ->get();
                    
            return responseMsgs(true,"suggested Roll",$data);
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function rollPrintingUpdate(Request $request){        
        try{
           
            $rule = [
                "id" => "required|exists:" . $this->_M_Machine->getTable() . ",id,is_printing,true",
                "printingUpdate" => "required|date|date_format:Y-m-d|before_or_equal:" . Carbon::now()->format("Y-m-d"),
                "roll" => "required|array",
                "roll.*.id"=>"required|exists:".$this->_M_RollDetail->getTable().",id,lock_status,false,is_printed,false,is_cut,false",
            ];
            $validate = Validator::make($request->all(),$rule);
            if($validate->fails()){
                return validationError($validate);
            }
            DB::beginTransaction();
            foreach($request->roll as $index=>$val){
                $roll = $this->_M_RollDetail->find($val['id']);
                $roll->is_printed = true;
                $roll->printing_date = $request->printingUpdate;
                $roll->weight_after_print = $val["printingWeight"];
                $roll->printing_machine_id = $request->id;
                $roll->update();
            }
            DB::commit();
            return responseMsgs(true,"Roll No ".$roll->roll_no." Printed","");

        }catch(Exception $e){
            DB::rollBack();
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function rollCuttingUpdate(Request $request){        
        try{
           
            $rule = [
                "id" => "required|exists:" . $this->_M_Machine->getTable() . ",id,is_cutting,true",
                "cuttingUpdate" => "required|date|date_format:Y-m-d|before_or_equal:" . Carbon::now()->format("Y-m-d"),
                "shift" => "required|in:Day,Night",
                "operatorId" => "required|exists:".$this->_M_User->getTable().",id",
                "helperId" => "required|exists:".$this->_M_User->getTable().",id",
                "roll" => "required|array",
                "roll.*.id"=>"required|exists:".$this->_M_RollDetail->getTable().",id,lock_status,false,is_cut,false",
                "roll.*.totalQtr"=>"required",
            ];
            $validate = Validator::make($request->all(),$rule);
            if($validate->fails()){
                return validationError($validate);
            }
            DB::beginTransaction();
            foreach($request->roll as $index=>$val){
                $roll = $this->_M_RollDetail->find($val['id']);
                $roll->is_cut = true;
                $roll->cutting_date = $request->cuttingUpdate;
                $roll->weight_after_cutting = $roll->net_weight - $val["totalQtr"]??0;
                $roll->cutting_machine_id = $request->id;
                $roll->update();
                $newRequest = new Request($request->all());
                $newRequest->merge([
                    "roll_id"=>$roll->id,
                    "total_qtr"=>$val["totalQtr"]??0,
                ]);
                $id = $this->_M_GarbageAcceptRegister->store($newRequest);
            }
            DB::commit();
            return responseMsgs(true,"Roll No ".$roll->roll_no." Printed","");

        }catch(Exception $e){
            DB::rollBack();
            return responseMsgs(false,$e->getMessage(),"");
        }
    }


    public function orderPunches(Request $request){
        $data["clientList"] = $this->_M_ClientDetails->getClientListOrm()->orderBy("client_name","ASC")->get();
        $data["bagType"] = $this->_M_BagType->getBagListOrm()->orderBy("id")->get();
        $data["color"] = $this->_M_Color->getColorListOrm()->orderBy("id")->get();
        $data["rollColor"]=$this->_M_RollColor->getRollColorListOrm()->orderBy("id")->get();
        $data["grade"]=$this->_M_GradeMaster->getGradeListOrm()->orderBy("id")->get();
        $data["fare"]=$this->_M_FareDetail->getFareListOrm()->orderBy("id")->get();
        $data["stereo"]=$this->_M_StereoDetail->getStereoListOrm()->orderBy("id")->get();
        $data["rateType"] = $this->_M_RateTypeMaster->getRateTypeListOrm()->orderBy("id")->get();
        $data["loopColor"] = $this->_M_LoopStock->getLoopColorOrm()->where("balance",">",0)->orderBy("id")->get();
        $data["broker"] = $this->_M_OrderBroker->getBrokerOrm()->orderBy("broker_name","ASC")->get();
        $rollStockGsm = $this->_M_RollDetail->select('gsm')->distinct()->pluck('gsm');
        $rollTransitGsm = $this->_M_RollTransit->select('gsm')->where("gsm",">",2)->distinct()->pluck('gsm');
        $data["gsm"] = $rollStockGsm->union($rollTransitGsm)->unique()->sort()->values();
        $data["altRollColor"]=$data["rollColor"];
        $data["altGsm"]=$data["gsm"];
        return view("Roll/orderPunches",$data);
    }

    public function alternateOptionsColor(Request $request){
        try{  
            if($request->bagQuality=="BOPP"){
                $request->merge(["bagGsm"=>array_sum(explode("/",$request->bagGsmJson))]);
            }         

            $roll= $this->getRollStockORM();
                
            $transit = $this->getRollTransitORM();
            if($request->bagQuality){
                $roll->where("roll_details.roll_type",$request->bagQuality);
                $transit->where("roll_transits.roll_type",$request->bagQuality);
            }
            if($request->gradeId){
                $quality = $this->_M_RollQualityMaster->where("grade_id",$request->gradeId)->get()->pluck("id");
                $roll->whereIn("roll_details.quality_id",$quality);
                $transit->whereIn("roll_transits.quality_id",$quality);
            }
            if($request->bookingBagColor){
                $bag_color = $request->bookingBagColor;
                if(!is_array($request->bookingBagColor))
                {
                    $bag_color=[$request->bookingBagColor];
                }
                $roll->whereNotIn("roll_details.roll_color",$bag_color);
                $transit->whereNotIn("roll_transits.roll_color",$bag_color);
            }
            if($request->bagGsm){
                $bagGsm = $request->bagGsm;
                if(!is_array($request->bagGsm))
                {
                    $bagGsm=[$request->bagGsm];
                }
                $bagGsm = array_map(function($val){
                    return (int) $val;
                },$bagGsm);
                $roll->whereNotIn("roll_details.gsm",$bagGsm);
                $transit->whereNotIn("roll_transits.gsm",$bagGsm);
            }
            
            $bag = $this->_M_BagType->find($request->bookingBagTypeId);
            if($bag){
                $newRequest = new Request();
                $newRequest->merge([
                    "formula"=>$bag->roll_size_find,
                    "bookingBagUnits"=>"M", 
                    "bagL"=> $request->l,
                    "bagW"=> $request->w,
                    "bagG"=> $request->g,
                ]);
                $result = $this->calculatePossibleProduction($newRequest);
                $fromSize = (int)($result["result"]??0);
                $uptoSize = $fromSize+3;
                $roll->whereBetween("roll_details.size",[$fromSize,$uptoSize]);
                $transit->whereBetween("roll_transits.size",[$fromSize,$uptoSize]);
            }
            $rolls = collect($roll->get())->union($transit->get());
            $data["altBagColor"] = collect($rolls->pluck("roll_color")->unique())->sort()->values();
            // $data["altGsm"] = collect($rolls->pluck("gsm")->unique())->sort()->values();
            return responseMsgs(true,"",$data);

        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function alternateOptionsGsm(Request $request){
        try{  
            if($request->bagQuality=="BOPP"){
                $request->merge(["bagGsm"=>array_sum(explode("/",$request->bagGsmJson))]);
            }         

            $roll= $this->getRollStockORM();
                
            $transit = $this->getRollTransitORM();
            if($request->bagQuality){
                $roll->where("roll_details.roll_type",$request->bagQuality);
                $transit->where("roll_transits.roll_type",$request->bagQuality);
            }
            if($request->gradeId){
                $quality = $this->_M_RollQualityMaster->where("grade_id",$request->gradeId)->get()->pluck("id");
                $roll->whereIn("roll_details.quality_id",$quality);
                $transit->whereIn("roll_transits.quality_id",$quality);
            }
            if($request->bookingBagColor){
                $bag_color = $request->bookingBagColor;
                if(!is_array($request->bookingBagColor))
                {
                    $bag_color=[$request->bookingBagColor];
                }
                if($request->altBagColor){
                    $bag_color = array_merge($bag_color,$request->altBagColor);
                }
                $roll->whereIn("roll_details.roll_color",$bag_color);
                $transit->whereIn("roll_transits.roll_color",$bag_color);
            }
            if($request->bagGsm){
                $bagGsm = $request->bagGsm;
                if(!is_array($request->bagGsm))
                {
                    $bagGsm=[$request->bagGsm];
                }
                $bagGsm = array_map(function($val){
                    return (int) $val;
                },$bagGsm);
                $roll->whereNotIn("roll_details.gsm",$bagGsm);
                $transit->whereNotIn("roll_transits.gsm",$bagGsm);
            }
            
            $bag = $this->_M_BagType->find($request->bookingBagTypeId);
            if($bag){
                $newRequest = new Request();
                $newRequest->merge([
                    "formula"=>$bag->roll_size_find,
                    "bookingBagUnits"=>"M", 
                    "bagL"=> $request->l,
                    "bagW"=> $request->w,
                    "bagG"=> $request->g,
                ]);
                $result = $this->calculatePossibleProduction($newRequest);
                $fromSize = (int)($result["result"]??0);
                $uptoSize = $fromSize+3;
                $roll->whereBetween("roll_details.size",[$fromSize,$uptoSize]);
                $transit->whereBetween("roll_transits.size",[$fromSize,$uptoSize]);
            }
            $rolls = collect($roll->get())->union($transit->get());
            $data["altGsm"] = collect($rolls->pluck("gsm")->unique())->sort()->values();
            return responseMsgs(true,"",$data);

        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function oldOrderOfClient(Request $request){
        try{
            // DB::enableQueryLog();
            $roll = $this->_M_OrderPunches
                    ->select(DB::raw("order_punch_details.bag_type_id,
                                    order_punch_details.bag_quality,
                                    order_punch_details.bag_gsm,
                                    order_punch_details.units,
                                    order_punch_details.total_units,
                                    order_punch_details.rate_per_unit,
                                    order_punch_details.bag_w,
                                    order_punch_details.bag_l,
                                    order_punch_details.bag_g,
                                    order_punch_details.bag_loop_color,
                                    order_punch_details.bag_color,
                                    order_punch_details.created_at,
                                    order_punch_details.grade_id,
                                    order_punch_details.rate_type_id,
                                    order_punch_details.fare_type_id,
                                    order_punch_details.stereo_type_id,
                                    bag_type_masters.bag_type,
                                    order_punch_details.bag_printing_color::text
                                      ")
                    )
                    ->join("bag_type_masters","bag_type_masters.id","order_punch_details.bag_type_id")
                    ->where("order_punch_details.client_detail_id",$request->clientId)
                    ->where("order_punch_details.lock_status",false)
                    ->orderBy("order_punch_details.created_at","DESC")
                    ->get();

            // Remove duplicates based on the specified columns
            $roll = $roll->unique(function ($item) {
                return implode('|', [
                    $item->bag_type_id, $item->bag_quality, $item->bag_gsm, $item->units, 
                    $item->total_units, $item->rate_per_unit, $item->bag_w, $item->bag_l, 
                    $item->bag_g, $item->bag_loop_color, $item->bag_color, $item->bag_type,
                    $item->grade_id,$item->rate_type_id,$item->fare_type_id,$item->stereo_type_id,
                    $item->bag_printing_color,
                ]);
            })->values();

                    // dd(DB::getQueryLog());
            return responseMsgs(true,"old history",$roll);
            

        }catch(ExcelExcel $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function orderSuggestionClient(Request $request){
        try{
            if($request->bagQuality=="BOPP"){
                $request->merge(["bagGsm"=>array_sum(explode("/",$request->bagGsmJson))]);
            }
            if($request->bookingBagTypeId==3 && $request->totalUnits){
                $request->merge(["totalUnits"=> $request->totalUnits + ($request->totalUnits * 0.12)]);
            }
            $bag = $this->_M_BagType->find($request->bookingBagTypeId);
            $roll=$this->_M_RollDetail->select("roll_details.*",DB::raw("'stock' as stock, client_detail_masters.client_name,vendor_detail_masters.vendor_name,roll_quality_masters.quality"))
                    ->leftJoin("client_detail_masters","client_detail_masters.id","roll_details.client_detail_id")
                    ->leftJoin("vendor_detail_masters","vendor_detail_masters.id","roll_details.vender_id")
                    ->leftJoin("roll_quality_masters","roll_quality_masters.id","roll_details.quality_id")
                    ->where("roll_details.is_cut",false)
                    ->where("roll_details.is_printed",false)
                    ->where("roll_details.lock_status",false);
                    
            $transit = $this->_M_RollTransit->select("roll_transits.*",DB::raw("'transit' as stock, client_detail_masters.client_name,vendor_detail_masters.vendor_name,roll_quality_masters.quality"))
                        ->leftJoin("client_detail_masters","client_detail_masters.id","roll_transits.client_detail_id")
                        ->leftJoin("vendor_detail_masters","vendor_detail_masters.id","roll_transits.vender_id")
                        ->leftJoin("roll_quality_masters","roll_quality_masters.id","roll_transits.quality_id")
                        ->where("roll_transits.is_cut",false)
                        ->where("roll_transits.is_printed",false)
                        ->where("roll_transits.lock_status",false);
                        
            if($request->bagQuality){
                $roll->where("roll_details.roll_type",$request->bagQuality);
                $transit->where("roll_transits.roll_type",$request->bagQuality);
            }
            if($request->bagGsm){
                $bagGsm = $request->bagGsm;
                if(!is_array($request->bagGsm))
                {
                    $bagGsm=[$request->bagGsm];
                }
                if($request->altBagGsm){
                    $bagGsm = array_merge($bagGsm,$request->altBagGsm);
                }
                $bagGsm = array_map(function($val){
                    return (int) $val;
                },$bagGsm);
                $roll->whereIn("roll_details.gsm",$bagGsm);
                $transit->whereIn("roll_transits.gsm",$bagGsm);
            }
            if($request->bookingBagColor){
                $bag_color = $request->bookingBagColor;
                if(!is_array($request->bookingBagColor))
                {
                    $bag_color=[$request->bookingBagColor];
                }
                if($request->altBagColor){
                    $bag_color = array_merge($bag_color,$request->altBagColor);
                }
                $roll->whereIn("roll_details.roll_color",$bag_color);
                $transit->whereIn("roll_transits.roll_color",$bag_color);
            }
            if($request->gradeId){
                $quality = $this->_M_RollQualityMaster->where("grade_id",$request->gradeId)->get()->pluck("id");
                $roll->whereIn("roll_details.quality_id",$quality);
                $transit->whereIn("roll_transits.quality_id",$quality);
            }
            if($bag){
                $newRequest = new Request();
                $newRequest->merge([
                    "formula"=>$bag->roll_size_find,
                    "bookingBagUnits"=>"M",                    
                    // "gsm" => $request->bagGsm,
                    "bagL"=> $request->l,
                    "bagW"=> $request->w,
                    "bagG"=> $request->g,
                ]);
                $result = $this->calculatePossibleProduction($newRequest);
                $fromSize = (int)($result["result"]??0);
                $uptoSize = $fromSize+3;
                $roll->whereBetween("roll_details.size",[$fromSize,$uptoSize]);
                $transit->whereBetween("roll_transits.size",[$fromSize,$uptoSize]);
            }

            $roll= $roll->get();
            $transit = $transit->get();
            if($request->bookingBagTypeId && $request->totalUnits && $request->bookingBagUnits){
                $bestFind = "";
                $bestFind2 = ""; 
                if($request->bookingBagUnits=="Kg"){
                    $bestFind = "RW";
                    $bestFind2 = "RW";
                }elseif($request->bookingBagUnits=="Piece"){
                    $bestFind = $bag->roll_find;                    
                    $bestFind2 = $bag->roll_find_as_weight;
                }
                $roll = $roll->map(function($val)use($request,$bestFind,$bestFind2){
                    $newRequest = new Request($val->toArray());
                    $newRequest->merge([
                        "formula"=>$bestFind,
                        "bookingBagUnits"=>$request->bookingBagUnits,
                        "length" => $val->length,
                        "netWeight" => $val->net_weight,
                        "size" => $val->size,
                        "gsm" => $val->gsm,
                        "bagL"=> $request->l,
                        "bagW"=> $request->w,
                        "bagG"=> $request->g,
                    ]);
                    $newRequest2 = new Request($newRequest->all());
                    $newRequest2->merge([
                        "formula"=>$bestFind2
                    ]);
                    $result = $this->calculatePossibleProduction($newRequest);
                    $result1 = $this->calculatePossibleProduction($newRequest);
                    $avg = round((($result["result"]??0)+($result1["result"]??0))/2);
                    $val->result = $avg; 
                    $val->unit =  $avg." ".$request->bookingBagUnits; 
                    return $val;
                })->where("result","<=",$request->totalUnits)
                ->sortByDesc(function ($item) {
                    return [$item['result'], $item['size']];
                });

                $transit = $transit->map(function($val)use($request,$bestFind,$bestFind2){
                    $newRequest = new Request($val->toArray());
                    $newRequest->merge([
                        "formula"=>$bestFind,
                        "bookingBagUnits"=>$request->bookingBagUnits,
                        "length" => $val->length,
                        "netWeight" => $val->net_weight,
                        "size" => $val->size,
                        "gsm" => $val->gsm,
                        "bagL"=> $request->l,
                        "bagW"=> $request->w,
                        "bagG"=> $request->g,
                    ]);
                    $newRequest2 = new Request($newRequest->all());
                    $newRequest2->merge([
                        "formula"=>$bestFind2
                    ]);
                    $result = $this->calculatePossibleProduction($newRequest);
                    $result1 = $this->calculatePossibleProduction($newRequest2);
                    $avg = round((($result["result"]??0)+($result1["result"]??0))/2);
                    $val->result = $avg; 
                    $val->unit =  $avg." ".$request->bookingBagUnits; 
                    return $val;
                })->where("result","<=",$request->totalUnits)
                ->sortByDesc(function ($item) {
                    return [$item['result'], $item['size']];
                });
            }
                        
            $data["roll"]= collect($roll->values());
            $data["rollTransit"]= collect($transit->values());
            $data["size"] = [$fromSize,$uptoSize];
            // dd($request->all());
            return responseMsgs(true,"data Fetched",$data);
        }catch(ExcelExcel $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function orderPunchesSave(Request $request){
        try{
            $request->merge([
                "clientDetailId"=>$request->bookingForClientId,
                "estimateDeliveryDate"=>$request->bookingEstimatedDespatchDate,
                "bagTypeId"=>$request->bookingBagTypeId,
                "units"=>$request->bookingBagUnits,
                "bag_w"=>$request->w,
                "bag_l"=>$request->l,
                "bag_g"=>$request->g,
                "bag_loop_color"=>$request->looColor,
                "bag_color"=>$request->bookingBagColor,
                "bag_printing_color"=>$request->bookingPrintingColor,
            ]); 
            if($request->bagQuality!="BOPP"){
                $request->merge(["bagGsmJson"=>null]);
            }
            if($request->bagQuality=="BOPP"){
                $request->merge(["bagGsm"=>array_sum(explode("/",$request->bagGsmJson))]);
                $request->merge(["bagGsmJson"=>(explode("/",$request->bagGsmJson))]);
            }

            DB::beginTransaction();
            if($request->id){
                $orderId = $request->id;
            }else{
                $orderId = $this->_M_OrderPunches->store($request);
            }
            $orderNew = $this->_M_OrderPunches->find($orderId);
            $type ="Pending";
            $bookOrders = 0;
            if($request->roll){
                $type ="Booked";
                foreach($request->roll as $val){
                    $roll = $this->_M_RollDetail->find($val["id"]);
                    if(!$roll){
                        $roll = $this->_M_RollTransit->find($val["id"]);
                    }
                    if($roll->client_detail_id){
                        $orderRoll = $this->_M_OrderRollBagType->where("roll_id",$roll->id)->where("lock_status",false)->first();
                        if($orderRoll){
                            $order = $this->_M_OrderPunches->find($orderRoll->order_id);
                            $bag = $this->_M_BagType->find($order->bag_type_id);
                            $bestFind = "";
                            $bestFind2 ="";
                            if($order->units=="Kg"){
                                $bestFind = "RW";
                                $bestFind2 = "RW";
                            }elseif($order->units=="Piece"){
                                $bestFind = $bag->roll_find;
                                $bestFind2 = $bag->roll_find_as_weight;
                            }
                            $newRequest = new Request();
                            $newRequest->merge(
                                [
                                "bookingBagUnits" => $order->units,
                                "formula" => $bestFind,
                                "length" => $roll->length,
                                "netWeight"=>$roll->net_weight,
                                "size"=>$roll->size,
                                "gsm"=>$roll->gsm,

                                "bagL"=>$order->bag_l,
                                "bagW"=>$order->bag_w,
                                "bagG"=>$order->bag_g
                                ]
                            );
                            $newRequest2 = new Request($newRequest->all());
                            $newRequest2->merge([
                                "formula"=>$bestFind2
                            ]);
                            $result = $this->calculatePossibleProduction($newRequest);
                            $result2 = $this->calculatePossibleProduction($newRequest2);
                            $qty = ((($result["result"]??0)+($result2["result"]??0))/2);

                            $newRequest->merge([
                                "formula" => $bag->roll_find,
                            ]);
                            $newRequest2->merge([
                                "formula" => $bag->roll_find_as_weight,
                            ]);
                            $pieces = $this->calculatePossibleProduction($newRequest);
                            $pieces2 = $this->calculatePossibleProduction($newRequest2);
                            $totalPiece = ((($pieces["result"]??0)+($pieces2["result"]??0))/2); 
                            $totalLoopWeight = (($totalPiece*3.4)/1000);
                            if(in_array($bag->id,[2,4])){
                                $loopStock = $this->_M_LoopStock->where("loop_color",$order->bag_loop_color)->first();

                                $newLoopAccRequest = new Request(
                                    [
                                        "loop_stock_id"=>$loopStock->id,
                                        "roll_id"=>$roll->id,
                                        "order_id"=>$order->id,
                                        "description"=>"Roll Remove From Booking",
                                        "opening_balance"=>$loopStock->balance,
                                        "credit"=>0,
                                        "debit"=>$totalLoopWeight,
                                        "balance"=>$loopStock->balance +  $totalLoopWeight,
                                        "user_id"=>Auth()->user()->id
                                    ]
                                );
                                $this->_M_LoopAccount->store($newLoopAccRequest);

                                $loopStock->balance = $loopStock->balance + $totalLoopWeight;
                                $loopStock->update();
                            }
                            $order->booked_units = $order->booked_units - $qty;
                            $orderRoll->lock_status=true;
                            $order->update();
                            $orderRoll->update();
                        }                        
                    }
                    $roll->client_detail_id = $orderNew->client_detail_id;
                    $roll->estimate_delivery_date = $orderNew->estimate_delivery_date;
                    $roll->bag_type_id = $orderNew->bag_type_id;
                    $roll->bag_unit = $orderNew->units;
                    $roll->loop_color = $orderNew->bag_loop_color;
                    $roll->w = $orderNew->bag_w;
                    $roll->l = $orderNew->bag_l;
                    $roll->g = $orderNew->bag_g;
                    $roll->printing_color = $orderNew->bag_printing_color?json_decode($orderNew->bag_printing_color,true):null;
                    $roll->update();
                    $newRequest = new Request($roll->toArray());
                    $newRequest->merge(["order_id"=>$orderId,"roll_id"=>$roll->id]);
                    $this->_M_OrderRollBagType->store($newRequest);

                    $bag = $this->_M_BagType->find($orderNew->bag_type_id);
                    $formula = "";
                    $formula2 = "";
                    if($orderNew->units=="Kg"){
                        $formula = "RW";
                        $formula2 = "RW";
                    }elseif($orderNew->units=="Piece"){
                        $formula = $bag->roll_find;
                        $formula2 = $bag->roll_find_as_weight;
                    }
                    $newRequest = new Request();
                    $newRequest->merge(
                        [
                        "bookingBagUnits" => $orderNew->units,
                        "formula" => $formula,
                        "length" => $roll->length,
                        "netWeight"=>$roll->net_weight,
                        "size"=>$roll->size,
                        "gsm"=>$roll->gsm,

                        "bagL"=>$orderNew->bag_l,
                        "bagW"=>$orderNew->bag_w,
                        "bagG"=>$orderNew->bag_g
                        ]
                    );
                    $newRequest2 = new Request($newRequest->all());
                    $newRequest2->merge([
                        "formula" => $formula2,
                    ]); 
                    $result = $this->calculatePossibleProduction($newRequest);
                    $result2 = $this->calculatePossibleProduction($newRequest2);
                    $bookOrders += ((($result["result"]??0)+($result2["result"]??0))/2); 

                    $newRequest->merge([
                        "formula" => $bag->roll_find,
                    ]);
                    $newRequest2->merge([
                        "formula" => $bag->roll_find_as_weight,
                    ]);
                    $pieces = $this->calculatePossibleProduction($newRequest);
                    $pieces2 = $this->calculatePossibleProduction($newRequest2);
                    $totalPiece = ((($pieces["result"]??0)+($pieces2["result"]??0))/2); 
                    $totalLoopWeight = (($totalPiece*3.4)/1000);
                    if(in_array($bag->id,[2,4])){
                        $loopStock = $this->_M_LoopStock->where("loop_color",$orderNew->bag_loop_color)->first();

                        $newLoopAccRequest = new Request(
                            [
                                "loop_stock_id"=>$loopStock->id,
                                "roll_id"=>$roll->id,
                                "order_id"=>$orderNew->id,
                                "description"=>"Roll Add To Booking",
                                "opening_balance"=>$loopStock->balance,
                                "credit"=>$totalLoopWeight,
                                "debit"=>0,
                                "balance"=>$loopStock->balance -  $totalLoopWeight,
                                "user_id"=>Auth()->user()->id
                            ]
                        );
                        $this->_M_LoopAccount->store($newLoopAccRequest);
                        $loopStock->balance = $loopStock->balance - $totalLoopWeight;
                        $loopStock->update();
                    }
                } 
                $orderNew = $this->_M_OrderPunches->find($orderId);               
                $orderNew->booked_units = $orderNew->booked_units+$bookOrders;
            }
            $orderNew->update();
            DB::commit();
            return responseMsgs(true,"Order Place On $type","");
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function orderPunchesEdit(Request $request){
        $order = $this->_M_OrderPunches->find($request->orderId);
        if(!$order){
            flashToast("message","Data not found");
            return redirect()->back();
        }
        if($order->deceived_by){
            flashToast("message","Data Order is Delved");
            return redirect()->back();
        }
        if($request->post()){
            try{

                $request->merge([
                    "estimateDeliveryDate"=>$request->bookingEstimatedDespatchDate,
                    "bagTypeId"=>$request->bookingBagTypeId,
                    "units"=>$request->bookingBagUnits,
                    "bag_w"=>$request->w,
                    "bag_l"=>$request->l,
                    "bag_g"=>$request->g,
                    "bag_loop_color"=>$request->looColor,
                    "bag_color"=>$request->bookingBagColor,
                    "bag_printing_color"=>$request->bookingPrintingColor,
                ]); 
                if($request->bagQuality!="BOPP"){
                    $request->merge(["bagGsmJson"=>null]);
                }
                if($request->bagQuality=="BOPP"){
                    $request->merge(["bagGsm"=>[array_sum(explode("/",$request->bagGsmJson))]]);
                    $request->merge(["bagGsmJson"=>(explode("/",$request->bagGsmJson))]);
                }
                $request->merge(["id"=>$order->id]);
                DB::beginTransaction();
                $this->_M_OrderPunches->edit($request);
                DB::commit();
                flashToast("message","Order Updated");
                return redirect()->route('order.unbook');
            }catch (Exception $e) {
                return redirect()->back()->withErrors(['error' => 'Order update failed.']);
            }
            

        }
        $order->bag_gsm_json = $order->bag_gsm_json ? implode("/",json_decode($order->bag_gsm_json,true)) : $order->bag_gsm_json;
        $order->bag_printing_color = json_decode($order->bag_printing_color,true);
        $order->bag_color = json_decode($order->bag_color,true);
        $order->bag_gsm = collect(json_decode($order->bag_gsm,true))->map(function($val){
            return (int) $val;
        })->toArray();
        $data["prevUrl"] = url()->previous();
        $data["order"] = $order;
        $data["clientList"] = $this->_M_ClientDetails->getClientListOrm()->orderBy("client_name","ASC")->get();
        $data["bagType"] = $this->_M_BagType->getBagListOrm()->orderBy("id")->get();
        $data["color"] = $this->_M_Color->getColorListOrm()->orderBy("id")->get();
        $data["rollColor"]=$this->_M_RollColor->getRollColorListOrm()->orderBy("id")->get();
        $data["grade"]=$this->_M_GradeMaster->getGradeListOrm()->orderBy("id")->get();
        $data["fare"]=$this->_M_FareDetail->getFareListOrm()->orderBy("id")->get();
        $data["stereo"]=$this->_M_StereoDetail->getStereoListOrm()->orderBy("id")->get();
        $data["rateType"] = $this->_M_RateTypeMaster->getRateTypeListOrm()->orderBy("id")->get();
        $data["loopColor"] = $this->_M_LoopStock->getLoopColorOrm()->where("balance",">",0)->orderBy("id")->get();
        $data["broker"] = $this->_M_OrderBroker->getBrokerOrm()->orderBy("broker_name","ASC")->get();
        $rollStockGsm = $this->_M_RollDetail->select('gsm')->distinct()->pluck('gsm');
        $rollTransitGsm = $this->_M_RollTransit->select('gsm')->where("gsm",">",2)->distinct()->pluck('gsm');
        $data["gsm"] = $rollStockGsm->union($rollTransitGsm)->unique()->sort()->values();
        return view("Roll/orderEdit",$data);
    }

    public function bookedOrder_old1(Request $request){
        
        if($request->ajax())
        {
            // dd($request->ajax());
            $fromDate = $request->fromDate;
            $uptoDate = $request->uptoDate;
            $orderNo = $request->orderNo;            
            $data = $this->_M_OrderPunches
                    ->select(
                                "order_punch_details.*","order_roll_bag_types.*",
                                "client_detail_masters.client_name",                                
                    )
                    ->join(
                        DB::raw("(
                            SELECT *
                            FROM(
                                    (
                                        SELECT order_roll_bag_types.order_id, STRING_AGG(roll_details.roll_no,' , ') as roll_no , 
                                            STRING_AGG(bag_type_masters.bag_type,' , ') as bag_type, 
                                            STRING_AGG(roll_details.bag_unit,' , ') as bag_unit, 
                                            STRING_AGG(roll_details.printing_color,' , ') AS printing_color
                                        FROM order_roll_bag_types
                                        JOIN (
                                            SELECT 
                                                roll_details.id, roll_details.roll_no, roll_details.bag_type_id, roll_details.bag_unit,
                                                '(' || STRING_AGG(jsonb_element.value, ', ') || ')' AS printing_color
                                            FROM roll_details
                                            LEFT JOIN LATERAL jsonb_array_elements_text(roll_details.printing_color::jsonb) AS jsonb_element(value) ON TRUE
                                            GROUP BY roll_details.id
                                        ) as roll_details on roll_details.id = order_roll_bag_types.roll_id
                                        JOIN bag_type_masters on bag_type_masters.id = roll_details.bag_type_id
                                        GROUP BY order_roll_bag_types.order_id
                                    )
                                    UNION ALL(
                                        SELECT order_roll_bag_types.order_id, STRING_AGG(roll_transits.roll_no,' , ') as roll_no , 
                                            STRING_AGG(bag_type_masters.bag_type,' , ') as bag_type, 
                                            STRING_AGG(roll_transits.bag_unit,' , ') as bag_unit, 
                                            STRING_AGG(roll_transits.printing_color,' , ') AS printing_color
                                        FROM order_roll_bag_types
                                        JOIN (
                                            SELECT 
                                                roll_transits.id, roll_transits.roll_no, roll_transits.bag_type_id, roll_transits.bag_unit,
                                                '(' || STRING_AGG(jsonb_element.value, ', ') || ')' AS printing_color
                                            FROM roll_transits
                                            LEFT JOIN LATERAL jsonb_array_elements_text(roll_transits.printing_color::jsonb) AS jsonb_element(value) ON TRUE
                                            GROUP BY roll_transits.id
                                        ) as roll_transits on roll_transits.id = order_roll_bag_types.roll_id
                                        JOIN bag_type_masters on bag_type_masters.id = roll_transits.bag_type_id
                                        GROUP BY order_roll_bag_types.order_id
                                    )
                            )
                        ) AS order_roll_bag_types"),
                        "order_roll_bag_types.order_id","order_punch_details.id"
                    )                
                    ->leftJoin("client_detail_masters","client_detail_masters.id","order_punch_details.client_detail_id")                   
                    ->where("order_punch_details.lock_status",false)
                    ->orderBy("order_punch_details.created_at","ASC");                               

            if($fromDate && $uptoDate){             
                $data->whereBetween(DB::raw("order_punch_details.created_at::date"),[$fromDate,$uptoDate]);
            }

            elseif($fromDate){
                $data->where(DB::raw("order_punch_details.created_at::date"),">=",$fromDate);
            }
            elseif($uptoDate){
                $data->where(DB::raw("order_punch_details.created_at::date"),"<=",$uptoDate);
            } 

            if($orderNo){
                $data->where("order_punch_details.order_no",$orderNo);
            }
            // DB::enableQueryLog();
            // $data->get();
            // dd(DB::getQueryLog());            
            $list = DataTables::of($data)
                ->addIndexColumn()                
                ->addColumn('is_delivered', function ($val) {                    
                    return $val->is_delivered ? "YES" : "NO";
                })
                ->addColumn('created_at', function ($val) {                    
                    return $val->created_at ? Carbon::parse($val->created_at)->format("d-m-Y") : "";                    
                })
                ->addColumn('estimate_delivery_date', function ($val) {                    
                    return $val->estimate_delivery_date ? Carbon::parse($val->estimate_delivery_date)->format("d-m-Y") : "";                    
                })
                ->addColumn('delivery_date', function ($val) {                    
                    return $val->delivery_date ? Carbon::parse($val->delivery_date)->format("d-m-Y") : "";                    
                })
                ->make(true);
            return $list;

        }
        return view("Roll/bookedOrder");
    }

    public function bookedOrder(Request $request){
        if($request->ajax())
        {
            $fromDate = $request->fromDate;
            $uptoDate = $request->uptoDate;
            $orderNo = $request->orderNo;            
            $data = $this->_M_OrderPunches
                    ->select(
                                "order_punch_details.*","order_roll_bag_types.*",
                                "client_detail_masters.client_name",  
                                "bag_type_masters.bag_type" ,                       
                    )
                    ->join(
                        DB::raw("(
                            SELECT *
                            FROM(
                                    SELECT order_id, STRING_AGG(roll_no,' , ') as roll_no 
                                    FROM(
                                        (
                                            SELECT order_roll_bag_types.order_id, STRING_AGG(roll_details.roll_no,' , ') as roll_no 
                                            FROM order_roll_bag_types
                                            JOIN roll_details on roll_details.id = order_roll_bag_types.roll_id
                                            WHERE order_roll_bag_types.lock_status = false
                                            GROUP BY order_roll_bag_types.order_id
                                        )
                                        UNION ALL(
                                            SELECT order_roll_bag_types.order_id, STRING_AGG(roll_transits.roll_no,' , ') as roll_no 
                                            FROM order_roll_bag_types
                                            JOIN roll_transits on roll_transits.id = order_roll_bag_types.roll_id
                                            WHERE order_roll_bag_types.lock_status = false
                                            GROUP BY order_roll_bag_types.order_id
                                        )
                                    ) AS orders
                                    GROUP BY order_id 
                            )
                        ) AS order_roll_bag_types"),
                        "order_roll_bag_types.order_id","order_punch_details.id"
                    )                
                    ->leftJoin("client_detail_masters","client_detail_masters.id","order_punch_details.client_detail_id") 
                    ->leftJoin("bag_type_masters","bag_type_masters.id","order_punch_details.bag_type_id")                   
                    ->where("order_punch_details.lock_status",false)
                    ->where("order_punch_details.is_delivered",false)
                    ->where("order_punch_details.booked_units",">",0)
                    // ->where(function($where){
                    //     $where->where(DB::raw("order_punch_details.total_units"),"<=",DB::raw("order_punch_details.booked_units + order_punch_details.disbursed_units"));
                    // })
                    ->orderBy("order_punch_details.estimate_delivery_date","ASC");                               

            if($fromDate && $uptoDate){             
                $data->whereBetween(DB::raw("order_punch_details.created_at::date"),[$fromDate,$uptoDate]);
            }

            elseif($fromDate){
                $data->where(DB::raw("order_punch_details.created_at::date"),">=",$fromDate);
            }
            elseif($uptoDate){
                $data->where(DB::raw("order_punch_details.created_at::date"),"<=",$uptoDate);
            } 

            if($orderNo){
                $data->where("order_punch_details.order_no",$orderNo);
            }         
            $list = DataTables::of($data)
                ->addIndexColumn()                
                ->addColumn('is_delivered', function ($val) {                    
                    return $val->is_delivered ? "YES" : "NO";
                })
                ->addColumn("bag_printing_color",function($val){
                    return $val->bag_printing_color ? collect(json_decode($val->bag_printing_color,true))->implode(",") : "";
                })
                ->addColumn("bag_color",function($val){
                    return collect(json_decode($val->bag_color,true))->implode(", ") ;
                })
                ->addColumn("bag_gsm",function($val){
                    return collect(json_decode($val->bag_gsm,true))->implode(", ") ;
                })
                ->addColumn("total_units",function($val){
                    return round($val->total_units);
                })
                ->addColumn("booked_units",function($val){
                    return round($val->booked_units);
                })
                ->addColumn("bag_size",function($val){
                    return (float)$val->bag_w." x ".(float)$val->bag_l.($val->bag_g ?(" x ".(float)$val->bag_g) :"") ;
                })
                ->addColumn('created_at', function ($val) {                    
                    return $val->created_at ? Carbon::parse($val->created_at)->format("d-m-Y") : "";                    
                })
                ->addColumn('estimate_delivery_date', function ($val) {                    
                    return $val->estimate_delivery_date ? Carbon::parse($val->estimate_delivery_date)->format("d-m-Y") : "";                    
                })
                ->addColumn('delivery_date', function ($val) {                    
                    return $val->delivery_date ? Carbon::parse($val->delivery_date)->format("d-m-Y") : "";                    
                })
                ->make(true);
            return $list;

        }
        return view("Roll/bookedOrder");
    }

    public function bookedOrderDelivered(Request $request){
        if($request->ajax())
        {
            $fromDate = $request->fromDate;
            $uptoDate = $request->uptoDate;
            $orderNo = $request->orderNo;            
            $data = $this->_M_OrderPunches
                    ->select(
                                "order_punch_details.*","order_roll_bag_types.*",
                                "client_detail_masters.client_name",  
                                "bag_type_masters.bag_type" ,                       
                    )
                    ->join(
                        DB::raw("(
                            SELECT *
                            FROM(
                                    SELECT order_id, STRING_AGG(roll_no,' , ') as roll_no 
                                    FROM(
                                        (
                                            SELECT order_roll_bag_types.order_id, STRING_AGG(roll_details.roll_no,' , ') as roll_no 
                                            FROM order_roll_bag_types
                                            JOIN roll_details on roll_details.id = order_roll_bag_types.roll_id
                                            WHERE order_roll_bag_types.lock_status = false
                                            GROUP BY order_roll_bag_types.order_id
                                        )
                                        UNION ALL(
                                            SELECT order_roll_bag_types.order_id, STRING_AGG(roll_transits.roll_no,' , ') as roll_no 
                                            FROM order_roll_bag_types
                                            JOIN roll_transits on roll_transits.id = order_roll_bag_types.roll_id
                                            WHERE order_roll_bag_types.lock_status = false
                                            GROUP BY order_roll_bag_types.order_id
                                        )
                                    ) AS orders
                                    GROUP BY order_id 
                            )
                        ) AS order_roll_bag_types"),
                        "order_roll_bag_types.order_id","order_punch_details.id"
                    )                
                    ->leftJoin("client_detail_masters","client_detail_masters.id","order_punch_details.client_detail_id") 
                    ->leftJoin("bag_type_masters","bag_type_masters.id","order_punch_details.bag_type_id")                   
                    ->where("order_punch_details.lock_status",false)
                    ->where("order_punch_details.is_delivered",true)
                    ->orderBy("order_punch_details.created_at","DESC");                               

            if($fromDate && $uptoDate){             
                $data->whereBetween(DB::raw("order_punch_details.created_at::date"),[$fromDate,$uptoDate]);
            }

            elseif($fromDate){
                $data->where(DB::raw("order_punch_details.created_at::date"),">=",$fromDate);
            }
            elseif($uptoDate){
                $data->where(DB::raw("order_punch_details.created_at::date"),"<=",$uptoDate);
            } 

            if($orderNo){
                $data->where("order_punch_details.order_no",$orderNo);
            }         
            $list = DataTables::of($data)
                ->addIndexColumn()                
                ->addColumn('is_delivered', function ($val) {                    
                    return $val->is_delivered ? "YES" : "NO";
                })
                ->addColumn("bag_color",function($val){
                    return $val->bag_color ? collect(json_decode($val->bag_color,true))->implode(",") : "";
                })
                ->addColumn("bag_color",function($val){
                    return collect(json_decode($val->bag_color,true))->implode(", ") ;
                })
                ->addColumn("bag_gsm",function($val){
                    return collect(json_decode($val->bag_gsm,true))->implode(", ") ;
                })
                ->addColumn("total_units",function($val){
                    return round($val->total_units);
                })
                ->addColumn("booked_units",function($val){
                    return round($val->booked_units);
                })
                ->addColumn("bag_size",function($val){
                    return (float)$val->bag_w." x ".(float)$val->bag_l.($val->bag_g ?(" x ".(float)$val->bag_g) :"") ;
                })
                ->addColumn('created_at', function ($val) {                    
                    return $val->created_at ? Carbon::parse($val->created_at)->format("d-m-Y") : "";                    
                })
                ->addColumn('estimate_delivery_date', function ($val) {                    
                    return $val->estimate_delivery_date ? Carbon::parse($val->estimate_delivery_date)->format("d-m-Y") : "";                    
                })
                ->addColumn('delivery_date', function ($val) {                    
                    return $val->delivery_date ? Carbon::parse($val->delivery_date)->format("d-m-Y") : Carbon::parse($val->updated_at)->format("d-m-Y") ;                    
                })
                ->make(true);
            return $list;

        }
        return view("Roll/bookedOrderDelivered");
    }

    public function unBookedOrder_old1(Request $request){
        
        if($request->ajax())
        {
            // dd($request->ajax());
            $fromDate = $request->fromDate;
            $uptoDate = $request->uptoDate;
            $orderNo = $request->orderNo;            
            $data = $this->_M_OrderPunches
                    ->select(
                                "order_punch_details.*","pending_order_bag_types.*",
                                "client_detail_masters.client_name",
                                
                    )
                    ->join(
                        DB::raw("(
                            SELECT pending_order_bag_types.order_id, 
                                STRING_AGG( DISTINCT(bag_type_masters.bag_type),' , ') as bag_type, 
		                        STRING_AGG( DISTINCT(pending_order_bag_types.bag_unit),' , ') as bag_unit, 
                                '(' || STRING_AGG(jsonb_element.value, ', ') || ')' AS printing_color
                            FROM pending_order_bag_types
                            JOIN bag_type_masters on bag_type_masters.id = pending_order_bag_types.bag_type_id
                            LEFT JOIN LATERAL jsonb_array_elements_text(pending_order_bag_types.printing_color::jsonb) AS jsonb_element(value) ON TRUE
                            GROUP BY pending_order_bag_types.order_id
                        ) AS pending_order_bag_types")
                        ,"pending_order_bag_types.order_id","order_punch_details.id")
                    ->leftJoin("order_roll_bag_types", "order_roll_bag_types.order_id","order_punch_details.id")                
                    ->leftJoin("client_detail_masters","client_detail_masters.id","order_punch_details.client_detail_id")                    
                    ->where("order_punch_details.lock_status",false)
                    ->whereNull("order_roll_bag_types.order_id")
                    ->orderBy("order_punch_details.created_at","ASC");                               

            if($fromDate && $uptoDate){             
                $data->whereBetween(DB::raw("order_punch_details.created_at::date"),[$fromDate,$uptoDate]);
            }

            elseif($fromDate){
                $data->where(DB::raw("order_punch_details.created_at::date"),">=",$fromDate);
            }
            elseif($uptoDate){
                $data->where(DB::raw("order_punch_details.created_at::date"),"<=",$uptoDate);
            } 

            if($orderNo){
                $data->where("order_punch_details.order_no",$orderNo);
            }
            $list = DataTables::of($data)
                ->addIndexColumn()                
                ->addColumn('is_delivered', function ($val) {                    
                    return $val->is_delivered ? "YES" : "NO";
                })
                ->addColumn('created_at', function ($val) {                    
                    return $val->created_at ? Carbon::parse($val->created_at)->format("d-m-Y") : "";                    
                })
                ->addColumn('estimate_delivery_date', function ($val) {                    
                    return $val->estimate_delivery_date ? Carbon::parse($val->estimate_delivery_date)->format("d-m-Y") : "";                    
                })
                ->addColumn("roll_no",function($val){
                    return "";
                })
                ->addColumn('delivery_date', function ($val) {                    
                    return $val->delivery_date ? Carbon::parse($val->delivery_date)->format("d-m-Y") : "";                    
                })
                ->make(true);
            return $list;

        }
        return view("Roll/unBookedOrder");
    }

    public function unBookedOrder(Request $request){
        
        if($request->ajax())
        {
            $fromDate = $request->fromDate;
            $uptoDate = $request->uptoDate;
            $orderNo = $request->orderNo;            
            $data = $this->_M_OrderPunches
                    ->select(
                                "order_punch_details.*","order_roll_bag_types.*",
                                "client_detail_masters.client_name",  
                                "bag_type_masters.bag_type" , 
                                "grade_masters.grade" ,                       
                    )
                    ->leftJoin(
                        DB::raw("(
                            SELECT *
                            FROM(
                                    SELECT order_id, STRING_AGG(roll_no,' , ') as roll_no 
                                    FROM(
                                        (
                                            SELECT order_roll_bag_types.order_id, roll_details.roll_no 
                                            FROM order_roll_bag_types
                                            JOIN roll_details on roll_details.id = order_roll_bag_types.roll_id
                                            WHERE order_roll_bag_types.lock_status = false
                                        )
                                        UNION ALL(
                                            SELECT order_roll_bag_types.order_id, roll_transits.roll_no
                                            FROM order_roll_bag_types
                                            JOIN roll_transits on roll_transits.id = order_roll_bag_types.roll_id
                                            WHERE order_roll_bag_types.lock_status = false
                                        )
                                    )orders
                                    GROUP BY order_id 
                            )
                        ) AS order_roll_bag_types"),
                        "order_roll_bag_types.order_id","order_punch_details.id"
                    )                
                    ->leftJoin("client_detail_masters","client_detail_masters.id","order_punch_details.client_detail_id") 
                    ->leftJoin("bag_type_masters","bag_type_masters.id","order_punch_details.bag_type_id")    
                    ->leftJoin("grade_masters","grade_masters.id","order_punch_details.grade_id")                   
                    ->where("order_punch_details.lock_status",false)
                    ->where("order_punch_details.is_delivered",false)
                    ->where(function($where){
                        $where->where(DB::raw("order_punch_details.total_units"),">",DB::raw("order_punch_details.booked_units + order_punch_details.disbursed_units"));
                    })
                    ->orderBy("order_punch_details.created_at","ASC");                               

            if($fromDate && $uptoDate){             
                $data->whereBetween(DB::raw("order_punch_details.created_at::date"),[$fromDate,$uptoDate]);
            }

            elseif($fromDate){
                $data->where(DB::raw("order_punch_details.created_at::date"),">=",$fromDate);
            }
            elseif($uptoDate){
                $data->where(DB::raw("order_punch_details.created_at::date"),"<=",$uptoDate);
            } 

            if($orderNo){
                $data->where("order_punch_details.order_no",$orderNo);
            }     
            $list = DataTables::of($data)
                ->addIndexColumn()                
                ->addColumn('is_delivered', function ($val) {                    
                    return $val->is_delivered ? "YES" : "NO";
                })
                ->addColumn("bag_size",function($val){
                    return (float)$val->bag_w." x ".(float)$val->bag_l.($val->bag_g ?(" x ".(float)$val->bag_g) :"") ;
                })
                ->addColumn("bag_printing_color",function($val){
                    return collect(json_decode($val->bag_printing_color,true))->implode(", ") ;
                })
                ->addColumn("bag_color",function($val){
                    return collect(json_decode($val->bag_color,true))->implode(", ") ;
                })
                ->addColumn("bag_gsm",function($val){
                    return collect(json_decode($val->bag_gsm,true))->implode(", ") ;
                })
                ->addColumn("alt_bag_color",function($val){
                    return collect(json_decode($val->alt_bag_color,true))->implode(", ") ;
                })
                ->addColumn("alt_bag_gsm",function($val){
                    return collect(json_decode($val->alt_bag_gsm,true))->implode(", ") ;
                })
                
                ->addColumn("total_units",function($val){
                    return round($val->total_units) ;
                })
                ->addColumn("booked_units",function($val){
                    return round($val->booked_units) ;
                })
                ->addColumn("balance_units",function($val){
                    return round($val->total_units -( $val->booked_units + $val->disbursed_units));
                })
                ->addColumn("balance_units_in_kg",function($val){
                    $balance = round($val->total_units -( $val->booked_units + $val->disbursed_units));
                    $bag = $val->getBagType();
                    $formula = $bag->weight_of_bag_per_piece??"";
                    $size = $bag->roll_size_find;
                    $newRequest = new Request();
                    $newRequest->merge([
                        "formula"=>$formula,
                        "bagL" =>$val->bag_l,
                        "size" =>" ( ".$size." ) ",
                        "bagL"=>$val->bag_l,                        
                        "bagW"=>$val->bag_w,
                        "bagG"=>$val->bag_g,
                    ]);                    
                    $kg=[];
                    $gsmArr = json_decode($val->bag_gsm,true);
                    if($gsmArr && is_array($gsmArr) && $val->units=="Piece"){
                        
                        foreach($gsmArr as $gsm){
                            $newRequest->merge([
                                "gsm"=>$gsm
                            ]);
                            $result = $this->calculatePossibleProduction($newRequest);
                            $kg[] = ["key"=>$gsm,"val"=> roundFigure(($result["result"] * $balance)/1000)];
                        }
                    }
                    return $kg;
                })
                ->addColumn('created_at', function ($val) {                    
                    return $val->created_at ? Carbon::parse($val->created_at)->format("d-m-Y") : "";                    
                })
                ->addColumn('estimate_delivery_date', function ($val) {                    
                    return $val->estimate_delivery_date ? Carbon::parse($val->estimate_delivery_date)->format("d-m-Y") : "";                    
                })
                ->addColumn('delivery_date', function ($val) {                    
                    return $val->delivery_date ? Carbon::parse($val->delivery_date)->format("d-m-Y") : "";                    
                })
                ->addColumn('action', function ($val) {     
                    $button ='<i class="btn btn-sm btn-success mx-2" onClick="openBookingModel('.$val->id.')">S</i>'; 
                    $button .='<a href = "'.route('order.punches.edit',['orderId' => $val->id]).'" class="btn btn-sm btn-primary mx-2">E</a>';      
                    $button .= '<i class="btn btn-sm btn-warning mx-2" onClick="disbursedOrder('.$val->id.')" >D</i>';
                    $button .= '<i class="btn btn-sm btn-danger mx-2" onClick="deactivate('.$val->id.')" >X</i>';         
                    return '<div style="display:flex">'.$button.'</div>';                    
                })
                ->make(true);
            return $list;

        }
        return view("Roll/unBookedOrder");
    }

    public function rebookOrder(Request $request){
        try{
            $order = $this->_M_OrderPunches->find($request->id);
            $bag = $order->getBagType();
            $client = $order->getClient();
            $grade = $order->getGrade();
            $rateType = $order->getRateType();
            $fare = $order->getFare();
            $stereo = $order->getStereo();
            $order->bag_type = $bag->bag_type??"";
            $order->client_name = $client->client_name??"";
            $order->grade = $grade->grade??"";
            $order->rate_type = $rateType->rate_type??"";
            $order->fare_type = $fare->fare_type??"";
            $order->stereo_type = $stereo->stereo_type??"";
            
            $request->merge([
                "bagQuality"=>$order->bag_quality,
                "gradeId"=>$order->grade_id,
                "ratePerUnit"=>$order->rate_per_unit,
                "rateTypeId"=>$order->rate_type_id,
                "fareTypeId"=>$order->fare_type_id,
                "stereoTypeId"=>$order->stereo_type_id,
                "bookingBagColor"=>collect(json_decode($order->bag_color,true))->toArray(),//$order->bag_color,
                "bagGsm"=>collect(json_decode($order->bag_gsm,true))->toArray(),//$order->bag_gsm,
                "bagGsmJson"=>collect(json_decode($order->bag_gsm_json,true))->implode("/"),
                "bookingBagTypeId"=>$order->bag_type_id,
                "totalUnits"=> $order->total_units -  $order->disbursed_units, //$order->booked_units -
                "bookingBagUnits"=>$order->units,
                "altBagGsm"=> collect(json_decode($order->alt_bag_gsm,true))->toArray(),
                "altBagColor"=>collect(json_decode($order->alt_bag_color,true))->toArray(),

                "l"=>$order->bag_l,
                "w"=>$order->bag_w,
                "g"=>$order->bag_g,
                
            ]);
            $result = $this->orderSuggestionClient($request)->original;
            $order->bag_gsm_json = collect(json_decode($order->bag_gsm_json,true))->implode("/");
            $data["order"] =$order;
            $data["roll"] =[];
            $data["rollTransit"] =[];
            if($result["status"]){
                $data["roll"] = $result["data"]["roll"];
                $data["rollTransit"] = $result["data"]["rollTransit"];
            }
            $message="Data Fetched";
            if(sizeof($data["roll"])<=0 && sizeof($data["rollTransit"])<=0){
                $message=" Roll Not Found For ".$request->totalUnits." ".$request->bookingBagUnits;
            }
            return responseMsgs(true,$message,$data);
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function rollTestByOrder(Request $request){
        try{
            $rollId = $request->id;
            $orderId = $request->bookingForClientId;
            $roll = $this->_M_RollTransit->find($rollId);
            if(!$roll){
                $roll= $this->_M_RollDetail->find($rollId);
            }
            $order = $this->_M_OrderPunches->find($orderId);
            $newRequest = new Request();
            $newRequest->merge([
                "bagQuality"=>$order->bag_quality,
                "gradeId"=>$order->grade_id,
                "ratePerUnit"=>$order->rate_per_unit,
                "rateTypeId"=>$order->rate_type_id,
                "fareTypeId"=>$order->fare_type_id,
                "stereoTypeId"=>$order->stereo_type_id,
                "bookingBagColor"=>collect(json_decode($order->bag_color,true))->toArray(),//$order->bag_color,
                "bagGsm"=>collect(json_decode($order->bag_gsm,true))->toArray(),//$order->bag_gsm,
                "bagGsmJson"=>collect(json_decode($order->bag_gsm_json,true))->implode("/"),
                "bookingBagTypeId"=>$order->bag_type_id,
                "totalUnits"=> $order->total_units - $order->booked_units - $order->disbursed_units,
                "bookingBagUnits"=>$order->units,
                "altBagGsm"=> collect(json_decode($order->alt_bag_gsm,true))->toArray(),
                "altBagColor"=>collect(json_decode($order->alt_bag_color,true))->toArray(),

                "l"=>$order->bag_l,
                "w"=>$order->bag_w,
                "g"=>$order->bag_g,
                
            ]); 
            $result = $this->orderSuggestionClient($newRequest)->original;
            if(!$result["status"]){
                throw new Exception($result["message"]);                
            }
            $test = false;
            if($roll && $roll->getTable()=="roll_details"){
                $test = collect($result["data"]["roll"])->where("id",$rollId)->count()>0 ? true : false;
            }
            if($roll && $roll->getTable()=="roll_transits"){
                $test = collect($result["data"]["rollTransit"])->where("id",$rollId)->count()>0 ? true : false;
            }
            $message="Roll is tested and suitable for this order";
            if(!$test){
                $message="Roll is tested and not suitable for this order";
            }
            $data["test"]=$test;
            return responseMsgs(true,$message,$data);
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function bookRollToOrder(Request $request){
        try{
            $rule = [
                "id"=>"required",
                "bookingForClientId"=>"required",
            ];
            $newRequest = new Request([
                "id"=>$request->bookingForClientId,
                "roll"=>[
                    ["id"=>$request->id],
                ]
                ]);
                return $this->orderPunchesSave($newRequest);
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function removeBookedRoll(Request $request){
        try{
            $rule = [
                "id"=>"required",
            ];
            $validate = Validator::make($request->all(),$rule);
            if($validate->fails()){
                return validationError($validate);
            }
            $roll = $this->_M_RollDetail->find($request->id);
            if(!$roll){
                $roll = $this->_M_RollTransit->find($request->id);
            }
            if($roll->is_printed){
                throw new Exception("Roll is Printed");
            }
            if($roll->is_cut){
                throw new Exception("Roll is cut");
            }
            DB::beginTransaction();
            if($roll->client_detail_id){
                $orderRoll = $this->_M_OrderRollBagType->where("roll_id",$roll->id)->where("lock_status",false)->first();
                if($orderRoll){
                    $order = $this->_M_OrderPunches->find($orderRoll->order_id);
                    $bag = $this->_M_BagType->find($order->bag_type_id);
                    $bestFind = "";
                    $bestFind2 = "";
                    if($order->units=="Kg"){
                        $bestFind = "RW";
                        $bestFind2 = "RW";
                    }elseif($order->units=="Piece"){
                        $bestFind = $bag->roll_find;
                        $bestFind2 = $bag->roll_find_as_weight;
                    }
                    $newRequest = new Request();
                    $newRequest->merge(
                        [
                        "bookingBagUnits" => $order->units,
                        "formula" => $bestFind,
                        "length" => $roll->length,
                        "netWeight"=>$roll->net_weight,
                        "size"=>$roll->size,
                        "gsm"=>$roll->gsm,

                        "bagL"=>$order->bag_l,
                        "bagW"=>$order->bag_w,
                        "bagG"=>$order->bag_g
                        ]
                    );
                    $newRequest2 = new Request($newRequest->all());
                    $newRequest2->merge([
                        "formula" => $bestFind2,
                    ]);
                    $result = $this->calculatePossibleProduction($newRequest);
                    $result2 = $this->calculatePossibleProduction($newRequest2);
                    $order->booked_units = $order->booked_units - ((($result["result"]??0)+($result2["result"]??0))/2);
                    $orderRoll->lock_status=true;
                    $order->update();
                    $orderRoll->update();

                    $newRequest->merge([
                        "formula" => $bag->roll_find,
                    ]);
                    $newRequest2->merge([
                        "formula" => $bag->roll_find_as_weight,
                    ]);
                    $pieces = $this->calculatePossibleProduction($newRequest);
                    $pieces2 = $this->calculatePossibleProduction($newRequest2);
                    $totalPiece = ((($pieces["result"]??0)+($pieces2["result"]??0))/2); 
                    $totalLoopWeight = (($totalPiece*3.4)/1000);
                    if(in_array($bag->id,[2,4])){
                        $loopStock = $this->_M_LoopStock->where("loop_color",$order->bag_loop_color)->first();

                        $newLoopAccRequest = new Request(
                            [
                                "loop_stock_id"=>$loopStock->id,
                                "roll_id"=>$roll->id,
                                "order_id"=>$order->id,
                                "description"=>"Roll Remove From Booking",
                                "opening_balance"=>$loopStock->balance,
                                "credit"=>0,
                                "debit"=>$totalLoopWeight,
                                "balance"=>$loopStock->balance +  $totalLoopWeight,
                                "user_id"=>Auth()->user()->id
                            ]
                        );
                        $this->_M_LoopAccount->store($newLoopAccRequest);

                        $loopStock->balance = $loopStock->balance + $totalLoopWeight;
                        $loopStock->update();
                    }
                }                        
            }
            $roll->resizeRollFromClient($roll->id);
            DB::commit();
            return responseMsgs(true,"Roll Remove From Booking","");
        }catch(Exception $e){
            DB::rollBack();
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function disbursedOrder(Request $request){
        try{
            $validate = Validator::make($request->all(),[
                "id"=>"required|exists:".$this->_M_OrderPunches->getTable().",id",
            ]);
            if($validate->fails()){
                return validationError($validate);
            }
            $order = $this->_M_OrderPunches->find($request->id);
            $order->disbursed_units = $order->total_units - $order->booked_units;
            $order->disbursed_by = Auth()->user()->id;
            DB::beginTransaction();
            $order->update();
            DB::commit();
            return responseMsgs(true,"Order Disbursed","");
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function deactivateOrder(Request $request){
        try{
            $validate = Validator::make($request->all(),[
                "id"=>"required|exists:".$this->_M_OrderPunches->getTable().",id",
            ]);
            if($validate->fails()){
                return validationError($validate);
            }
            $order = $this->_M_OrderPunches->find($request->id);
            
            $rollTransit = $order->getRollTransit()->get(); 
            $roll = $order->getRollDetail()->get();             
            $order->lock_status = true;
            $order->deceived_by = Auth()->user()->id;
            $test1=$rollTransit->filter(function ($item) {
                return $item->is_printed || $item->is_cut;
            })->count();
            $test2=$roll->filter(function ($item) {
                return $item->is_printed || $item->is_cut;
            })->count();
            if($test1 || $test2){
                throw new Exception("Some Roll are printed or cut");
            }

            DB::beginTransaction();
            foreach($rollTransit as $val){
                $val->resizeRollFromClient($val->id);
            }
            foreach($roll as $val){
                $val->resizeRollFromClient($val->id);
            }
            $order->update();
            DB::commit();
            return responseMsgs(true,"Order Is Deactivated","");
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function disburseRegister(Request $request){
        if($request->ajax())
        {
            $fromDate = $request->fromDate;
            $uptoDate = $request->uptoDate;
            $orderNo = $request->orderNo;            
            $data = $this->_M_OrderPunches
                    ->select(
                                "order_punch_details.*","order_roll_bag_types.*",
                                "client_detail_masters.client_name",  
                                "bag_type_masters.bag_type" ,  
                                "users.name"                     
                    )
                    ->leftJoin(
                        DB::raw("(
                            SELECT *
                            FROM(
                                    SELECT order_id, STRING_AGG(roll_no,' , ') as roll_no 
                                    FROM(
                                        (
                                            SELECT order_roll_bag_types.order_id, roll_details.roll_no 
                                            FROM order_roll_bag_types
                                            JOIN roll_details on roll_details.id = order_roll_bag_types.roll_id
                                            WHERE order_roll_bag_types.lock_status = false
                                        )
                                        UNION ALL(
                                            SELECT order_roll_bag_types.order_id, roll_transits.roll_no
                                            FROM order_roll_bag_types
                                            JOIN roll_transits on roll_transits.id = order_roll_bag_types.roll_id
                                            WHERE order_roll_bag_types.lock_status = false
                                        )
                                    )orders
                                    GROUP BY order_id 
                            )
                        ) AS order_roll_bag_types"),
                        "order_roll_bag_types.order_id","order_punch_details.id"
                    )                
                    ->leftJoin("client_detail_masters","client_detail_masters.id","order_punch_details.client_detail_id") 
                    ->leftJoin("bag_type_masters","bag_type_masters.id","order_punch_details.bag_type_id")  
                    ->leftJoin("users","users.id","order_punch_details.disbursed_by")                 
                    ->where("order_punch_details.lock_status",false)
                    ->where("order_punch_details.disbursed_units",">",0)
                    ->orderBy("order_punch_details.created_at","ASC");                               

            if($fromDate && $uptoDate){             
                $data->whereBetween(DB::raw("order_punch_details.created_at::date"),[$fromDate,$uptoDate]);
            }

            elseif($fromDate){
                $data->where(DB::raw("order_punch_details.created_at::date"),">=",$fromDate);
            }
            elseif($uptoDate){
                $data->where(DB::raw("order_punch_details.created_at::date"),"<=",$uptoDate);
            } 

            if($orderNo){
                $data->where("order_punch_details.order_no",$orderNo);
            }         
            $list = DataTables::of($data)
                ->addIndexColumn()                
                ->addColumn('is_delivered', function ($val) {                    
                    return $val->is_delivered ? "YES" : "NO";
                })
                ->addColumn("bag_size",function($val){
                    return (float)$val->bag_w." x ".(float)$val->bag_l.($val->bag_g ? (" x ".(float)$val->bag_g) : "") ;
                })
                ->addColumn("bag_printing_color",function($val){
                    return collect(json_decode($val->bag_printing_color,true))->implode(", ") ;
                })
                ->addColumn("bag_color",function($val){
                    return collect(json_decode($val->bag_color,true))->implode(", ") ;
                })
                ->addColumn("bag_gsm",function($val){
                    return collect(json_decode($val->bag_gsm,true))->implode(", ") ;
                })
                
                ->addColumn("total_units",function($val){
                    return round($val->total_units);
                })
                ->addColumn("booked_units",function($val){
                    return round($val->booked_units) ;
                })
                ->addColumn("disbursed_units",function($val){
                    return round($val->disbursed_units) ;
                })
                ->addColumn('created_at', function ($val) {                    
                    return $val->created_at ? Carbon::parse($val->created_at)->format("d-m-Y") : "";                    
                })
                ->addColumn('estimate_delivery_date', function ($val) {                    
                    return $val->estimate_delivery_date ? Carbon::parse($val->estimate_delivery_date)->format("d-m-Y") : "";                    
                })
                ->addColumn('delivery_date', function ($val) {                    
                    return $val->delivery_date ? Carbon::parse($val->delivery_date)->format("d-m-Y") : "";                    
                })
                ->make(true);
            return $list;

        }
        return view("Roll/disburseRegister");
    }

    public function rollUpdate(Request $request){
        try{
            $roll = $this->_M_RollDetail->find($request->editRollId);
            if(!$roll){
                $roll = $this->_M_RollTransit->find($request->editRollId);
            }
            if($roll->is_printed){
                throw new Exception("Roll Is Printed");
            }
            if($roll->is_cut){
                throw new Exception("Roll Is Cut");
            }
            $roll->quality_id = $request->qualityId;
            $roll->roll_type = $request->rollType;
            $roll->hardness = $request->hardness;
            $roll->gsm = $request->gsm;
            $roll->gsm_json = $request->gsmJson;
            $roll->roll_color = $request->rollColor;
            $roll->length = $request->length;
            $roll->size = $request->size;
            $roll->net_weight = $request->netWeight;
            $roll->gross_weight = $request->grossWeight;
            DB::beginTransaction();
            $roll->update();
            DB::commit();
            return responseMsgs(true,"Roll Update","");
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function swapRollNo(Request $request){
        try{
            $rules = [
                "roll" => "required|array",
                "roll.firstRoll.*" => "required|exists:" . $this->_M_RollDetail->getTable() . ",roll_no,is_printed,false,is_cut,false",
                "roll.secondRoll.*" => [
                    "required",
                    "exists:" . $this->_M_RollDetail->getTable() . ",roll_no,is_printed,false,is_cut,false",
                    function ($attr, $value, $fail) use ($request) {
                        $key = explode(".",$attr)[2];
                        $firstRollNo = $request->input('roll.firstRoll.'.$key);
                        $secondRollNo = $value;
                        
                        if($firstRollNo==$secondRollNo){
                            $fail("firstRoll and secondRoll must be deferent");
                            return;
                        }
                        // Fetch roll details for all rolls in firstRoll and secondRoll
                        $firstRollDetails = $this->_M_RollDetail
                            ->where('roll_no', $firstRollNo)
                            ->first();
                        $secondRollDetails = $this->_M_RollDetail
                            ->where('roll_no', $secondRollNo)
                            ->first();
            
                        $columnsToMatch = [
                            'vendor_id', 'gsm', 'gsm_json', 
                            'roll_color', //'length', 
                            'size', 
                            "quality_id",
                            //'net_weight', 
                            'hardness', 
                            'roll_type'
                        ];
                        if($firstRollDetails && $secondRollDetails){
                            foreach ($columnsToMatch as $column) {
                                if ($firstRollDetails->$column !== $secondRollDetails->$column) {
                                    $fail("firstRoll and secondRoll must have the same value for the column: $column.");
                                    return;
                                }
                            }
                        }
                    },
                ],
            ];
            
            $validate = Validator::make($request->all(), $rules);
            
            if ($validate->fails()) {
                return validationError($validate);
            }
            DB::beginTransaction();
            foreach($request->roll["firstRoll"] as $index=> $val){
                $rollNo1 = $val;
                $rollNo2 = $request->roll["secondRoll"][$index];
                $roll1 = $this->_M_RollDetail->where("roll_no",$rollNo1)->first();
                $roll2 = $this->_M_RollDetail->where("roll_no",$rollNo2)->first();
                if($roll1->is_printed){
                    throw new Exception("Roll No. ".$roll1->roll_no." is printed");
                }
                if($roll1->is_cut){
                    throw new Exception("Roll No. ".$roll1->roll_no." is cut");
                }
                if($roll2->is_printed){
                    throw new Exception("Roll No. ".$roll2->roll_no." is printed");
                }
                if($roll2->is_cut){
                    throw new Exception("Roll No. ".$roll2->roll_no." is cut");
                }
                $roll1->roll_no = null;
                $roll2->roll_no = null;
                $roll1->update();
                $roll2->update();
                $roll1->roll_no = $rollNo2;
                $roll2->roll_no = $rollNo1;
                $roll1->update();
                $roll2->update();
            }
            DB::commit();
            return responseMsgs(true,"Roll No. Swap","");
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }
    

}
