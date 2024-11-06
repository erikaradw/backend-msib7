<?php

namespace Database\Seeders;

use App\Models\M_OpsiCabang;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class M_OpsiCabangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */


    public function run(): void
    {
        $tableName = (new M_OpsiCabang)->getTable();
        if (Schema::hasTable($tableName)) {
            $rowCount = M_OpsiCabang::count();
            if ($rowCount > 0) M_OpsiCabang::truncate();

            $sequence = $tableName . "_id_seq";
            DB::statement("ALTER SEQUENCE $sequence RESTART WITH 1");
        };

        $scheme = [
            [
                'dist_code' => 'TRS',
                'dist_name' => 'TRS',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'dist_code' => 'PVL',
                'dist_name' => 'PVL',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'dist_code' => 'PPG',
                'dist_name' => 'PPG',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            
        ];
        M_OpsiCabang::insert($scheme);
    }
}