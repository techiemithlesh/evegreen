<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GarbageEntry extends Model
{
    use HasFactory;
    use Loggable;
    protected $fillable =[
        "order_id",
        "operator_id",
        "helper_id",
        "shift",
        "client_id",
        "user_id",
        "garbage",
        "is_verify",
        "verify_by",
        "remarks",
        "lock_status"
    ];

    public function store($request){                
        $inputs = snakeCase($request);
        if(!isset($inputs["user_id"])){
            $inputs["user_id"] = Auth()->user()->id??null;
        }
        $id= self::create($inputs->all())->id;
        return $id;
    }
}
