<?php

namespace App\Imports;

use App\Models\RollDetail;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
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
        foreach ($rows as  $row) {  
            $request = new Request($row->toArray());
            $_M_RollDetail->store($request);
        }
    }

}
