<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\support\Facades\DB;
use DateTimeInterface;


class M_Cabang extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $table = 'm__cabangs';
    protected $guarded = [];



    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
    public function get_data_($search, $arr_pagination)
    {
        $query = M_Cabang::whereNull('deleted_by');

        // Tambahkan kondisi pencarian jika search term ada
        if (!empty($search)) {
            $search = strtolower($search);
            $query->whereRaw("(lower(kode_cabang) like '%$search%' OR 
                              lower(nama_cabang) like '%$search%' OR 
                              lower(branch_code) like '%$search%' OR 
                              lower(dist_code) like '%$search%' OR 
                              lower(area_code) like '%$search%' OR 
                              lower(area_name) like '%$search%' OR 
                              lower(region_code) like '%$search%' OR 
                              lower(region_name) like '%$search%')");
        }

        // Ambil data sesuai dengan limit dan offset
        $data = $query->select('id', 'kode_cabang', 'nama_cabang', 'branch_code', 'dist_code', 'area_code', 'area_name', 'region_code', 'region_name')
            ->offset($arr_pagination['offset'])
            ->limit($arr_pagination['limit'])
            ->orderBy('id', 'ASC')
            ->get();

        return $data;
    }

    public function count_data_($search)
    {
        $query = M_Cabang::whereNull('deleted_by');

        // Tambahkan kondisi pencarian jika search term ada
        if (!empty($search)) {
            $search = strtolower($search);
            $query->whereRaw("(lower(kode_cabang) like '%$search%' OR 
                              lower(nama_cabang) like '%$search%' OR 
                              lower(branch_code) like '%$search%' OR 
                              lower(dist_code) like '%$search%' OR 
                              lower(area_code) like '%$search%' OR 
                              lower(area_name) like '%$search%' OR 
                              lower(region_code) like '%$search%' OR 
                              lower(region_name) like '%$search%')");
        }

        // Hitung total data hasil search
        $count = $query->count();

        return $count;
    }
    // public function get_data_($search, $arr_pagination)
    // {
    //     if (!empty($search)) {
    //         $arr_pagination['offset'] = 0;
    //     }

    //     // Pastikan pencarian dilakukan sesuai dengan struktur database kamu
    //     $search = strtolower($search);
    //     $data = M_Cabang::whereRaw("(lower(kode_cabang) like '%$search%' OR lower(nama_cabang) like '%$search%'  OR lower(branch_code) like '%$search%' OR lower(dist_code) like '%$search%' OR lower(area_code) like '%$search%' OR lower(area_name) like '%$search%' OR lower(region_code) like '%$search%' OR lower(region_name) like '%$search%') AND deleted_by is NULL")
    //         ->select('id', 'kode_cabang', 'nama_cabang', 'branch_code', 'dist_code', 'area_code', 'area_name', 'region_code', 'region_name')
    //         ->offset($arr_pagination['offset'])
    //         ->limit($arr_pagination['limit'])
    //         ->orderBy('id', 'ASC')
    //         ->get();

    //     return $data;
    // }
    // public function count_data_($search, $arr_pagination)
    // {
    //     if (!empty($search)) {
    //         $arr_pagination['offset'] = 0;
    //     }

    //     // Pastikan pencarian dilakukan sesuai dengan struktur database kamu
    //     $search = strtolower($search);
    //     $data = M_Cabang::whereRaw("(lower(kode_cabang) like '%$search%' OR lower(nama_cabang) like '%$search%'  OR lower(branch_code) like '%$search%' OR lower(dist_code) like '%$search%' OR lower(area_code) like '%$search%' OR lower(area_name) like '%$search%' OR lower(region_code) like '%$search%' OR lower(region_name) like '%$search%') AND deleted_by is NULL")
    //         ->select('id', 'kode_cabang', 'nama_cabang', 'branch_code', 'dist_code', 'area_code', 'area_name', 'region_code', 'region_name')
    //         ->orderBy('id', 'ASC')
    //         ->count();

    //     return $data;
    // }
}
