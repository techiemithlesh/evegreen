<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentModeMaster extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_mode',
        "lock_status",
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

    public function getPaymentModeListOrm(){
        return self::where("lock_status",false);
    }
}
