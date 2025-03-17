<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrintingEntry extends Model
{
    use HasFactory;
    use Loggable;

    protected $fillable =[
        "printing_date",
        "machine_id",
        "operator_id",
        "helper_id",
        "shift",
        "user_id",
        "is_verify",
        "verify_by",
        "remarks",
        "lock_status",
    ];

    public function store($request){                
        $inputs = snakeCase($request);
        if(!isset($inputs["user_id"])){
            $inputs["user_id"] = Auth()->user()->id??null;
        }
        $id= self::create($inputs->all())->id;
        return $id;
    }

    public function edit($request){
        $inputs = snakeCase($request)->filter(function($val,$index){
            return (in_array($index,$this->fillable));
        });
        $data = self::find($request->id);
        if($data){
            $data->fill($inputs->all());
            $data->update();
            return true;
        }
        return false;
    }
}
