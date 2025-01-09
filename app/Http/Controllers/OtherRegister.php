<?php

namespace App\Http\Controllers;

use App\Models\GarbageAcceptRegister;
use App\Models\GarbageNotAcceptRegister;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class OtherRegister extends Controller
{
    //

    protected $_M_GarbageAcceptRegister;
    protected $_M_GarbageNotAcceptRegister;
    function __construct()
    {
        $this->_M_GarbageAcceptRegister = new GarbageAcceptRegister();
        $this->_M_GarbageNotAcceptRegister = new GarbageNotAcceptRegister();
    }

    public function acceptGarbage(Request $request){
        if($request->ajax()){
            $data = $this->_M_GarbageAcceptRegister
                    ->select("roll_details.*","garbage_accept_registers.*","users.name AS operator_name","helper.name AS helper_name")
                    ->join("roll_details","roll_details.id","garbage_accept_registers.roll_id")
                    ->leftJoin("users",function($join){
                        $join->on("users.id","garbage_accept_registers.operator_id")
                            ->where("users.user_type_id",6);
                    })
                    ->leftJoin("users AS helper",function($join){
                        $join->on("helper.id","garbage_accept_registers.helper_id")
                            ->where("helper.user_type_id",5);
                    })
                    ->where("garbage_accept_registers.lock_status",false)
                    ->orderBy("garbage_accept_registers.id","DESC");
            
            return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn("cutting_date",function($val){
                        return $val->cutting_date ? Carbon::parse($val->cutting_date)->format("d-m-Y"):"";
                    })
                    ->make(true);
        }
        return view("Register/accept_garbage");
    }
}
