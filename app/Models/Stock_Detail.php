<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\{Request, JsonResponse};
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;


class Stock_Detail extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $table = 'stock__details'; // Nama tabel sesuai dengan migration
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
        $data = Stock_Detail::whereRaw("
            (lower(tahun) like '%$search%'
            OR lower(bulan) like '%$search%'
            OR lower(dist_code) like '%$search%'
            OR lower(kode_cabang) like '%$search%'
            OR lower(brch_name) like '%$search%'  
            OR lower(item_code) like '%$search%'  
            OR lower(on_hand_unit) like '%$search%' ) 
            AND deleted_by IS NULL
        ")
            ->select('id', 'tahun', 'bulan', 'dist_code', 'kode_cabang', 'brch_name', 'item_code', 'on_hand_unit')
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
        $data = Stock_Detail::whereRaw("
             (lower(tahun) like '%$search%'
            OR lower(bulan) like '%$search%'
            OR lower(dist_code) like '%$search%'
            OR lower(kode_cabang) like '%$search%'
            OR lower(brch_name) like '%$search%'  
            OR lower(item_code) like '%$search%'  
            OR lower(on_hand_unit) like '%$search%' ) 
            AND deleted_by IS NULL
        ")
            ->select('id', 'tahun', 'bulan', 'dist_code', 'kode_cabang', 'brch_name', 'item_code', 'on_hand_unit')
            ->orderBy('id', 'ASC')
            ->count();

        return $data;
    }

    public function insertBulk(Request $request): JsonResponse
    {
        $csvData = $request->input('csv');
        $user_id = $request->input('user_id');
        DB::beginTransaction();
        try {
            foreach ($csvData as $data) {
                $data['created_by'] = $user_id;
                $data['obj_type'] = '10';
                $todos = Sales_Unit::create($data);
            }
            DB::commit();
            return response()->json([
                'code' => 201,
                'status' => true,
                'message' => 'Success bulk insert',
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'code' => 403,
                'status' => false,
                'message' => 'Failed bulk insert',
                'e' => $e,
            ], 403);
        }
    }
}
