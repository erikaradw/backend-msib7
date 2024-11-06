<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\support\Facades\DB;
use DateTimeInterface;


class M_Kategori extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $table = 'm__kategoris';
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
        $data = M_Kategori::whereRaw("(lower(parent_code) like '%$search%' OR lower(item_name) like '%$search%' OR lower(kategori) like '%$search%') AND deleted_by is NULL")
            ->select('id', 'parent_code', 'item_name', 'kategori')
            ->offset($arr_pagination['offset'])
            ->limit($arr_pagination['limit'])
            ->orderBy('id', 'ASC')
            ->get();

        return $data;
    }
}
