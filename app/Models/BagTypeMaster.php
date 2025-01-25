<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BagTypeMaster extends Model
{
    use Loggable;
    use HasFactory;

    protected $fillable = [
        'bag_type',
        "gsm_variation",
        "roll_find",
        "roll_find_as_weight",
        "roll_size_find",
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

    public function getBagListOrm(){
        return self::where("lock_status",false);
    }
}
