<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderRollBagType extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        "bag_type_id",
        "roll_id",
        "bag_unit",
        "w",
        "l",
        "g",
        "printing_color",
        "lock_status",
    ];

    public function store($request){        
        $inputs = snakeCase($request);
        $id= self::create($inputs->all())->id;
        return $id;
    }
}
