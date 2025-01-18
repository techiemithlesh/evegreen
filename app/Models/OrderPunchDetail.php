<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderPunchDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        "order_date",
        'client_detail_id',
        "estimate_delivery_date",
        "delivery_date",
        "is_delivered",
        "payment_mode_id",
        "bag_type_id",
        "bag_quality",
        "bag_gsm",
        "bag_gsm_json",
        "units",
        "total_units",
        "rate_per_unit",
        "bag_w",
        "bag_l",
        "bag_g",
        "bag_loop_color",
        "bag_color",
        "bag_printing_color",
        "booked_units",
        "disbursed_units",
        "grade_id",
        "rate_type_id",
        "fare_type_id",
        "stereo_type_id",
        "lock_status",
    ];

    public function store($request){        
        $inputs = snakeCase($request);
        if(!isset($inputs["order_date"])){
            $inputs["order_date"]=Carbon::now()->format("Y-m-d");
        }
        $id= self::create($inputs->all())->id;
        return $id;
    }

    public function getBagType(){
        return $this->belongsTo(BagTypeMaster::class,"bag_type_id","id")->first();
    }

    public function getClient(){
        return $this->belongsTo(ClientDetailMaster::class,"client_detail_id","id")->first();
    }

    public function getGrade(){
        return $this->belongsTo(GradeMaster::class,"grade_id","id")->first();
    }

    public function getRateType(){
        return $this->belongsTo(RateTypeMaster::class,"rate_type_id","id")->first();
    }

    public function getFare(){
        return $this->belongsTo(FareDetail::class,"fare_type_id","id")->first();
    }

    public function getStereo(){
        return $this->belongsTo(StereoDetail::class,"stereo_type_id","id")->first();
    }

    public function getRollTransit(){
        return $this->hasManyThrough(RollTransit::class,OrderRollBagType::class,"order_id","id","id","roll_id","id");
    }

    public function getRollDetail(){
        return $this->hasManyThrough(RollDetail::class,OrderRollBagType::class,"order_id","id","id","roll_id","id");
    }
}
