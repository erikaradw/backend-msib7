<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class basoba extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $table = 'basobas'; // Nama tabel sesuai dengan migration
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
        $data = basoba::whereRaw("
            (lower(channel) like '%$search%'
            OR lower(distributor) like '%$search%'
            OR lower(region) like '%$search%'
            OR lower(area) like '%$search%'
            OR lower(cabang) like '%$search%'
            OR lower(parent_code) like '%$search%'
            OR lower(sku) like '%$search%'
            OR lower(brand) like '%$search%'
            OR lower(kategori) like '%$search%'
            OR lower(status_product) like '%$search%') 
            AND deleted_by IS NULL
        ")
            ->select('id', 'distributor', 'channel', 'region', 'area', 'cabang', 'parent_code', 'sku', 'brand', 'kategori', 'status_product')
            ->offset($arr_pagination['offset'])
            ->limit($arr_pagination['limit'])
            ->orderBy('id', 'ASC');
            

        return $data;
    }
}
