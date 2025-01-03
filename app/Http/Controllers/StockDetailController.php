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
            $count = (new Stock_Detail())->count_data_($request->search, $request);
            $arr_pagination = (new PublicModel())->pagination_without_search(
                $URL,
                $request->limit,
                $request->offset
            );
            $todos = (new Stock_Detail())->get_data_($request->search, $arr_pagination, $request);
        } else {
            $arr_pagination = (new PublicModel())->pagination_without_search(
                $URL,
                $request->limit,
                $request->offset,
                $request->search
            );
            $todos =  (new Stock_Detail())->get_data_($request->search, $arr_pagination, $request);
            $count = (new Stock_Detail())->count_data_($request->search, $request);
        }
        return response()->json(
            (new PublicModel())->array_respon_200_table($todos, $count, $arr_pagination),
            200
        );
    }
    public function fetchFilteredDataStockDetail(Request $request)
    {
        try {
            // Ambil parameter dari request
            $distCode = $request->input('dist_code');
            $tahun = $request->input('tahun');
            $bulan = $request->input('bulan');

            // Query dasar
            $query = DB::table('stock__details');
            if (!empty($distCode)) {
                $query->where('dist_code', '=', $distCode);
            }
            if (!empty($tahun)) {
                $query->where('tahun', '=', $tahun);
            }
            if (!empty($bulan)) {
                $query->where('bulan', '=', $bulan);
            }

            $filteredData = $query->get();

            return response()->json([
                'status' => true,
                'message' => 'Data fetched successfully',
                'data' => $filteredData
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch data: ' . $e->getMessage()
            ], 500);
        }
    }
    public function getAll(Request $request)
    {
        $dist_code = $request->input('dist_code');
        $tahun = $request->input('tahun');
        $bulan = $request->input('bulan');

        $query = DB::table('stock__details');

        if ($dist_code) {
            $query->where('dist_code', $dist_code);
        }
        if ($tahun) {
            $query->where('tahun', $tahun);
        }
        if ($bulan) {
            $query->where('bulan', $bulan);
        }

        $data = $query->paginate(10); // Pagination 10 data per halaman

        return response()->json([
            'status' => true,
            'data' => $data,
        ]);
    }

    public function deletefilterstockdetail(Request $request)
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

            // Hapus data berdasarkan filter
            $deletedRows = DB::table('stock__details')
                ->where('dist_code', $dist_code)
                ->where('tahun', $tahun)
                ->where('bulan', $bulan)
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

    public function hapusBanyakData(Request $request)
    {
        $ids = $request->post();
        try {
            // $target = Stock_Detail::find($id);
            $delete = Stock_Detail::whereIn('id', $ids)->delete();
            if (!$delete) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data not found'
                ], 404);
            }

            // $target->delete();

            return response()->json([
                'status' => true,
                'message' => 'Data deleted successfully.'
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
            $key_x = $req->key_x ?? 'onprocess';

            Log::info("Proses batch dengan key_x: {$key_x}");

            if ($key_x === 'start') {
                DB::statement("TRUNCATE TABLE temp_stock__details RESTART IDENTITY");
                Log::info("Tabel temporary dikosongkan (start).");
            }

            $existingDataCount = Stock_Detail::count();

            $data_csv = json_decode(json_encode($req->data), true);
            $user_id = $req->userid;

            if ($existingDataCount === 0) {
                foreach ($data_csv as $key => $value) {
                    if (
                        empty($value['tahun']) || empty($value['bulan']) || empty($value['dist_code']) ||
                        empty($value['kode_cabang']) || empty($value['item_code'])
                    ) {
                        Log::warning("Skipped row due to missing required fields:", $value);
                        continue;
                    }

                    $data = [
                        'tahun' => $value['tahun'],
                        'bulan' => $value['bulan'],
                        'dist_code' => $value['dist_code'],
                        'kode_cabang' => $value['kode_cabang'],
                        'brch_name' => $value['brch_name'],
                        'item_code' => $value['item_code'],
                        'on_hand_unit' => $value['on_hand_unit'],
                        'data_baru' => true,
                        'created_by' => $user_id,
                        'updated_by' => $user_id,
                    ];

                    Stock_Detail::create($data);
                }
            } else {
                Stock_Detail::where('data_baru', true)
                    ->orWhere('data_baru', null)
                    ->update(['data_baru' => false]);

                foreach ($data_csv as $key => $value) {
                    if (
                        empty($value['tahun']) || empty($value['bulan']) || empty($value['dist_code']) ||
                        empty($value['kode_cabang']) || empty($value['item_code'])
                    ) {
                        Log::warning("Skipped row due to missing required fields:", $value);
                        continue;
                    }

                    $attributes = [
                        'tahun' => $value['tahun'],
                        'bulan' => $value['bulan'],
                        'dist_code' => $value['dist_code'],
                        'kode_cabang' => $value['kode_cabang'],
                        'brch_name' => $value['brch_name'],
                        'item_code' => $value['item_code'],
                        'on_hand_unit' => $value['on_hand_unit']
                    ];

                    $values = [
                        'on_hand_unit' => $value['on_hand_unit'] ?? null,
                        'data_baru' => true,
                        'created_by' => $user_id,
                        'updated_by' => $user_id,
                    ];

                    Log::info('Processing Attributes:', $attributes);
                    Log::info('Processing Values:', $values);

                    Stock_Detail::updateOrCreate($attributes, $values);
                }
            }

            if ($key_x === 'end') {
                DB::statement("
                INSERT INTO stock__details (
                    tahun,
                    bulan,
                    dist_code,
                    kode_cabang,
                    brch_name,
                    item_code,
                    on_hand_unit,
                    data_baru,
                    created_at,
                    updated_at
                )
                SELECT
                    tahun,
                    bulan,
                    dist_code,
                    kode_cabang,
                    brch_name,
                    item_code,
                    on_hand_unit,
                    data_baru,
                    created_at,
                    updated_at
                FROM temp_stock__details
                WHERE data_baru = true
            ");

                Log::info("Data valid dipindahkan ke tabel utama.");

                DB::statement("TRUNCATE TABLE temp_stock__details RESTART IDENTITY");
                Log::info("Tabel temporary telah dibersihkan (end).");
            }

            DB::commit();
            return response()->json([
                'code' => 201,
                'status' => true,
                'message' => "Batch dengan key_x {$key_x} berhasil diproses.",
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in storeBulky:', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => false,
                'message' => 'Failed to create or update data',
                'error' => $e->getMessage(),
            ], 403);
        }
    }

    // public function storeBulky(Request $req): JsonResponse
    // {
    //     DB::beginTransaction(); // Mulai transaksi database
    //     try {
    //         // 1. Cek apakah ada data sebelumnya di database
    //         $existingDataCount = Stock_Detail::count();

    //         // 2. Ambil data dari CSV
    //         $data_csv = json_decode(json_encode($req->csv), true);
    //         $user_id = $req->userid; // ID pengguna dari request

    //         // 3. Jika tidak ada data sebelumnya, langsung insert semua data
    //         if ($existingDataCount === 0) {
    //             foreach ($data_csv as $key => $value) {
    //                 // Validasi data CSV, pastikan semua kolom wajib diisi
    //                 if (
    //                     empty($value['tahun']) || empty($value['bulan']) || empty($value['dist_code']) ||
    //                     empty($value['kode_cabang']) || empty($value['item_code'])
    //                 ) {
    //                     Log::warning("Skipped row due to missing required fields:", $value);
    //                     continue; // Lewati jika ada kolom wajib yang kosong
    //                 }

    //                 // Data baru yang akan diinsert
    //                 $data = [
    //                     'tahun' => $value['tahun'],
    //                     'bulan' => $value['bulan'],
    //                     'dist_code' => $value['dist_code'],
    //                     'kode_cabang' => $value['kode_cabang'],
    //                     'brch_name' => $value['brch_name'],
    //                     'item_code' => $value['item_code'],
    //                     'on_hand_unit' => $value['on_hand_unit'],
    //                     'data_baru' => true, // Tandai sebagai data baru
    //                     'created_by' => $user_id,
    //                     'updated_by' => $user_id,
    //                 ];

    //                 // Insert data baru
    //                 Stock_Detail::create($data);
    //             }
    //         } else {
    //             // 4. Jika ada data sebelumnya, lakukan update or create
    //             Stock_Detail::where('data_baru', true)
    //                 ->orWhere('data_baru', null)
    //                 ->update(['data_baru' => false]);

    //             foreach ($data_csv as $key => $value) {
    //                 // Validasi data CSV, pastikan semua kolom wajib diisi
    //                 if (
    //                     empty($value['tahun']) || empty($value['bulan']) || empty($value['dist_code']) ||
    //                     empty($value['kode_cabang']) || empty($value['item_code'])
    //                 ) {
    //                     Log::warning("Skipped row due to missing required fields:", $value);
    //                     continue; // Lewati jika ada kolom wajib yang kosong
    //                 }

    //                 // Tentukan atribut unik untuk mencocokkan data di database
    //                 $attributes = [
    //                     'tahun' => $value['tahun'],
    //                     'bulan' => $value['bulan'],
    //                     'dist_code' => $value['dist_code'],
    //                     'kode_cabang' => $value['kode_cabang'],
    //                     'brch_name' => $value['brch_name'],
    //                     'item_code' => $value['item_code'],
    //                     'on_hand_unit' => $value['on_hand_unit']
    //                 ];

    //                 // Data yang akan diupdate atau diinsert
    //                 $values = [
    //                     'on_hand_unit' => $value['on_hand_unit'] ?? null,
    //                     'data_baru' => true, // Tandai sebagai data baru
    //                     'created_by' => $user_id,
    //                     'updated_by' => $user_id,
    //                 ];

    //                 Log::info('Processing Attributes:', $attributes);
    //                 Log::info('Processing Values:', $values);

    //                 // Gunakan updateOrCreate untuk insert atau update data
    //                 Stock_Detail::updateOrCreate($attributes, $values);
    //             }
    //         }

    //         DB::commit(); // Commit transaksi jika semua berhasil
    //         return response()->json([
    //             'code' => 201,
    //             'status' => true,
    //             'message' => 'Data created or updated successfully',
    //         ], 201);
    //     } catch (\Exception $e) {
    //         DB::rollBack(); // Rollback transaksi jika ada error
    //         Log::error('Error in storeBulky:', ['error' => $e->getMessage()]);
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Failed to create or update data',
    //             'error' => $e->getMessage(),
    //         ], 403);
    //     }
    // }

    // public function storeBulky(Request $req): JsonResponse
    // {
    //     DB::beginTransaction();
    //     try {
    //         $todo = Stock_Detail::where('data_baru', true)->orWhere('data_baru', null);
    //         $todo->update(['data_baru' => false]);
    //         $user_id = 'USER TEST'; // Sesuaikan dengan ID pengguna yang sebenarnya
    //         $data_csv = json_decode(json_encode($req->csv), true);
    //         foreach ($data_csv as $key => $value) {
    //             $data = array();
    //             $data['tahun'] = $value['tahun'];
    //             $data['bulan'] = $value['bulan'];
    //             $data['dist_code'] = $value['dist_code'];
    //             $data['kode_cabang'] = $value['kode_cabang'];
    //             $data['brch_name'] = $value['brch_name'];
    //             $data['item_code'] = $value['item_code'];
    //             $data['on_hand_unit'] = $value['on_hand_unit'];
    //             $data['data_baru'] = true;

    //             $data['created_by'] = $req->userid;
    //             $data['updated_by'] = $req->userid;
    //             $todos = Stock_Detail::create($data);
    //         }
    //         DB::commit();
    //         return response()->json([
    //             'code' => 201,
    //             'status' => true,
    //             'message' => 'created successfully',
    //         ], 201);
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'failed to create data',
    //             'error' => $e
    //         ], 403);
    //     }
    // }

    public function destroy(Request $request): JsonResponse
    {
        $user_id = 'USER TEST';
        try {
            $id = $request->input('id', null);
            if ($id) {
                $todo = Stock_Detail::where('id', $id);
                $todo->delete();
            } else {
                $todo = Stock_Detail::where('data_baru', true);
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
