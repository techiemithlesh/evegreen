<?php

namespace App\Livewire\Dashboard;

use App\Models\OrderPunchDetail;
use Carbon\Carbon;
use Livewire\Component;

class NearestDispatchedOrder extends Component
{
    public $cards;
    function __construct()
    {
        $this->cards = (new OrderPunchDetail())->select("estimate_delivery_date")
            ->where("estimate_delivery_date",">",Carbon::now()->format("Y-m-d"))
            ->where("is_delivered",false)
            ->where("lock_status",false)
            ->orderBy("estimate_delivery_date","ASC")
            ->get()
            ->pluck("estimate_delivery_date")
            ->unique()->take(5);
            $this->cards = $this->cards->map(function($val){
                $data = OrderPunchDetail::where("is_delivered",false)
                        ->where("lock_status",false)
                        ->where("estimate_delivery_date",$val)
                        ->get();
                return collect(["date"=>Carbon::parse($val)->format("d-m-Y"),"data"=>$data]);
            })->values();
        
    }
    public function render()
    {
        return view('livewire.dashboard.nearest-dispatched-order');
    }
}
