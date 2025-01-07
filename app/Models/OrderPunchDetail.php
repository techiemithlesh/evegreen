<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderPunchDetail extends Model
{
    use HasFactory;

    protected $fillable = [
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
        "booked_units",
        "disbursed_units",
        "lock_status",
    ];

    public function store($request){        
        $inputs = snakeCase($request);
        $id= self::create($inputs->all())->id;
        return $id;
    }

    public function getBagType(){
        return $this->belongsTo(BagTypeMaster::class,"bag_type_id","id")->first();
    }

    public function getClient(){
        return $this->belongsTo(ClientDetailMaster::class,"client_detail_id","id")->first();
    }
}
