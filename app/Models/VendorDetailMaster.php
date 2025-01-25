<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorDetailMaster extends Model
{
    use Loggable;
    use HasFactory;

    protected $fillable = [
        'vendor_name',
        'gst_no',
        'mobile_no',
        'email',
        'address',
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

    public function getVenderListOrm(){
        return self::where("lock_status",false);
    }

    public function rollQualityList(){
        return $this->hasMany(RollQualityMaster::class,"vendor_id","id")->where("lock_status",false)->orderBy("id","ASC");
    }
}
