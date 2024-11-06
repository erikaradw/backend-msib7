<?php

namespace Database\Seeders;

use App\Models\M_Cabang;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class M_CabangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */


    public function run(): void
    {
        $tableName = (new M_Cabang)->getTable();
        if (Schema::hasTable($tableName)) {
            $rowCount = M_Cabang::count();
            if ($rowCount > 0) M_Cabang::truncate();

            $sequence = $tableName . "_id_seq";
            DB::statement("ALTER SEQUENCE $sequence RESTART WITH 1");
        };

        $scheme = [
            [
                'kode_cabang' => 'BDG1',
                'nama_cabang' => 'Bandung 1',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'kode_cabang' => 'BDG2',
                'nama_cabang' => 'Bandung 2',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'kode_cabang' => 'BDL',
                'nama_cabang' => 'Bandarlampung',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'kode_cabang' => 'BGR',
                'nama_cabang' => 'Bogor',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'kode_cabang' => 'BJM',
                'nama_cabang' => 'Banjarmasin',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'kode_cabang' => 'BKL',
                'nama_cabang' => 'Bengkulu',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'kode_cabang' => 'CRB',
                'nama_cabang' => 'Cirebon',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'kode_cabang' => 'DPS',
                'nama_cabang' => 'Denpasar-Bali',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'kode_cabang' => 'JBI',
                'nama_cabang' => 'Jambi',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'kode_cabang' => 'JKTBRT',
                'nama_cabang' => 'Jakarta Barat',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'kode_cabang' => 'JKTPST',
                'nama_cabang' => 'Jakarta Pusat',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'kode_cabang' => 'JKTSLT',
                'nama_cabang' => 'Jakarta Selatan',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'kode_cabang' => 'JKTTMR',
                'nama_cabang' => 'Jakarta Timur',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'kode_cabang' => 'MDN',
                'nama_cabang' => 'Medan',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'kode_cabang' => 'JMB',
                'nama_cabang' => 'Jember',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'kode_cabang' => 'KDR',
                'nama_cabang' => 'Kediri',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'kode_cabang' => 'KRW',
                'nama_cabang' => 'Karawang',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'kode_cabang' => 'MKS',
                'nama_cabang' => 'Makassar',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'kode_cabang' => 'MLG',
                'nama_cabang' => 'Malang',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'kode_cabang' => 'PDG',
                'nama_cabang' => 'Padang',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'kode_cabang' => 'PKB',
                'nama_cabang' => 'Pekanbaru',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'kode_cabang' => 'PLB',
                'nama_cabang' => 'Palembang',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'kode_cabang' => 'PTK',
                'nama_cabang' => 'Pontianak',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'kode_cabang' => 'PWT',
                'nama_cabang' => 'Purwokerto',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
            [
                'kode_cabang' => 'SBY1',
                'nama_cabang' => 'Surabaya 1',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => 'admin',
            ],
        ];
        M_Cabang::insert($scheme);
    }
}