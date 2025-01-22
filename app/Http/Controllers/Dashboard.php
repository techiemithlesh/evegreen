<?php

namespace App\Http\Controllers;

use App\Models\LoopStock;
use Exception;
use Illuminate\Http\Request;

class Dashboard extends Controller
{
    protected $_M_LoopStock;
    function __construct()
    {
        $this->_M_LoopStock = new LoopStock();
    }

    public function home(){
        return view("home");
    }

    public function loopStatus(Request $request){
        try{
            $data = $this->_M_LoopStock->getLoopColorOrm()->orderBy("id","ASC")->get();
            return responseMsgs(true,"",$data);
        }catch(Exception $e){
            return responseMsgs(false,$e->getMessage(),"");
        }
    }

    public function nearOrderDispatched(){
        
    }
}
