<?php

namespace App\Http\Controllers;

use App\Models\MachineMater;
use App\Models\RollDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    //

    protected $_M_Machine;
    protected $_RollDetails;

    function __construct()
    {
        $this->_M_Machine = new MachineMater();
        $this->_RollDetails = new RollDetail();
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
                $val->bag_size = (float)$val->bag_w." x ".(float)$val->bag_l.($val->bag_g ?(" x ".(float)$val->bag_g) :"") ;
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
}
