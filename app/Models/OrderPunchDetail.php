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
        "lock_status",
    ];

    public function store($request){        
        $inputs = snakeCase($request);
        $id= self::create($inputs->all())->id;
        return $id;
    }
}
