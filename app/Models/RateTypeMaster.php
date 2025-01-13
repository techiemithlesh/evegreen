<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class RateTypeMaster extends Model
{
    use HasFactory;
    protected $fillable = [
        "rate_type",
        "user_id",
        'lock_status',
    ];

    public function store($request){        
        $inputs = snakeCase($request);
        $inputs["user_id"] = $inputs["user_id"]??false ? $inputs["user_id"] : Auth()->user()->id??null;
        $id= self::create($inputs->all())->id;
        return $id;
    }

    public function edit($request){
        $inputs = snakeCase($request)->filter(function($val,$index){
            return (in_array($index,$this->fillable));
        });
        $return= self::where("id",$request->id)->update($inputs->all());
        return $return;
    }

    public function getRateTypeListOrm(){
        return self::where("lock_status",false);
    }
}
