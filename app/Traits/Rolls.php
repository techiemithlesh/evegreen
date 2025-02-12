<?php
namespace App\Traits;

use App\Models\RollDetail;
use App\Models\RollTransit;
use Illuminate\Support\Facades\DB;

trait Rolls{
    public function getRollStockORM(){
        return RollDetail::select("roll_details.*",DB::raw("'stock' as stock, client_detail_masters.client_name,vendor_detail_masters.vendor_name,roll_quality_masters.quality"))
                ->leftJoin("client_detail_masters","client_detail_masters.id","roll_details.client_detail_id")
                ->leftJoin("vendor_detail_masters","vendor_detail_masters.id","roll_details.vender_id")
                ->leftJoin("roll_quality_masters","roll_quality_masters.id","roll_details.quality_id")
                ->where("roll_details.is_cut",false)
                ->where("roll_details.is_printed",false)
                ->where("roll_details.lock_status",false);
    }

    public function getRollTransitORM(){
        return RollTransit::select("roll_transits.*",DB::raw("'transit' as stock, client_detail_masters.client_name,vendor_detail_masters.vendor_name,roll_quality_masters.quality"))
                ->leftJoin("client_detail_masters","client_detail_masters.id","roll_transits.client_detail_id")
                ->leftJoin("vendor_detail_masters","vendor_detail_masters.id","roll_transits.vender_id")
                ->leftJoin("roll_quality_masters","roll_quality_masters.id","roll_transits.quality_id")
                ->where("roll_transits.is_cut",false)
                ->where("roll_transits.is_printed",false)
                ->where("roll_transits.lock_status",false);
    }
}