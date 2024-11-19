<?php

namespace App\Http\Controllers;

use App\Models\POCust;
use App\Models\PublicModel;
use Illuminate\Http\{Request, JsonResponse};

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\str;
use Carbon\Support\Carbon;
use Exception;

use Illuminate\Support\Facades\URL;


class POCustController extends Controller
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
            $count =  (new POCust())->count();
            $arr_pagination = (new PublicModel())->pagination_without_search(
                $URL,
                $request->limit,
                $request->offset
            );
            $todos = (new POCust())->get_data_($request->search, $arr_pagination);
        } else {
            $arr_pagination = (new PublicModel())->pagination_without_search(
                $URL,
                $request->limit,
                $request->offset,
                $request->search
            );
            $todos =  (new POCust())->get_data_($request->search, $arr_pagination);
            $count = (new POCust())->count_data_($request->search);
        }
        return response()->json(
            (new PublicModel())->array_respon_200_table($todos, $count, $arr_pagination),
            200
        );
    }

    // public function paging(Request $request): JsonResponse
    // {
    //     $URL = URL::current();
    //     $sortColumn = $request->get('sort', 'tgl_order');
    //     $sortOrder = $request->get('order', 'asc');

    //     $validSortColumns = ['dist_code', 'tgl_order', 'mtg_code', 'qty_sc_reg', 'qty_po', 'branch_code'];
    //     if (!in_array($sortColumn, $validSortColumns)) {
    //         $sortColumn = 'tgl_order';
    //     }
    //     if (!in_array(strtolower($sortOrder), ['asc', 'desc'])) {
    //         $sortOrder = 'asc';
    //     }

    //     if (!isset($request->search)) {
    //         $query = (new POCust())->orderBy($sortColumn, $sortOrder);
    //         $count = $query->count();
    //         $arr_pagination = (new PublicModel())->pagination_without_search(
    //             $URL,
    //             $request->limit,
    //             $request->offset
    //         );
    //         $todos = $query->skip($arr_pagination['offset'])->take($arr_pagination['limit'])->get();
    //     } else {
    //         $search = $request->search;
    //         $query = (new POCust())
    //             ->where('dist_code', 'LIKE', "%$search%")
    //             ->orWhere('tgl_order', 'LIKE', "%$search%")
    //             ->orWhere('mtg_code', 'LIKE', "%$search%")
    //             ->orderBy($sortColumn, $sortOrder);
    //         $count = $query->count();
    //         $arr_pagination = (new PublicModel())->pagination_with_search(
    //             $URL,
    //             $request->limit,
    //             $request->offset,
    //             $request->search
    //         );
    //         $todos = $query->skip($arr_pagination['offset'])->take($arr_pagination['limit'])->get();
    //     }

    //     return response()->json(
    //         (new PublicModel())->array_respon_200_table($todos, $count, $arr_pagination),
    //         200
    //     );
    // }

    public function deletefilterpocust(Request $request)
    {
        // Validasi input
        $this->validate($request, [
            'dist_code' => 'required',
            'tahun' => 'required',
            'bulan' => 'required'
        ]);

        try {
            // Data filter
            $dist_code = $request->input('dist_code');
            $tahun = $request->input('tahun');
            $bulan = $request->input('bulan');

            // Hapus data berdasarkan filter (dengan parsing bulan dan tahun dari tgl_order)
            $deletedRows = DB::table('p_o_custs')
                ->where('dist_code', $dist_code)
                ->whereRaw("EXTRACT(YEAR FROM TO_DATE(tgl_order, 'MM-DD-YYYY')) = ?", [$tahun])
                ->whereRaw("EXTRACT(MONTH FROM TO_DATE(tgl_order, 'MM-DD-YYYY')) = ?", [$bulan])
                ->delete();

            // Response sukses
            return response()->json([
                'status' => true,
                'message' => 'Data deleted successfully.',
                'deleted_rows' => $deletedRows
            ]);
        } catch (\Exception $e) {
            // Response gagal
            return response()->json([
                'status' => false,
                'message' => 'Failed to delete data: ' . $e->getMessage()
            ], 500);
        }
    }


    public function store(Request $request): JsonResponse
    {
        DB::beginTransaction();
        $user_id = 'USER TEST';
        $data = $this->validate($request, [
            'dist_code' => 'required',
            'tgl_order' => 'required',
            'mtg_code'   => 'required',
            'qty_sc_reg' => 'required',
            'qty_po' => 'required',
            'branch_code' => 'required',
        ]);

        try {
            $data['created_by'] = $user_id;
            $todos = POCust::create($data);

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
            $rowCount = DB::table('p_o_custs')->count();
            DB::table('p_o_custs')->truncate();

            Log::info('All data in p_o_custs table has been deleted.', ['row_count' => $rowCount]);

            return response()->json([
                'status' => true,
                'message' => 'All data deleted successfully',
                'deleted_rows' => $rowCount
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to delete all data from p_o_custs table.', [
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
            $todo = POCust::where('data_baru', true)->orWhere('data_baru', null);
            $todo->update(['data_baru' => false]);
            $user_id = 'USER TEST'; // Sesuaikan dengan ID pengguna yang sebenarnya
            $data_csv = json_decode(json_encode($req->csv), true);
            foreach ($data_csv as $key => $value) {
                $data = array();
                $data['dist_code'] = $value['dist_code'];
                $data['tgl_order'] = $value['tgl_order'];
                $data['mtg_code'] = $value['mtg_code'];
                $data['qty_sc_reg'] = $value['qty_sc_reg'];
                $data['qty_po'] = $value['qty_po'];
                $data['branch_code'] = $value['branch_code'];
                $data['data_baru'] = true;

                $data['created_by'] = $req->userid;
                $data['updated_by'] = $req->userid;
                $todos = POCust::create($data);
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

    public function destroy(Request $request): JsonResponse
    {
        $user_id = 'USER TEST';
        try {
            $id = $request->input('id', null);
            if ($id) {
                $todo = POCust::where('id', $id);
                $todo->delete();
            } else {
                $todo = POCust::where('data_baru', true);
                $todo->update(['deleted_by' => $user_id]);

                $todo->delete();
            }
            return response()->json([
                'code' => 201,
                'status' => true,
                'message' => 'deleted succesfully',
            ], 201);
        } catch (\Exception $e) {

            return response()->json([
                'code' => 409,
                'status' => false,
                'message' => 'delete failed',
                'e' => $e,
            ], 409);
        }
    }

    // public function destroy(Request $request, int $id): JsonResponse
    // {
    //     DB::beginTransaction();
    //     $user_id = 'USER TEST';

    //     try {
    //         $todo = POCust::findOrFail($id);

    //         POCust::where('id', $id)->update(['deleted_by' => $user_id]);
    //         $todo->delete();

    //         DB::commit();
    //         return response()->json([
    //             'code' => 201,
    //             'status' => true,
    //             'message' => 'deleted succesfully',
    //         ], 201);
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return response()->json([
    //             'code' => 409,
    //             'status' => false,
    //             'message' => 'delete failed',
    //             'e' => $e,
    //         ], 409);
    //     }
    // }

    public function show(int $id): JsonResponse
    {
        try {
            $todos = POCust::findOrFail($id);
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
            'dist_code' => 'required',
            'tgl_order' => 'required',
            'mtg_code'   => 'required',
            'qty_sc_reg' => 'required',
            'qty_po' => 'required',
            'branch_code' => 'required',
        ]);

        try {
            $todo = POCust::findOrFail($id);
            $todo->fill($data);
            $todo->save();

            POCust::where('id', $id)->update(['updated_by' => $user_id, 'updated_at' => date('Y-m-d H:i:s')]);

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
