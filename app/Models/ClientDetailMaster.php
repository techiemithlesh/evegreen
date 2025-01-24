<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ClientDetailMaster extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_name',
        'mobile_no',
        'email',
        'address',
        "city_id",
        "state_id",
        "sector_id",
        "secondary_mobile_no",
        "temporary_mobile_no",
        'lock_status',
    ];

    public function store($request){        
        $inputs = snakeCase($request);
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

    public function getClientListOrm(){
        return self::where("lock_status",false);
    }

    public function getCity(){
        return $this->belongsTo(CityStateMap::class,"city_id","id")->first();
    }

    public function getState(){
        return $this->belongsTo(StateMaster::class,"state_id","id")->first();
    }
}
