<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BagPacking extends Model
{
    use HasFactory;

    protected $fillable = [
        "packing_no",
        'packing_weight',
        "packing_bag_pieces",
        "packing_date",
        'packing_status',
        'order_id',
        'user_id',
        'lock_status',
    ];

    public function store($request){        
        $inputs = snakeCase($request);
        if(!isset($inputs["packing_date"])){
            $inputs["packing_date"]=Carbon::now()->format("Y-m-d");
        }
        $id= self::create($inputs->all())->id;
        return $id;
    }
}
