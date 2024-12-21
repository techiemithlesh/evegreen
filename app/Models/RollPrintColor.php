<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RollPrintColor extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'roll_id',
        'color',
        'lock_status',
    ];

    public function store($request){        
        $inputs = snakeCase($request);
        $id= self::create($inputs->all())->id;
        return $id;
    }
}
