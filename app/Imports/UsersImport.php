<?php

namespace App\Imports;

use App\Models\stm;
// use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;

class UsersImport implements ToModel
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new stm([
            'distributor'=> $row[0],
            'channel'    => $row[1],
            'region'     => $row[2],
            'area'       => $row[3],
            'cabang'     => $row[4],
            'parent_code'=> $row[5],
            'sku'        => $row[6],
            'brand'      => $row[7],
            'kategori'   => $row[8],
            'status_product'=> $row[9],

        ]);
    }
}
