<?php

namespace App\Observers;

use App\Models\BagPacking;
use Carbon\Carbon;

class BagPackingObserver
{
    /**
     * Handle the BagPacking "created" event.
     */
    public function created(BagPacking $bagPacking): void
    {
        if(!$bagPacking->packing_no){
            $orderDate = Carbon::parse(Carbon::now());
            $rolNo = $orderDate->clone()->format("dmy")."-";
            $sl = BagPacking::where("packing_date",$orderDate->clone()->format('Y-m-d'))->count("id");
            $slNo ="";
            while(true){   
                $slNo = str_pad((string)$sl,4,"0",STR_PAD_LEFT);
                $sl=($sl+1);             
                $test = BagPacking::where("packing_no",$rolNo.$slNo)->count();
                if((!$test)){                    
                    $rolNo.=$slNo;
                    break;
                }
            }
            $bagPacking->packing_no  = $rolNo;
        }
        $bagPacking->save();
    }

    /**
     * Handle the BagPacking "updated" event.
     */
    public function updated(BagPacking $bagPacking): void
    {
        //
    }

    /**
     * Handle the BagPacking "deleted" event.
     */
    public function deleted(BagPacking $bagPacking): void
    {
        //
    }

    /**
     * Handle the BagPacking "restored" event.
     */
    public function restored(BagPacking $bagPacking): void
    {
        //
    }

    /**
     * Handle the BagPacking "force deleted" event.
     */
    public function forceDeleted(BagPacking $bagPacking): void
    {
        //
    }
}
