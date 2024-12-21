<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserTypeMaster extends Model
{
    //
    use HasFactory; 

    protected $fillable =[
        "user_type",
        "lock_status",
    ];

    public function getMenuList(){
        return $this->hasMany(MenuPermission::class,"user_type_master_id","id")->where("lock_status",false)->get();
    }
}
