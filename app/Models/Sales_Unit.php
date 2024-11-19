<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sales_Unit extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $table = 'sales__units'; // Nama tabel sesuai dengan migration
    protected $guarded = []; // Tidak ada kolom yang dilarang untuk mass assignment

    /**
     * Menentukan format tanggal yang digunakan untuk kolom created_at, updated_at, dan deleted_at.
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    /**
     * Mendapatkan data dengan pencarian dan paginasi
     * @param string $search
     * @param array $arr_pagination
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function get_data_($search, $arr_pagination)
    {
        // Jika ada pencarian, reset offset pagination ke 0
        if (!empty($search)) {
            $arr_pagination['offset'] = 0;
        }

        $search = strtolower($search);

        // Query dengan pencarian dan paginasi
        $data = Sales_Unit::whereRaw("
            (lower(tahun) like '%$search%'
            OR lower(bulan) like '%$search%'
            OR lower(dist_code) like '%$search%'
            OR lower(chnl_code) like '%$search%' 
            OR lower(kode_cabang) like '%$search%' 
            OR lower(brch_name) like '%$search%'
            OR lower(item_code) like '%$search%'
            OR lower(net_sales_unit) like '%$search%'
            OR lower(cust_code) like '%$search%' )
            AND deleted_by IS NULL
        ")
            ->select('id', 'tahun', 'bulan', 'dist_code', 'chnl_code','kode_cabang','brch_name', 'item_code', 'net_sales_unit', 'cust_code')
            ->offset($arr_pagination['offset'])
            ->limit($arr_pagination['limit'])
            ->orderBy('id', 'ASC')
            ->get();

        return $data;
    }

    public function count_data_($search)
    {
        // Jika ada pencarian, reset offset pagination ke 0
        if (!empty($search)) {
            $arr_pagination['offset'] = 0;
        }

        $search = strtolower($search);

        // Query dengan pencarian dan paginasi
        $data = Sales_Unit::whereRaw("
            (lower(tahun) like '%$search%'
            OR lower(bulan) like '%$search%'
            OR lower(dist_code) like '%$search%'
            OR lower(chnl_code) like '%$search%' 
            OR lower(kode_cabang) like '%$search%' 
            OR lower(brch_name) like '%$search%'
            OR lower(item_code) like '%$search%'
            OR lower(net_sales_unit) like '%$search%'
            OR lower(cust_code) like '%$search%' )
            AND deleted_by IS NULL
        ")
            ->select('id', 'tahun', 'bulan', 'dist_code', 'chnl_code','kode_cabang','brch_name', 'item_code', 'net_sales_unit', 'cust_code')
            ->orderBy('id', 'ASC')
            ->count();

        return $data;
    }
}
