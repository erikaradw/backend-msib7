<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\support\Facades\DB;
use DateTimeInterface;


class M_Customer extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $table = 'm__customers';
    protected $guarded = [];



    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function get_data_($search, $arr_pagination)
    {
        if (!empty($search)) {
            $arr_pagination['offset'] = 0;
        }

        // Pastikan pencarian dilakukan sesuai dengan struktur database kamu
        $search = strtolower($search);
        $data = M_Customer::whereRaw("(lower(dist_code) like '%$search%' OR lower(region_name) like '%$search%' OR lower(area_code) like '%$search%' OR lower(kode_cabang) like '%$search%' OR lower(cust_code) like '%$search%' OR lower(cust_name) like '%$search%' OR lower(chnl_code) like '%$search%' OR lower(item_code) like '%$search%' OR lower(kota) like '%$search%' OR lower(provinsi) like '%$search%') AND deleted_by is NULL")
            ->select('id', 'dist_code', 'region_name', 'area_code', 'kode_cabang', 'cust_code', 'cust_name', 'chnl_code', 'item_code', 'kota', 'provinsi')
            ->offset($arr_pagination['offset'])
            ->limit($arr_pagination['limit'])
            ->orderBy('id', 'ASC')
            ->get();

        return $data;
    }
}
