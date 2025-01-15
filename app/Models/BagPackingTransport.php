<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BagPackingTransport  extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_no',
        "transporter_name",
        "transporter_id",
        "auto_id",
        "transport_date",
        'bill_no',
        'invoice_no',
        "transport_status",
        'user_id',
        "reiving_user_id",
        'lock_status',
    ];

    public function store($request){        
        $inputs = snakeCase($request);
        if(!isset($inputs["transport_date"])){
            $inputs["transport_date"]=Carbon::now()->format("Y-m-d");
        }
        $id= self::create($inputs->all())->id;
        return $id;
    }

    public function getBag(){
        return $this->hasManyThrough(BagPacking::class,BagPackingTransportDetail::class,"pack_transport_id","id","id","bag_packing_id","id");
    }
}
