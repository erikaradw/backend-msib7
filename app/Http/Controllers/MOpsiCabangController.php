<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\M_OpsiCabang;
use Exception;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;
use App\Models\PublicModel;
use Illuminate\Http\{Request, JsonResponse};

class MOpsiCabangController extends Controller
{
    // Method untuk mengambil semua warehouse
    public function index()
    {
        $warehouses = M_OpsiCabang::all();
        return response()->json($warehouses);
    }

    protected $judul_halaman_notif;
    public function __construct()
    {
        $this->judul_halaman_notif = 'MASTER DISTRIBUTION CODE';
    }

    public function paging(Request $request): JsonResponse
    {
        $URL = URL::current();
        if (!isset($request->search)) {
            $count = (new M_OpsiCabang())->count();
            $arr_pagination = (new PublicModel())->pagination_without_search($URL, $request->limit, $request->offset);
            $todos = (new M_OpsiCabang())->get_data_($request->search, $arr_pagination);
        } else {
            $arr_pagination = (new PublicModel())->pagination_without_search($URL, $request->limit, $request->offset, $request->search);
            $todos = (new M_OpsiCabang())->get_data_($request->search, $arr_pagination);
            $count = $todos->count();
        }

        return response()->json(
            (new PublicModel())->array_respon_200_table($todos, $count, $arr_pagination),
            200
        );
    }

    public function getData()
    {
        try {
            $getAllData = M_OpsiCabang::with(["opsi cabang"])->get();
            return response()->json($getAllData, 200);
        } catch (Exception $e) {
            return response()->json([$e->getMessage()], 400);
        }
    }

    // Method untuk menambahkan warehouse baru
    public function store(Request $request): JsonResponse
    {
        DB::beginTransaction();
        $user_id = 'USER TEST';
        $data = $this->validate($request, [
            'dist_code' => 'required',
            'dist_name' => 'required',


        ]);

        try {
            $data['created_by'] = $user_id;
            $todos = M_OpsiCabang::create($data);

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

    public function getAllData()
    {
        try {
            $todo = M_OpsiCabang::orderBy('dist_code', 'asc')->get();
            return response()->json([
                'code' => 200,
                'status' => true,
                'data' => $todo
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 409,
                'status' => false,
                'message' => $e
            ], 409);
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
                $data['dist_code'] = $value['dist_code'];
                $data['dist_name'] = $value['dist_name'];
                $data['created_by'] = $req->userid;
                $data['updated_by'] = $req->userid;
                $todos = M_OpsiCabang::create($data);
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
    public function deleteAll()
    {
        try {
            $rowCount = DB::table('m__opsi_cabangs')->count();
            DB::table('m__opsi_cabangs')->truncate();

            Log::info('All data in m__opsi_cabangs table has been deleted.', ['row_count' => $rowCount]);

            return response()->json([
                'status' => true,
                'message' => 'All data deleted successfully',
                'deleted_rows' => $rowCount
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to delete all data from m__opsi_cabangs table.', [
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

    public function destroy(Request $request, int $id): JsonResponse
    {
        DB::beginTransaction();
        $user_id = 'USER TEST';

        try {
            $todo = M_OpsiCabang::findOrFail($id);

            M_OpsiCabang::where('id', $id)->update(['deleted_by' => $user_id]);
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

    public function update(Request $request, int $id): JsonResponse
    {
        DB::beginTransaction();
        $user_id = 'USER TEST';
        $data = $this->validate($request, [
            'dist_code' => "required",
            'dist_name' => 'required',


        ]);

        try {
            $todo = M_OpsiCabang::findOrFail($id);
            $todo->fill($data);
            $todo->save();

            M_OpsiCabang::where('id', $id)->update(['updated_by' => $user_id, 'updated_at' => date('Y-m-d H:i:s')]);

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
                'message' => 'update failed',

            ], 409);
        }
    }


    public function show(int $id): JsonResponse
    {
        try {
            $todos = M_OpsiCabang::findOrFail($id);
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
}