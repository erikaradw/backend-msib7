<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\support\Facades\DB;
use DateTimeInterface;


class M_Region extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $table = 'm__regions';
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
        $data = M_Region::whereRaw("(lower(region_code) like '%$search%' OR lower(region_name) like '%$search%') AND deleted_by is NULL")
            ->select('id', 'region_code', 'region_name')
            ->offset($arr_pagination['offset'])
            ->limit($arr_pagination['limit'])
            ->orderBy('id', 'ASC')
            ->get();

        return $data;
    }
}
