<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\JsonResponse;
use App\Models\stm;
use App\Models\PublicModel;

use Maatwebsite\Excel\Facades\Excel;
use App\Imports\YourDataImport;

class StmController extends Controller
{
    protected $judul_halaman_notif;

    public function __construct()
    {
        $this->judul_halaman_notif = 'Trend_g';
    }



    public function paging(Request $request): JsonResponse
    {
        $URL = URL::current();
        if (!isset($request->search)) {
            $count = (new stm())->count();
            $arr_pagination = (new PublicModel())->pagination_without_search(
                $URL,
                $request->limit,
                $request->offset
            );
            $todos = (new stm())->get_data_($request->search, $arr_pagination);
        } else {
            $arr_pagination = (new PublicModel())->pagination_without_search(
                $URL,
                $request->limit,
                $request->offset,
                $request->search
            );
            $todos = (new stm())->get_data_($request->search, $arr_pagination);
            $count = $todos->count();
        }
        if ($request->tahun) {
            $todos = $todos->where('tahun', '=', $request->tahun);
        }
        if ($request->distCode) {
            $todos = $todos->where('distCode', '=', $request->distCode);
        }
        if ($request->brchName) {
            $todos = $todos->where('brchName', '=', $request->brchName);
        }
        if ($request->bulan) {
            $todos = $todos->where('bulan', '=', $request->bulan);
        }
        if ($request->chnlCode) {
            $todos = $todos->where('chnlCode', '=', $request->chnlCode);
        }
        if ($request->itemCode) {
            $todos = $todos->where('itemCode', '=', $request->itemCode);
        }
        if ($request->netSalesUnit) {
            $todos = $todos->where('netSalesUnit', '=', $request->netSalesUnit);
        }


        return response()->json(
            (new PublicModel())->array_respon_200_table($todos->get('*'), $count, $arr_pagination),
            200
        );
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
                $data['distCode'] = $value['distCode'];
                $data['chnlCode'] = $value['chnlCode'];
                $data['brchName'] = $value['brchName'];
                $data['itemCode'] = $value['itemCode'];
                $data['netSalesUnit'] = $value['netSalesUnit'];
                $data['created_by'] = $req->userid;
                $data['updated_by'] = $req->userid;
                $todos = stm::create($data);
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

    public function pagingBulky(Request $request)
    {
        $URL = URL::current();
        $limit = $request->limit ?? 10;  // Default limit jika tidak disediakan
        $offset = $request->offset ?? 0; // Default offset jika tidak disediakan

        // Cek apakah request adalah untuk ekspor CSV
        if ($request->has('export_csv') && $request->export_csv == true) {
            // Ambil semua data sesuai dengan pencarian jika ada
            $data = stm::when($request->search, function ($query) use ($request) {
                $search = $request->search;
                return $query->where('tahun', 'like', "%$search%")
                    ->orWhere('bulan', 'like', "%$search%")
                    ->orWhere('distCode', 'like', "%$search%")
                    ->orWhere('chnlCode', 'like', "%$search%")
                    ->orWhere('brchName', 'like', "%$search%")
                    ->orWhere('itemCode', 'like', "%$search%")
                    ->orWhere('netSalesUnit', 'like', "%$search%")
                ;
            })->get();

            // Header CSV
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="export_data.csv"',
            ];

            // Callback untuk membuat file CSV
            $callback = function () use ($data) {
                $file = fopen('php://output', 'w');
                fwrite($file, "\xEF\xBB\xBF"); // Menambahkan BOM UTF-8

                // Tulis header kolom CSV
                fputcsv($file, [
                    'ID',
                    'Created At',
                    'Updated At',
                    'tahun',
                    'bulan',
                    'distCode',
                    'chnlCode',
                    'brchName',
                    'itemCode',
                    'netSalesUnit',
                    'Created By',
                    'Updated By',
                    'Deleted By',
                    'Deleted At'
                ]);

                // Tulis data ke file CSV
                foreach ($data as $row) {
                    fputcsv($file, [
                        $row->id,
                        $row->created_at,
                        $row->updated_at,
                        trim(str_replace(array("\r", "\n"), ' ', $row->tahun)),
                        trim(str_replace(array("\r", "\n"), ' ', $row->bulan)),
                        trim(str_replace(array("\r", "\n"), ' ', $row->distCode)),
                        trim(str_replace(array("\r", "\n"), ' ', $row->chnlCode)),
                        trim(str_replace(array("\r", "\n"), ' ', $row->brchName)),
                        trim(str_replace(array("\r", "\n"), ' ', $row->itemCode)),
                        trim(str_replace(array("\r", "\n"), ' ', $row->netSalesUnit)),
                        $row->created_by,
                        $row->updated_by,
                        $row->deleted_by,
                        $row->deleted_at,
                    ]);
                }

                fclose($file);
            };

            // Return response sebagai stream dengan header CSV
            return response()->stream($callback, 200, $headers);
        }

        // Logika paging jika tidak ekspor CSV
        if (!isset($request->search)) {
            // Hitung total data jika tidak ada pencarian
            $count = stm::count();
            // Lakukan pagination tanpa pencarian
            $arr_pagination = (new PublicModel())->pagination_without_search($URL, $limit, $offset);
            $todos = stm::skip($offset)->take($limit)->get();
        } else {
            // Lakukan pagination dengan pencarian
            $search = $request->search;
            $todos = stm::where('tahun', 'like', "%$search%")
                ->orWhere('bulan', 'like', "%$search%")
                ->orWhere('distCode', 'like', "%$search%")
                ->orWhere('chnlCode', 'like', "%$search%")
                ->orWhere('brchName', 'like', "%$search%")
                ->orWhere('itemCode', 'like', "%$search%")
                ->orWhere('netSalesUnit', 'like', "%$search%")
                ->skip($offset)
                ->take($limit)
                ->get();
            $count = $todos->count();
        }

        return response()->json(
            (new PublicModel())->array_respon_200_table($todos, $count, $arr_pagination),
            200
        );
    }

    public function getAllData(): JsonResponse
    {
        try {
            $todo = stm::get();
            return response()->json([
                'code' => 200,
                'status' => true,
                'message' => "$this->judul_halaman_notif success get data",
                'results' => $todo
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 409,
                'status' => false,
                'message' => "$this->judul_halaman_notif failed get data",
            ], 409);
        }
    }

    public function store(Request $req): JsonResponse
    {
        DB::beginTransaction();
        $user_id = 'USER TEST'; // Sesuaikan dengan ID pengguna yang sebenarnya
        $data = $this->validate($req, [
            'tahun' => 'required',
            'bulan' => 'required',
            'distCode' => 'nullable',
            'chnlCode' => 'nullable',
            'brchName' => 'required',
            'itemCode' => 'required',
            'netSalesUnit' => 'required',
        ]);

        try {
            $data['created_by'] = $user_id;
            stm::create($data);

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

    public function destroy(Request $req, int $id): JsonResponse
    {
        DB::beginTransaction();
        $user_id = 'USER TEST'; // Sesuaikan dengan ID pengguna yang sebenarnya

        try {
            $item = stm::findOrFail($id);

            stm::where('id', $id)->update(['deleted_by' => $user_id]);
            $item->delete(); // Soft delete

            DB::commit();
            return response()->json([
                'code' => 201,
                'status' => true,
                'message' => 'deleted successfully',
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'failed to delete',
            ], 409);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $item = stm::findOrFail($id);
            return response()->json([
                'code' => 200,
                'status' => true,
                'data' => $item
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'data is not found',
                'error' => $e
            ], 404);
        }
    }

    public function update(Request $req, int $id): JsonResponse
    {
        DB::beginTransaction();
        $user_id = 'USER TEST'; // Sesuaikan dengan ID pengguna yang sebenarnya
        $data = $this->validate($req, [
            'tahun' => 'required',
            'bulan' => 'required',
            'distCode' => 'nullable',
            'chnlCode' => 'nullable',
            'brchName' => 'required',
            'itemCode' => 'required',
            'netSalesUnit' => 'required',

        ]);

        try {
            $item = stm::findOrFail($id);
            $item->fill($data)->save();

            stm::where('id', $id)->update([
                'updated_by' => $user_id,
            ]);

            DB::commit();
            return response()->json([
                'code' => 201,
                'status' => true,
                'message' => 'updated successfully',
                'data' => $item
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'code' => 409,
                'status' => false,
                'message' => 'failed to update data',
            ], 409);
        }
    }
}
