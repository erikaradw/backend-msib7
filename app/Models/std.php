<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class std extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $table = 'stds'; // Nama tabel sesuai dengan migration
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
        $data = std::whereRaw("
            (lower('yop') like '%$search%'
            OR lower('mop') like '%$search%'
            OR lower('distCode') like '%$search%'
            OR lower('brchName') like '%$search%'
            OR lower('itemCode') like '%$search%'
            OR lower('onHandUnit') like '%$search%')
            AND deleted_by IS NULL
        ")
            ->select('id', 'yop', 'mop', 'distCode', 'brchName', 'itemCode', 'onHandUnit')
            ->offset($arr_pagination['offset'])
            ->limit($arr_pagination['limit'])
            ->orderBy('id', 'ASC');

        return $data;
    }
}
