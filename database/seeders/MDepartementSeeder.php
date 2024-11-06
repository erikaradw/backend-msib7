<?php

namespace Database\Seeders;

use App\Models\MDepartement;
use Carbon\Carbon;
use DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Schema;

class MDepartementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tableName = (new MDepartement)->getTable();
        if (Schema::hasTable($tableName)) {
            $rowCount = MDepartement::count();
            if ($rowCount > 0) {
                MDepartement::truncate();
            }
            $sequance = $tableName . "_id_seq";
            DB::statement("ALTER SEQUENCE $sequance RESTART WITH 1");
        }
        $scheme = [
            [
                "name" => 'IT',
                "code" => 'IT',
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now(),
            ],
            [
                "name" => 'MANAGEMENT',
                "code" => 'MNG',
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now(),
            ],
            [
                "name" => 'HRD',
                "code" => 'HR',
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now(),
            ]
        ];
        MDepartement::insert($scheme);
    }
}
