<?php

namespace App\Models;

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
        'roll_id',
        'user_id',
        'lock_status',
    ];

    public function store($request){        
        $inputs = snakeCase($request);
        $id= self::create($inputs->all())->id;
        return $id;
    }
}
