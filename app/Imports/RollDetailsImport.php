<?php

namespace App\Imports;

use App\Models\RollDetail;
use App\Models\VendorDetailMaster;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class RollDetailsImport implements ToCollection,WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */

    public function collection(Collection $rows)
    {
        $_M_RollDetail = new RollDetail();
        $_M_VendorDetail = new VendorDetailMaster();
        foreach ($rows as  $row) { 
            $row["vender_id"] = $_M_VendorDetail->where("vendor_name",$row["vendor_name"])->first()->id;
            $row["size"] = $row["roll_size"];
            $row["gsm"] = $row["roll_gsm"];
            $row["gsm_json"] = $row["bopp"] ? explode("/",$row["bopp"]):null;
            $row["length"] = $row["roll_length"];
            $row['roll_type'] = $row['roll_type']? $row['roll_type']: "NW";
            $request = new Request($row->toArray());
            $_M_RollDetail->store($request);
        }
    }

}
