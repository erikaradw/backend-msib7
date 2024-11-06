<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class M_Price extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    // Table name
    protected $table = 'm_price';
    protected $guarded = [];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }


    public function get_data_($search, $arr_pagination)
    {
        if (!empty($search)) $arr_pagination['offset'] = 0;
        $search = strtolower($search);
        $data = M_Price::whereRaw("( lower(price_code) like '%$search%' OR lower (price_name) like '%$search%') AND deleted_by is NULL")
            ->select('id', 'price_code', 'price_name', 'flag_active')
            ->offset($arr_pagination['offset'])->limit($arr_pagination['limit'])
            ->orderBy('id', 'ASC')->get();
        return $data;
        // OR lower (obj_type) like '%$search%')
    }
}