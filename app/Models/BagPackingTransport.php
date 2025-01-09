<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BagPackingTransport  extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_no',
        "transporter_name",
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
        $id= self::create($inputs->all())->id;
        return $id;
    }
}
