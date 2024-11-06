<?php

namespace App\Imports;

use App\Models\Excel;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;

class ImportExcel implements ToModel
{
    /**
     * @param array $row
     *
     * @return Excel|null
     */
    public function model(array $row)
    {
        return new Excel([
           'name'     => $row[0],
           'email'    => $row[1], 
           'password' => Hash::make($row[2]),
        ]);
    }
}