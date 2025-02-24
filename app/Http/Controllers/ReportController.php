<?php

namespace App\Http\Controllers;

use App\Models\MachineMater;
use App\Models\OrderPunchDetail;
use App\Models\RollDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ReportController extends Controller
{
    //

    protected $_M_Machine;
    protected $_RollDetails;
    protected $_M_OrderPunches;

    function __construct()
    {
        $this->_M_Machine = new MachineMater();
        $this->_RollDetails = new RollDetail();
        $this->_M_OrderPunches = new OrderPunchDetail();
    }

    public function dailyProduction(Request $request){
        $machine=$this->_M_Machine->orderBy("is_printing", "DESC")
                                ->orderBy("id", "ASC")
                                ->get();
        $machine->map(function($val){
            $monthly =0;
            $weakly =0;
            $daily =0;
            $todayProduction=[];
            $currentDate = Carbon::now();
            $today = $currentDate->copy()->format("Y-m-d");
            $fromYear = $currentDate->copy()->subYear()->format("Y-m-d");
            $fromMonth = $currentDate->copy()->subMonth()->format("Y-m-d");
            $fromWeak = $currentDate->copy()->subWeek()->format("Y-m-d");
            if($val->is_cutting){
                $monthly = $this->_RollDetails->where("cutting_machine_id",$val->id)->whereBetween("cutting_date",[$fromYear,$today])->count()/12;
                $weakly = $this->_RollDetails->where("cutting_machine_id",$val->id)->whereBetween("cutting_date",[$fromMonth,$today])->count()/30;
                $daily = $this->_RollDetails->where("cutting_machine_id",$val->id)->whereBetween("cutting_date",[$fromWeak,$today])->count()/7;
                $todayProduction = $this->_RollDetails->where("cutting_machine_id",$val->id)->where("cutting_date",$today)->orderBy("updated_at","ASC")->get();
            }elseif($val->is_printing){
                $monthly = $this->_RollDetails->where("printing_machine_id",$val->id)->whereBetween("printing_date",[$fromYear,$today])->count()/12;
                $weakly = $this->_RollDetails->where("printing_machine_id",$val->id)->whereBetween("printing_date",[$fromMonth,$today])->count()/30;
                $daily = $this->_RollDetails->where("printing_machine_id",$val->id)->whereBetween("printing_date",[$fromWeak,$today])->count()/7;
                $todayProduction = $this->_RollDetails->where("printing_machine_id",$val->id)->where("printing_date",$today)->orderBy("updated_at","ASC")->get();
            }
            $val->monthly = roundFigure($monthly);
            $val->weakly = roundFigure($weakly);
            $val->daily = roundFigure($daily);
            $val->todayProduction = collect($todayProduction)->map(function($val){
                $val->purchase_date = $val->purchase_date ? Carbon::parse($val->purchase_date)->format("d-m-Y"):"";
                $val->bag_size = (float)$val->w." x ".(float)$val->l.($val->g ?(" x ".(float)$val->g) :"") ;
                $val->client_name = $val->getClient()->first()->client_name??"";
                $val->vendor_name = $val->getVendor()->first()->vendor_name??"";
                $val->bag_type = $val->getBagType()->first()->bag_type??"";
                $val->quality = $val->getQualityType()->first()->quality??"";
                return $val;
            });
        });
        $data["machineList"] = $machine;
        return view("Reports/daily_production",$data);
    }

    public function agentBookingOrder(Request $request){

    }

    public function orderRepitition(Request $request){
        if($request->ajax()){
            $currentDate = Carbon::now();
            $fromDate = $currentDate->copy()->subMonths(2)->format('Y-m-d');
            $uptoDate = $currentDate->copy()->format("Y-m-d");
            DB::enableQueryLog();
            $data = DB::select("
                        SELECT 
                            btm.bag_type, 
                            opd.bag_quality, 
                            opd.total_units,
                            opd.bag_w, opd.bag_l, opd.bag_g, cdm.client_name,
                            gsm.bag_gsm_value, 
                            COUNT(DISTINCT opd.id) AS count
                        FROM order_punch_details opd
                        LEFT JOIN client_detail_masters cdm ON cdm.id = opd.client_detail_id
                        LEFT JOIN bag_type_masters btm on btm.id = opd.bag_type_id
                        CROSS JOIN LATERAL jsonb_array_elements_text(opd.bag_gsm::jsonb) AS gsm(bag_gsm_value) -- Fix JSON parsing
                        WHERE opd.order_date BETWEEN ? AND ?
                            AND opd.lock_status = ?
                        GROUP BY 
                            btm.bag_type, 
                            opd.bag_quality, 
                            opd.total_units,
                            opd.bag_w, opd.bag_l, opd.bag_g, cdm.client_name,
                            gsm.bag_gsm_value
                        HAVING COUNT(*) > 1
                        ORDER BY COUNT(*) DESC;
                    ", [$fromDate, $uptoDate, false]);
                    $list = DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('bag_size', function ($val) { 
                        return (float)$val->bag_w." x ".(float)$val->bag_l.($val->bag_g ?(" x ".(float)$val->bag_g) :"") ;
                    })
                    ->rawColumns(['row_color', 'action'])
                    ->make(true);
                return $list;
        }
        $data=[];
        return view("reports/orderRepitition",$data);
        
                
        dd(DB::getQueryLog(),$data);
    }
}
