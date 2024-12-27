<?php

namespace App\Observers;

use App\Models\RollDetail;
use App\Models\RollTransit;
use Carbon\Carbon;

class RollTransitObserver
{
    /**
     * Handle the RollTransit "created" event.
     */
    public function created(RollTransit $rollTransit): void
    {
        if(!$rollTransit->roll_no){
            $purchaseDate = Carbon::parse($rollTransit->purchase_date);
            $rolNo = $purchaseDate->clone()->format("m")."/".$purchaseDate->clone()->format("y")."-";
            $sl = RollTransit::where("purchase_date",$rollTransit->purchase_date)->count("id");
            $sl2 = RollDetail::where("purchase_date",$rollTransit->purchase_date)->count("id");
            $sl = $sl+$sl2;
            $slNo="";
            while(true){   
                $slNo = str_pad((string)$sl,4,"0",STR_PAD_LEFT);
                $sl=($sl+1);             
                $test = RollTransit::where("roll_no",$rolNo.$slNo)->count();
                $test2 = RollDetail::where("roll_no",$rolNo.$slNo)->count();
                if((!$test) && (!$test2)){                    
                    $rolNo.=$slNo;
                    break;
                }
            }
            $rollTransit->roll_no  = $rolNo;
        }
        if(!$rollTransit->gsm_variation){
            $rollTransit->gsm_variation = (((($rollTransit->net_weight * 39.37 * 1000) / $rollTransit->size)/$rollTransit->length)-$rollTransit->gsm)/$rollTransit->gsm;
        }
        $rollTransit->save();
    }

    /**
     * Handle the RollTransit "updated" event.
     */
    public function updated(RollTransit $rollTransit): void
    {
        //
    }

    /**
     * Handle the RollTransit "deleted" event.
     */
    public function deleted(RollTransit $rollTransit): void
    {
        //
    }

    /**
     * Handle the RollTransit "restored" event.
     */
    public function restored(RollTransit $rollTransit): void
    {
        //
    }

    /**
     * Handle the RollTransit "force deleted" event.
     */
    public function forceDeleted(RollTransit $rollTransit): void
    {
        //
    }
}