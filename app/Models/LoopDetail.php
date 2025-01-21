<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoopDetail extends Model
{
    use HasFactory;
    protected $fillable = [
        "roll_no",
        "purchase_date",
        "vender_id",
        "gsm",
        'gsm_json',
        "gsm_variation",
        "loop_color",
        "length",
        "size",
        "net_weight",
        "gross_weight",
        "quality_id",
        "hardness",
        'roll_type',
        'lock_status'
    ];

    public function store($request){        
        $inputs = snakeCase($request);
        if(!isset($inputs["user_id"])){
            $inputs["user_id"]=Auth()->user()->id;
        }
        $id= self::create($inputs->all())->id;
        return $id;
    }
}
