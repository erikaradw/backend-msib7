<?php

namespace Database\Seeders;

use App\Models\M_Cabang;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call('UsersTableSeeder');
        $this->call(MDepartementSeeder::class);
        $this->call(KaryawanSeeder::class);
        // $this->call(M_CabangSeeder::class);
        $this->call(M_RegionSeeder::class);
        $this->call(M_OpsiCabangSeeder::class);
    }
}
