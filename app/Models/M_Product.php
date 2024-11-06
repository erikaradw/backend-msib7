<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\support\Facades\DB;
use DateTimeInterface;
use Illuminate\Http\{Request, JsonResponse};
use Illuminate\Support\Facades\Log;


class M_Product extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $table = 'm__products';
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
        $data = M_Product::whereRaw("(lower(brand_code) like '%$search%' OR lower(brand_name) like '%$search%' OR lower(parent_code) like '%$search%'  OR lower(item_code) like '%$search%' OR lower(item_name) like '%$search%' OR lower(price_code) like '%$search%' OR lower(price) like '%$search%' OR lower(status_product) like '%$search%') AND deleted_by is NULL")
            ->select('id', 'brand_code', 'brand_name', 'parent_code','item_code', 'item_name', 'price_code', 'price', 'status_product')
            ->offset($arr_pagination['offset'])
            ->limit($arr_pagination['limit'])
            ->orderBy('id', 'ASC')
            ->get();

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
