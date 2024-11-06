<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\JsonResponse;
use App\Models\basoba;
use App\Models\PublicModel;

use Maatwebsite\Excel\Facades\Excel;
use App\Imports\YourDataImport;

class BasobaController extends Controller
{
    protected $judul_halaman_notif;

    public function __construct()
    {
        $this->judul_halaman_notif = 'BASOBA';
    }



    public function paging(Request $request): JsonResponse
    {
        $URL = URL::current();
        if (!isset($request->search)) {
            $count = (new basoba())->count();
            $arr_pagination = (new PublicModel())->pagination_without_search(
                $URL,
                $request->limit,
                $request->offset
            );
            $todos = (new basoba())->get_data_($request->search, $arr_pagination);
        } else {
            $arr_pagination = (new PublicModel())->pagination_without_search(
                $URL,
                $request->limit,
                $request->offset,
                $request->search
            );
            $todos = (new basoba())->get_data_($request->search, $arr_pagination);
            $count = $todos->count();
        }
        if ($request->distributor) {
            $todos = $todos->where('distributor', '=', $request->distributor);
        }
        if ($request->region) {
            $todos = $todos->where('region', '=', $request->region);
        }
        if ($request->branch) {
            $todos = $todos->where('cabang', '=', $request->branch);
        }
        if ($request->channel) {
            $todos = $todos->where('channel', '=', $request->channel);
        }
        if ($request->brand) {
            $todos = $todos->where('brand', '=', $request->brand);
        }
        if ($request->status_product) {
            $todos = $todos->where('status_product', '=', $request->status_product);
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
                $data['distributor'] = $value['distributor'];
                $data['channel'] = $value['channel'];
                $data['region'] = $value['region'];
                $data['area'] = $value['area'];
                $data['cabang'] = $value['cabang'];
                $data['parent_code'] = $value['parent_code'];
                $data['sku'] = $value['sku'];
                $data['brand'] = $value['brand'];
                $data['kategori'] = $value['kategori'];
                $data['status_product'] = $value['status_product'];
                $data['created_by'] = $req->userid;
                $data['updated_by'] = $req->userid;
                $todos = basoba::create($data);
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


    // public function downloadCSV()
    // {
    //     try {
    //         // Ambil data dari database
    //         $data = DB::table('basobas')->get(); // sesuaikan dengan tabel Anda

    //         // Buat temporary file
    //         $handle = fopen('php://temp', 'w+');

    //         // Tulis BOM untuk UTF-8
    //         fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

    //         // Tulis headers
    //         fputcsv($handle, [
    //             'id',
    //             'created_at',
    //             'updated_at',
    //             'distributor',
    //             'channel',
    //             'region',
    //             'area',
    //             'cabang',
    //             'parent_code',
    //             'sku',
    //             'brand',
    //             'kategori',
    //             'status_product',
    //             'created_deleted_at'
    //         ]);

    //         // Tulis data
    //         foreach ($data as $row) {
    //             fputcsv($handle, [
    //                 $row->id,
    //                 $row->created_at,
    //                 $row->updated_at,
    //                 $row->distributor,
    //                 $row->channel,
    //                 $row->region,
    //                 $row->area,
    //                 $row->cabang,
    //                 $row->parent_code,
    //                 $row->brand,
    //                 $row->kategori,
    //                 $row->status_product,
    //                 $row->created_deleted_at
    //             ]);
    //         }

    //         // Reset pointer dan ambil konten
    //         rewind($handle);
    //         $content = stream_get_contents($handle);
    //         fclose($handle);

    //         // Set headers untuk download
    //         $headers = [
    //             'Content-Type' => 'text/csv; charset=UTF-8',
    //             'Content-Disposition' => 'attachment; filename="export_data.csv"',
    //         ];

    //         // Return response
    //         return response($content, 200, $headers);
    //     } catch (\Exception $e) {
    //         // Log error
    //         basoba::error('CSV Download Error: ' . $e->getMessage());

    //         // Return error response
    //         return response()->json(['error' => 'Failed to generate CSV'], 500);
    //     }
    // }


    public function pagingBulky(Request $request)
    {
        $URL = URL::current();
        $limit = $request->limit ?? 10;  // Default limit jika tidak disediakan
        $offset = $request->offset ?? 0; // Default offset jika tidak disediakan

        // Cek apakah request adalah untuk ekspor CSV
        if ($request->has('export_csv') && $request->export_csv == true) {
            // Ambil semua data sesuai dengan pencarian jika ada
            $data = basoba::when($request->search, function ($query) use ($request) {
                $search = $request->search;
                return $query->where('distributor', 'like', "%$search%")
                    ->orWhere('channel', 'like', "%$search%")
                    ->orWhere('region', 'like', "%$search%")
                    ->orWhere('area', 'like', "%$search%")
                    ->orWhere('cabang', 'like', "%$search%")
                    ->orWhere('parent_code', 'like', "%$search%")
                    ->orWhere('sku', 'like', "%$search%")
                    ->orWhere('brand', 'like', "%$search%")
                    ->orWhere('kategori', 'like', "%$search%")
                    ->orWhere('status_product', 'like', "%$search%");
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
                    'Distributor',
                    'Channel',
                    'Region',
                    'Area',
                    'Cabang',
                    'Parent Code',
                    'SKU',
                    'Brand',
                    'Kategori',
                    'Status Product',
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
                        trim(str_replace(array("\r", "\n"), ' ', $row->distributor)),
                        trim(str_replace(array("\r", "\n"), ' ', $row->channel)),
                        trim(str_replace(array("\r", "\n"), ' ', $row->region)),
                        trim(str_replace(array("\r", "\n"), ' ', $row->area)),
                        trim(str_replace(array("\r", "\n"), ' ', $row->cabang)),
                        trim(str_replace(array("\r", "\n"), ' ', $row->parent_code)),
                        trim(str_replace(array("\r", "\n"), ' ', $row->sku)),
                        trim(str_replace(array("\r", "\n"), ' ', $row->brand)),
                        trim(str_replace(array("\r", "\n"), ' ', $row->kategori)),
                        trim(str_replace(array("\r", "\n"), ' ', $row->status_product)),
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
            $count = basoba::count();
            // Lakukan pagination tanpa pencarian
            $arr_pagination = (new PublicModel())->pagination_without_search($URL, $limit, $offset);
            $todos = basoba::skip($offset)->take($limit)->get();
        } else {
            // Lakukan pagination dengan pencarian
            $search = $request->search;
            $todos = basoba::where('distributor', 'like', "%$search%")
                ->orWhere('channel', 'like', "%$search%")
                ->orWhere('region', 'like', "%$search%")
                ->orWhere('area', 'like', "%$search%")
                ->orWhere('cabang', 'like', "%$search%")
                ->orWhere('parent_code', 'like', "%$search%")
                ->orWhere('sku', 'like', "%$search%")
                ->orWhere('brand', 'like', "%$search%")
                ->orWhere('kategori', 'like', "%$search%")
                ->orWhere('status_product', 'like', "%$search%")
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
            $todo = basoba::get();
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
            'distributor' => 'required',
            'channel' => 'required',
            'region' => 'nullable',
            'area' => 'nullable',
            'cabang' => 'required',
            'parent_code' => 'required',
            'sku' => 'required',
            'brand' => 'required',
            'kategori' => 'required',
            'status_product' => 'required',
        ]);

        try {
            $data['created_by'] = $user_id;
            basoba::create($data);

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
            ], 403);
        }
    }

    public function destroy(Request $req, int $id): JsonResponse
    {
        DB::beginTransaction();
        $user_id = 'USER TEST'; // Sesuaikan dengan ID pengguna yang sebenarnya

        try {
            $item = basoba::findOrFail($id);

            basoba::where('id', $id)->update(['deleted_by' => $user_id]);
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
            $item = basoba::findOrFail($id);
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
            'distributor' => 'required',
            'channel' => 'required',
            'region' => 'nullable',
            'area' => 'nullable',
            'cabang' => 'required',
            'parent_code' => 'required',
            'sku' => 'required',
            'brand' => 'required',
            'kategori' => 'required',
            'status_product' => 'required',

        ]);

        try {
            $item = basoba::findOrFail($id);
            $item->fill($data)->save();

            basoba::where('id', $id)->update([
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
