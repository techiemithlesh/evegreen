<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RollTransit extends Model
{
    use HasFactory;

    protected $fillable = [
        'roll_no',
        "purchase_date",
        "vender_id",
        "gsm",
        "gsm_json",
        "gsm_variation",
        "roll_color",
        "length",
        "size",
        "net_weight",
        "gross_weight",
        "hardness",
        "roll_type",
        "client_detail_id",
        "estimate_delivery_date",
	    "delivery_date"  ,
	    "is_delivered" ,
	    "bag_type_id",
	    "bag_unit" ,
        "w" ,
        "l" ,
        "g" ,
        "printing_color" ,
        "is_printed",
        "printing_date",
        "weight_after_print",
        "printing_machine_id",
        "is_cut",
        "cutting_date",
        "weight_after_cutting",
        "cutting_machine_id",
        "lock_status",
        "loop_color",
        "quality_id"
    ];

    public function store($request){        
        $inputs = snakeCase($request);
        $rollTransit = new RollTransit($inputs->all());
        $rollTransit->save();
        return $rollTransit->id;
    }


    public function getCuttingSchedule(){
        return $this->hasOne(CuttingScheduleDetail::class,"roll_id","id")->where("lock_status",false);
    }
    public function getPrintingSchedule(){
        return $this->hasOne(PrintingScheduleDetail::class,"roll_id","id")->where("lock_status",false);
    }
}
