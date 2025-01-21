<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class LoopStock extends Model
{
    use HasFactory;
    protected $fillable = [
        "loop_color",
        "balance",
        "min_limit",
        'lock_status',
    ];

    public function store($request){        
        $inputs = snakeCase($request);
        $id= self::create($inputs->all())->id;
        return $id;
    }

    public function edit($request)
    {
        $inputs = snakeCase($request)->filter(function($val,$index){
            return (in_array($index,$this->fillable));
        });
        $stock = self::find($request->id);
        if($stock && isset($inputs["balance"]) && $inputs["balance"]!=$stock->balance){         
            $newLoopAccRequest = new Request(
                [
                    "loop_stock_id"=>$request->id,
                    "description"=>"Update By User",
                    "opening_balance"=>$stock->balance,
                    "credit"=> $inputs["balance"] < $stock->balance ? ($stock->balance -$inputs["balance"]) : 0,
                    "debit"=> $inputs["balance"] > $stock->balance ? ( $inputs["balance"] -$stock->balance ) : 0,
                    "balance"=> $inputs["balance"],
                    "user_id"=>Auth()->user()->id
                ]
            );
            (new LoopUsageAccount())->store($newLoopAccRequest);
        }
        return self::where("id",$request->id)->update($inputs->all());
    }

    public function getLoopColorOrm(){
        return self::where("lock_status",false);
    }
}
