<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrintingScheduleDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'printing_date',
        "machine_id",
        "roll_id",
        "sl",
        "lock_status",
    ];

    public function store($request){        
        $inputs = snakeCase($request);
        if(!isset($inputs["printing_date"])){
            $inputs["printing_date"]=Carbon::now()->format("Y-m-d");
        }
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

    public function getPrintingScheduleOrm(){
        return self::where("lock_status",false);
    }
}
