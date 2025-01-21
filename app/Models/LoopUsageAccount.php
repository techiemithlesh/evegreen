<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class LoopUsageAccount extends Model
{
    use HasFactory;
    protected $fillable = [
        "loop_stock_id",
        "loop_id",
        "description",
        "opening_balance",
        'credit',
        "debit",
        "balance",
        "user_id"
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
