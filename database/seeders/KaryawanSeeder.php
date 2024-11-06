<?php

namespace Database\Seeders;

use App\Models\Karyawan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class KaryawanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tableName = (new Karyawan)->getTable();
        if (Schema::hasTable($tableName)) {
            $rowCount = Karyawan::count();
            if ($rowCount > 0) {
                Karyawan::truncate();
            }
            $sequance = $tableName . "_id_seq";
            DB::statement("ALTER SEQUENCE $sequance RESTART WITH 1");
        }
        $scheme = [
            [
                "nik" => '12121212',
                "name" => 'reza',
                "no_hp" => '081394558109',
                "umur" => 12,
                "code_departements" => "IT",
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now(),
            ],
            [
                "nik" => '13131313',
                "name" => 'zul',
                "no_hp" => '081394558199',
                "umur" => 17,
                "code_departements" => "HR",
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now(),
            ],
            [
                "nik" => '14151515',
                "name" => 'sob',
                "no_hp" => '081294558109',
                "umur" => 19,
                "code_departements" => "HR",
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now(),
            ]
        ];
        Karyawan::insert($scheme);
    }
}
