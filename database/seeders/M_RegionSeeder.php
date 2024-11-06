<?php

namespace Database\Seeders;

use App\Models\M_Region;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class M_RegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */


    public function run(): void
    {
        $tableName = (new M_Region)->getTable();
        if (Schema::hasTable($tableName)) {
            $rowCount = M_Region::count();
            if ($rowCount > 0) M_Region::truncate();

            $sequence = $tableName . "_id_seq";
            DB::statement("ALTER SEQUENCE $sequence RESTART WITH 1");
        };

        $scheme = [
            [
                'region_code' => 'SMT',
                'region_name' => 'SUMATERA',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'region_code' => 'JWA',
                'region_name' => 'JAWA',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'region_code' => 'KAL',
                'region_name' => 'KALIMANTAN',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'region_code' => 'SLW',
                'region_name' => 'SULAWESI',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            
        ];
        M_Region::insert($scheme);
    }
}