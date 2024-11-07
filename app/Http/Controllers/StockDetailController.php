<?php

namespace App\Http\Controllers;

use App\Models\Stock_Detail;
use App\Models\PublicModel;
use Illuminate\Http\{Request, JsonResponse};

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use League\Csv\Reader;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\str;
use Carbon\Support\Carbon;
use Exception;

use Illuminate\Support\Facades\URL;


class StockDetailController extends Controller
{
    protected $judul_halaman_notif;
    public function __construct()
    {
        $this->judul_halaman_notif = "MASTER USER";
    }

    public function paging(Request $request): JsonResponse
    {
        $URL = URL::current();
        if (!isset($request->search)) {
            $count =  (new Stock_Detail())->count();
            $arr_pagination = (new PublicModel())->pagination_without_search(
                $URL,
                $request->limit,
                $request->offset
            );
            $todos = (new Stock_Detail())->get_data_($request->search, $arr_pagination);
        } else {
            $arr_pagination = (new PublicModel())->pagination_without_search(
                $URL,
                $request->limit,
                $request->offset,
                $request->search
            );
            $todos =  (new Stock_Detail())->get_data_($request->search, $arr_pagination);
            $count = $todos->count();
        }
        return response()->json(
            (new PublicModel())->array_respon_200_table($todos, $count, $arr_pagination),
            200
        );
    }
    public function store(Request $request): JsonResponse
    {
        DB::beginTransaction();
        $user_id = 'USER TEST';
        $data = $this->validate($request, [
            'tahun' => 'required',
            'bulan' => 'required',
            'dist_code'   => 'required',
            'kode_cabang'   => 'required',
            'brch_name' => 'required',
            'item_code' => 'required',
            'on_hand_unit' => 'required',
        ]);

        try {
            $data['created_by'] = $user_id;
            $todos = Stock_Detail::create($data);

            DB::commit();
            return response()->json([
                'code' => 201,
                'status' => true,
                'message' => 'created succesfully',
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'code' => 403,
                'status' => false,
                'message' => 'created failed',
                'e' => $e,
            ], 403);
        }
    }
    public function deleteAll()
    {
        try {
            $rowCount = DB::table('stock__details')->count();
            DB::table('stock__details')->truncate();

            Log::info('All data in sales_units table has been deleted.', ['row_count' => $rowCount]);

            return response()->json([
                'status' => true,
                'message' => 'All data deleted successfully',
                'deleted_rows' => $rowCount
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to delete all data from sales_units table.', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Failed to delete data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function storeBulky(Request $req): JsonResponse
    {
        DB::beginTransaction();
        try {
            $user_id = 'USER TEST'; // Sesuaikan dengan ID pengguna yang sebenarnya
            $data_csv = json_decode(json_encode($req->csv), true);
            foreach ($data_csv as $key => $value) {
                $data = array();
                $data['tahun'] = $value['tahun'];
                $data['bulan'] = $value['bulan'];
                $data['dist_code'] = $value['dist_code'];
                $data['kode_cabang'] = $value['kode_cabang'];
                $data['brch_name'] = $value['brch_name'];
                $data['item_code'] = $value['item_code'];
                $data['on_hand_unit'] = $value['on_hand_unit'];

                $data['created_by'] = $req->userid;
                $data['updated_by'] = $req->userid;
                $todos = Stock_Detail::create($data);
            }
            DB::commit();
            return response()->json([
                'code' => 201,
                'status' => true,
                'message' => 'created successfully',
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'failed to create data',
                'error' => $e
            ], 403);
        }
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        DB::beginTransaction();
        $user_id = 'USER TEST';

        try {
            $todo = Stock_Detail::findOrFail($id);

            Stock_Detail::where('id', $id)->update(['deleted_by' => $user_id]);
            $todo->delete();

            DB::commit();
            return response()->json([
                'code' => 201,
                'status' => true,
                'message' => 'deleted succesfully',
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'code' => 409,
                'status' => false,
                'message' => 'delete failed',
                'e' => $e,
            ], 409);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $todos = Stock_Detail::findOrFail($id);
            return response()->json([
                'code' => 200,
                'status' => true,
                'data' => $todos
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 404,
                'status' => false,
                'message' => 'failed get data',
                'e' => $e,
            ], 404);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        DB::beginTransaction();
        $user_id = 'USER TEST';
        $data = $this->validate($request, [
            'tahun' => 'required',
            'bulan' => 'required',
            'dist_code'   => 'required',
            'kode_cabang'   => 'required',
            'brch_name' => 'required',
            'item_code' => 'required',
            'on_hand_unit' => 'required',
        ]);

        try {
            $todo = Stock_Detail::findOrFail($id);
            $todo->fill($data);
            $todo->save();

            Stock_Detail::where('id', $id)->update(['updated_by' => $user_id, 'updated_at' => date('Y-m-d H:i:s')]);

            DB::commit();
            return response()->json([
                'code' => 201,
                'status' => true,
                'message' => 'updated succesfully',
                'data' => $todo
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'code' => 409,
                'status' => false,
                'message' => 'update failed' . $e,
            ], 409);
        }
    }
}