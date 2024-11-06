<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class MItempricehistory extends Model
{
    use SoftDeletes;
    protected $dates  = ['deleted_at'];
    protected $table = 'm_itempricehistory';
    protected $guarded = [];
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function getData()
    {
        $data = DB::select('SELECT * FROM ' . $this->table." WHERE  deleted_at IS NULL");
        return $data;
    }

    public function get_data_($search, $arr_pagination)
    {

            if (!empty($search))     $arr_pagination['offset'] = 0;
            $search = strtolower($search);        
            $data = MItempricehistory::whereRaw(" (lower(item_code) like '%$search%'
            OR lower(price_code) like '%$search%' 
            OR lower(mtg_code) like '%$search%' 
            OR lower(item_name) like '%$search%' 
            OR CAST(price AS TEXT) LIKE '%$search%'
            OR CAST(yop AS TEXT) like '%$search%'
            OR CAST(mop AS TEXT) like '%$search%') AND deleted_by is NULL")
                ->select('id','yop','mop','price_code','item_code','mtg_code','item_name','price')
                ->offset($arr_pagination['offset'])->limit($arr_pagination['limit'])
                ->orderBy('id','ASC')->get();
                return $data;
        
    }
}