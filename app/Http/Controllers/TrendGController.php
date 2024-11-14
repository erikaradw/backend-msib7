<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\JsonResponse;
use App\Models\trend_g;
use App\Models\Sales_Unit;
use App\Models\PublicModel;
use Illuminate\Support\Facades\Log;

use Maatwebsite\Excel\Facades\Excel;
use App\Imports\YourDataImport;

class TrendGController extends Controller
{
    protected $judul_halaman_notif;

    public function __construct()
    {
        $this->judul_halaman_notif = 'Trend';
    }



    public function paging(Request $request): JsonResponse
    {
        $URL = URL::current();
        if (!isset($request->search)) {
            $count = (new trend_g())->count_data_($request->search);
            $arr_pagination = (new PublicModel())->pagination_without_search(
                $URL,
                $request->limit,
                $request->offset
            );
            $todos = (new trend_g())->get_data_($request->search, $arr_pagination);
        } else {
            $arr_pagination = (new PublicModel())->pagination_without_search(
                $URL,
                $request->limit,
                $request->offset,
                $request->search
            );
            $todos = (new trend_g())->get_data_($request->search, $arr_pagination);
            $count = $todos->count();
        }
        if ($request->dist_code) {
            $todos = $todos->where('dist_code', '=', $request->dist_code);
        }
        if ($request->region_name) {
            $todos = $todos->where('region_name', '=', $request->region_name);
        }
        if ($request->nama_cabang) {
            $todos = $todos->where('nama_cabang', '=', $request->nama_cabang);
        }
        if ($request->chnl_code) {
            $todos = $todos->where('chnl_code', '=', $request->chnl_code);
        }
        if ($request->brand_name) {
            $todos = $todos->where('brand_name', '=', $request->brand_name);
        }
        if ($request->status_product) {
            $todos = $todos->where('status_product', '=', $request->status_product);
        }
        if ($request->tahun) {
            $todos = $todos->where('tahun', '=', $request->tahun);
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
                $data['dist_code'] = $value['dist_code'];
                $data['chnl_code'] = $value['chnl_code'];
                $data['region_name'] = $value['region_name'];
                $data['area_name'] = $value['area_name'];
                $data['kode_cabang'] = $value['kode_cabang'];
                $data['branch_code'] = $value['branch_code'];
                $data['nama_cabang'] = $value['nama_cabang'];
                $data['item_code'] = $value['item_code'];
                $data['item_name'] = $value['item_name'];
                $data['brand_name'] = $value['brand_name'];
                $data['kategori'] = $value['kategori'];
                $data['status_product'] = $value['status_product'];
                $data['tahun'] = $value['tahun'];
                $data['januari'] = $value['januari'];
                $data['februari'] = $value['februari'];
                $data['maret'] = $value['maret'];
                $data['april'] = $value['april'];
                $data['mei'] = $value['mei'];
                $data['juni'] = $value['juni'];
                $data['juli'] = $value['juli'];
                $data['agustus'] = $value['agustus'];
                $data['september'] = $value['september'];
                $data['oktober'] = $value['oktober'];
                $data['november'] = $value['november'];
                $data['desember'] = $value['desember'];
               
                $data['beli_januari'] = $value['beli_januari'];
                $data['januari1'] = $value['januari1'];
                $data['beli_februari'] = $value['beli_februari'];
                $data['februari1'] = $value['februari1'];
                $data['beli_maret'] = $value['beli_maret'];
                $data['maret1'] = $value['maret1'];
                $data['beli_april'] = $value['beli_april'];
                $data['april1'] = $value['april1'];
                $data['beli_mei'] = $value['beli_mei'];
                $data['mei1'] = $value['mei1'];
                $data['beli_juni'] = $value['beli_juni'];
                $data['juni1'] = $value['juni1'];
                $data['beli_juli'] = $value['beli_juli'];
                $data['juli1'] = $value['juli1'];
                $data['beli_agustus'] = $value['beli_agustus'];
                $data['agustus1'] = $value['agustus1'];
                $data['beli_september'] = $value['beli_september'];
                $data['september1'] = $value['september1'];
                $data['beli_oktober'] = $value['beli_oktober'];
                $data['oktober1'] = $value['oktober1'];
                $data['beli_november'] = $value['beli_november'];
                $data['november1'] = $value['november1'];
                $data['beli_desember'] = $value['beli_desember'];
                $data['desember1'] = $value['desember1'];
                
                $data['created_by'] = $req->userid;
                $data['updated_by'] = $req->userid;
                $todos = trend_g::create($data);
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
            $data = trend_g::when($request->search, function ($query) use ($request) {
                $search = $request->search;
                return $query->where('dist_code', 'like', "%$search%")
                    ->orWhere('chnl_code', 'like', "%$search%")
                    ->orWhere('region_name', 'like', "%$search%")
                    ->orWhere('area_name', 'like', "%$search%")
                    ->orWhere('kode_cabang', 'like', "%$search%")
                    ->orWhere('branch_code', 'like', "%$search%")
                    ->orWhere('nama_cabang', 'like', "%$search%")
                    ->orWhere('item_code', 'like', "%$search%")
                    ->orWhere('item_name', 'like', "%$search%")
                    ->orWhere('brand_name', 'like', "%$search%")
                    ->orWhere('kategori', 'like', "%$search%")
                    ->orWhere('status_product', 'like', "%$search%")
                    ->orWhere('tahun', 'like', "%$search%")
                    ->orWhere('januari', 'like', "%$search%")
                    ->orWhere('februari', 'like', "%$search%")
                    ->orWhere('maret', 'like', "%$search%")
                    ->orWhere('april', 'like', "%$search%")
                    ->orWhere('mei', 'like', "%$search%")
                    ->orWhere('juni', 'like', "%$search%")
                    ->orWhere('juli', 'like', "%$search%")
                    ->orWhere('agustus', 'like', "%$search%")
                    ->orWhere('september', 'like', "%$search%")
                    ->orWhere('oktober', 'like', "%$search%")
                    ->orWhere('november', 'like', "%$search%")
                    ->orWhere('desember', 'like', "%$search%")
                    ->orWhere('unit12', 'like', "%$search%")
                    ->orWhere('value12', 'like', "%$search%")
                    ->orWhere('unit9', 'like', "%$search%")
                    ->orWhere('value9', 'like', "%$search%")
                    ->orWhere('unit6', 'like', "%$search%")
                    ->orWhere('value6', 'like', "%$search%")
                    ->orWhere('unit3', 'like', "%$search%")
                    ->orWhere('value3', 'like', "%$search%")
                    ->orWhere('beli_januari', 'like', "%$search%")
                    ->orWhere('januari1', 'like', "%$search%")
                    ->orWhere('beli_februari', 'like', "%$search%")
                    ->orWhere('februari1', 'like', "%$search%")
                    ->orWhere('beli_maret', 'like', "%$search%")
                    ->orWhere('maret1', 'like', "%$search%")
                    ->orWhere('beli_april', 'like', "%$search%")
                    ->orWhere('april1', 'like', "%$search%")
                    ->orWhere('beli_mei', 'like', "%$search%")
                    ->orWhere('mei1', 'like', "%$search%")
                    ->orWhere('beli_juni', 'like', "%$search%")
                    ->orWhere('juni1', 'like', "%$search%")
                    ->orWhere('beli_juli', 'like', "%$search%")
                    ->orWhere('juli1', 'like', "%$search%")
                    ->orWhere('beli_agustus', 'like', "%$search%")
                    ->orWhere('agustus1', 'like', "%$search%")
                    ->orWhere('beli_september', 'like', "%$search%")
                    ->orWhere('september1', 'like', "%$search%")
                    ->orWhere('beli_oktober', 'like', "%$search%")
                    ->orWhere('oktober1', 'like', "%$search%")
                    ->orWhere('beli_november', 'like', "%$search%")
                    ->orWhere('november1', 'like', "%$search%")
                    ->orWhere('beli_desember', 'like', "%$search%")
                    ->orWhere('desember1', 'like', "%$search%");
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
                    'Kode Cabang',
                    'Branch Code',
                    'Cabang',
                    'Parent Code',
                    'SKU',
                    'Brand',
                    'Kategori',
                    'Status Product',
                    'YOP',
                    'Januari',
                    'Februari',
                    'Maret',
                    'April',
                    'Mei',
                    'Juni',
                    'Juli',
                    'Agustus',
                    'September',
                    'Oktober',
                    'November',
                    'Desember',
                    
                    'Beli Januari',
                    'Januari 1',
                    'Beli Februari',
                    'Februari 1',
                    'Beli Maret',
                    'Maret 1',
                    'Beli April',
                    'April 1',
                    'Beli Mei',
                    'Mei 1',
                    'Beli Juni',
                    'Juni 1',
                    'Beli Juli',
                    'Juli 1',
                    'Beli Agustus',
                    'Agustus 1',
                    'Beli September',
                    'September 1',
                    'Beli Oktober',
                    'Oktober 1',
                    'Beli November',
                    'November 1',
                    'Beli Desember',
                    'Desember 1',
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
                        trim(str_replace(array("\r", "\n"), ' ', $row->dist_code)),
                        trim(str_replace(array("\r", "\n"), ' ', $row->item_name)),
                        trim(str_replace(array("\r", "\n"), ' ', $row->region)),
                        trim(str_replace(array("\r", "\n"), ' ', $row->area)),
                        trim(str_replace(array("\r", "\n"), ' ', $row->cabang)),
                        trim(str_replace(array("\r", "\n"), ' ', $row->item_code)),
                        trim(str_replace(array("\r", "\n"), ' ', $row->sku)),
                        trim(str_replace(array("\r", "\n"), ' ', $row->brand_name)),
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
            $count = trend_g::count();
            // Lakukan pagination tanpa pencarian
            $arr_pagination = (new PublicModel())->pagination_without_search($URL, $limit, $offset);
            $todos = trend_g::skip($offset)->take($limit)->get();
        } else {
            // Lakukan pagination dengan pencarian
            $search = $request->search;
            $todos = trend_g::where('dist_code', 'like', "%$search%")
                ->orWhere('chnl_code', 'like', "%$search%")
                ->orWhere('region_name', 'like', "%$search%")
                ->orWhere('area_name', 'like', "%$search%")
                ->orWhere('nama_cabang', 'like', "%$search%")
                ->orWhere('item_code', 'like', "%$search%")
                ->orWhere('tahun', 'like', "%$search%")
                ->orWhere('item_name', 'like', "%$search%")
                ->orWhere('brand_name', 'like', "%$search%")
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

    public function getAllData()
    {
        try {
            $todo = Sales_Unit::select('tahun')
                ->orderBy('tahun', 'asc')
                ->groupBy('tahun')
                ->get();
            return response()->json([
                'code' => 200,
                'status' => true,
                'data' => $todo
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 409,
                'status' => false,
                'message' => $e->getMessage()
            ], 409);
        }
    }

    public function store(Request $req): JsonResponse
    {
        DB::beginTransaction();
        $user_id = 'USER TEST'; // Sesuaikan dengan ID pengguna yang sebenarnya
        $data = $this->validate($req, [
            'dist_code' => 'required',
            'chnl_code' => 'required',
            'region_name' => 'required',
            'area_name' => 'required',
            'nama_cabang' => 'required',
            'item_code' => 'required',
            'item_name' => 'required',
            'brand_name' => 'required',
            'kategori' => 'required',
            'status_product' => 'required',
            'tahun' => 'required',
            'januari' => 'required',
            'februari' => 'required',
            'maret' => 'required',
            'april' => 'required',
            'mei' => 'required',
            'juni' => 'required',
            'juli' => 'required',
            'agustus' => 'required',
            'september' => 'required',
            'oktober' => 'required',
            'november' => 'required',
            'desember' => 'required',
            
            'beli_januari' => 'required',
            'januari1' => 'required',
            'beli_februari' => 'required',
            'februari1' => 'required',
            'beli_maret' => 'required',
            'maret1' => 'required',
            'beli_april' => 'required',
            'april1' => 'required',
            'beli_mei' => 'required',
            'mei1' => 'required',
            'beli_juni' => 'required',
            'juni1' => 'required',
            'beli_juli' => 'required',
            'juli1' => 'required',
            'beli_agustus' => 'required',
            'agustus1' => 'required',
            'beli_september' => 'required',
            'september1' => 'required',
            'beli_oktober' => 'required',
            'oktober1' => 'required',
            'beli_november' => 'required',
            'november1' => 'required',
            'beli_desember' => 'required',
            'desember1' => 'required',
            
        ]);

        try {
            $data['created_by'] = $user_id;
            trend_g::create($data);

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
            $item = trend_g::findOrFail($id);

            trend_g::where('id', $id)->update(['deleted_by' => $user_id]);
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
            $item = trend_g::findOrFail($id);
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

    public function getTrendData(Request $request)
    {
        try {
            // Query untuk mengambil data trend_gs, m__regions, m__areas, m__cabangs, dan m__customers
            $data = DB::table('trend_gs')
                ->join('m__regions', 'trends.region_name', '=', 'm__regions.region_name') // Join berdasarkan region_name
                ->join('m__areas', 'm__regions.region_name', '=', 'm__areas.region_name') // Join berdasarkan region_name
                ->join('m__cabangs', 'm__areas.area_code', '=', 'm__cabangs.area_code') // Join berdasarkan area_code
                ->join('m__customers', function ($join) {
                    $join->on('trends.dist_code', '=', 'm__customers.dist_code')
                        ->on('trends.chnl_code', '=', 'm__customers.chnl_code'); // Join berdasarkan dist_code dan chnl_code
                })
                ->join('m__products', function ($join) {
                    $join->on('trends.item_code', '=', 'm__products.item_code')
                        ->on('trends.item_name', '=', 'm__products.item_name')
                        ->on('trends.brand_name', '=', 'm__products.brand_name') // Join berdasarkan dist_code dan chnl_code
                        ->on('trends.status_product', '=', 'm__products.status_product'); // Join berdasarkan dist_code dan chnl_code
                })
                ->join('m__kategoris', 'trends.kategori', '=', 'm__kategoris.kategori') // Join berdasarkan area_code
                ->select(
                    'trends.id',  // Ambil id dari tabel trends
                    'm__regions.region_name',  // Ambil region_name dari tabel m__regions
                    'm__areas.area_name',      // Ambil area_name dari tabel m__areas
                    'm__cabangs.nama_cabang',  // Ambil nama_cabang dari tabel m__cabangs
                    'm__customers.dist_code',  // Ambil dist_code dari tabel m__customers
                    'm__customers.chnl_code',   // Ambil chnl_code dari tabel m__customers
                    'm__products.item_code',   // Ambil chnl_code dari tabel m__customers
                    'm__products.item_name',   // Ambil chnl_code dari tabel m__customers
                    'm__products.brand_name',  // Ambil chnl_code dari tabel m__customers
                    'm__products.status_product',
                    'm__kategoris.kategori'
                )
                ->paginate($request->input('limit', 10)); // Batas data per halaman (pagination)

            return response()->json([
                'results' => $data->items(),
                'count' => $data->total(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to get trend data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function deleteAll()
    {
        try {
            $rowCount = DB::table('trend_gs')->count();
            DB::table('trend_gs')->truncate();

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

    public function update(Request $req, int $id): JsonResponse
    {
        DB::beginTransaction();
        $user_id = 'USER TEST'; // Sesuaikan dengan ID pengguna yang sebenarnya
        $data = $this->validate($req, [
            'dist_code' => 'required',
            'item_name' => 'required',
            'region_name' => 'required',
            'area_name' => 'required',
            'nama_cabang' => 'required',
            'item_code' => 'required',
            'brand_name' => 'required',
            'kategori' => 'required',
            'status_product' => 'required',
            'tahun' => 'required',
            'januari' => 'required',
            'februari' => 'required',
            'maret' => 'required',
            'april' => 'required',
            'mei' => 'required',
            'juni' => 'required',
            'juli' => 'required',
            'agustus' => 'required',
            'september' => 'required',
            'oktober' => 'required',
            'november' => 'required',
            'desember' => 'required',
            'beli_januari' => 'required',
            'januari1' => 'required',
            'beli_februari' => 'required',
            'februari1' => 'required',
            'beli_maret' => 'required',
            'maret1' => 'required',
            'beli_april' => 'required',
            'april1' => 'required',
            'beli_mei' => 'required',
            'mei1' => 'required',
            'beli_juni' => 'required',
            'juni1' => 'required',
            'beli_juli' => 'required',
            'juli1' => 'required',
            'beli_agustus' => 'required',
            'agustus1' => 'required',
            'beli_september' => 'required',
            'september1' => 'required',
            'beli_oktober' => 'required',
            'oktober1' => 'required',
            'beli_november' => 'required',
            'november1' => 'required',
            'beli_desember' => 'required',
            
        ]);

        try {
            $item = trend_g::findOrFail($id);
            $item->fill($data)->save();

            trend_g::where('id', $id)->update([
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
    public function getMonthlySalesTrendG(Request $request)
    {
        $URL = URL::current();
        if (!isset($request->search)) {
            $count = count((new trend_g())->countMonthlySalesTrendG($request->search, $request->tahun));
            $arr_pagination = (new PublicModel())->pagination_without_search(
                $URL,
                $request->limit,
                $request->offset
            );
            $todos = (new trend_g())->getMonthlySalesTrendG($request->search, $arr_pagination, $request->tahun);
        } else {
            $arr_pagination = (new PublicModel())->pagination_without_search(
                $URL,
                $request->limit,
                $request->offset,
                $request->search
            );
            $todos = (new trend_g())->getMonthlySalesTrendG($request->search, $arr_pagination, $request->tahun);
            $count = count((new trend_g())->countMonthlySalesTrendG($request->search, $request->tahun));
        }
        return response()->json(
            (new PublicModel())->array_respon_200_table($todos, $count, $arr_pagination),
            200
        );
    }

    public function insertTrendsg()
    {
        // Simpan hasil query ke dalam variabel $data
        $data = DB::select("
           WITH aggregated_sales AS (
    SELECT 
        tahun,
        item_code,
        dist_code,
        kode_cabang,
        chnl_code,
        SUM(CASE WHEN bulan = '1' THEN REPLACE(net_sales_unit, ',', '')::NUMERIC ELSE 0::NUMERIC END) AS januari,
        SUM(CASE WHEN bulan = '2' THEN REPLACE(net_sales_unit, ',', '')::NUMERIC ELSE 0::NUMERIC END) AS februari,
        SUM(CASE WHEN bulan = '3' THEN REPLACE(net_sales_unit, ',', '')::NUMERIC ELSE 0::NUMERIC END) AS maret,
        SUM(CASE WHEN bulan = '4' THEN REPLACE(net_sales_unit, ',', '')::NUMERIC ELSE 0::NUMERIC END) AS april,
        SUM(CASE WHEN bulan = '5' THEN REPLACE(net_sales_unit, ',', '')::NUMERIC ELSE 0::NUMERIC END) AS mei,
        SUM(CASE WHEN bulan = '6' THEN REPLACE(net_sales_unit, ',', '')::NUMERIC ELSE 0::NUMERIC END) AS juni,
        SUM(CASE WHEN bulan = '7' THEN REPLACE(net_sales_unit, ',', '')::NUMERIC ELSE 0::NUMERIC END) AS juli,
        SUM(CASE WHEN bulan = '8' THEN REPLACE(net_sales_unit, ',', '')::NUMERIC ELSE 0::NUMERIC END) AS agustus,
        SUM(CASE WHEN bulan = '9' THEN REPLACE(net_sales_unit, ',', '')::NUMERIC ELSE 0::NUMERIC END) AS september,
        SUM(CASE WHEN bulan = '10' THEN REPLACE(net_sales_unit, ',', '')::NUMERIC ELSE 0::NUMERIC END) AS oktober,
        SUM(CASE WHEN bulan = '11' THEN REPLACE(net_sales_unit, ',', '')::NUMERIC ELSE 0::NUMERIC END) AS november,
        SUM(CASE WHEN bulan = '12' THEN REPLACE(net_sales_unit, ',', '')::NUMERIC ELSE 0::NUMERIC END) AS desember
    FROM sales__units
    GROUP BY tahun, item_code, dist_code, kode_cabang, chnl_code
),
aggregated_stock AS (
    SELECT 
        tahun,
        item_code,
        dist_code,
        kode_cabang,
        SUM(CASE WHEN bulan = '1' THEN REPLACE(on_hand_unit, ',', '')::NUMERIC ELSE 0::NUMERIC END) AS januari1,
        SUM(CASE WHEN bulan = '2' THEN REPLACE(on_hand_unit, ',', '')::NUMERIC ELSE 0::NUMERIC END) AS februari1,
        SUM(CASE WHEN bulan = '3' THEN REPLACE(on_hand_unit, ',', '')::NUMERIC ELSE 0::NUMERIC END) AS maret1,
        SUM(CASE WHEN bulan = '4' THEN REPLACE(on_hand_unit, ',', '')::NUMERIC ELSE 0::NUMERIC END) AS april1,
        SUM(CASE WHEN bulan = '5' THEN REPLACE(on_hand_unit, ',', '')::NUMERIC ELSE 0::NUMERIC END) AS mei1,
        SUM(CASE WHEN bulan = '6' THEN REPLACE(on_hand_unit, ',', '')::NUMERIC ELSE 0::NUMERIC END) AS juni1,
        SUM(CASE WHEN bulan = '7' THEN REPLACE(on_hand_unit, ',', '')::NUMERIC ELSE 0::NUMERIC END) AS juli1,
        SUM(CASE WHEN bulan = '8' THEN REPLACE(on_hand_unit, ',', '')::NUMERIC ELSE 0::NUMERIC END) AS agustus1,
        SUM(CASE WHEN bulan = '9' THEN REPLACE(on_hand_unit, ',', '')::NUMERIC ELSE 0::NUMERIC END) AS september1,
        SUM(CASE WHEN bulan = '10' THEN REPLACE(on_hand_unit, ',', '')::NUMERIC ELSE 0::NUMERIC END) AS oktober1,
        SUM(CASE WHEN bulan = '11' THEN REPLACE(on_hand_unit, ',', '')::NUMERIC ELSE 0::NUMERIC END) AS november1,
        SUM(CASE WHEN bulan = '12' THEN REPLACE(on_hand_unit, ',', '')::NUMERIC ELSE 0::NUMERIC END) AS desember1
    FROM stock__details
    GROUP BY tahun, item_code, dist_code, kode_cabang
),
aggregated_po AS (
    SELECT 
        dist_code,
        mtg_code AS item_code,  -- Menggunakan mtg_code sebagai item_code agar sesuai dengan sales dan stock
        branch_code,
        EXTRACT(YEAR FROM TO_DATE(tgl_order, 'MM/DD/YYYY'))::TEXT AS po_year,
        SUM(CASE WHEN EXTRACT(MONTH FROM TO_DATE(tgl_order, 'MM/DD/YYYY')) = 1 THEN REPLACE(qty_sc_reg, ',', '')::NUMERIC ELSE 0::NUMERIC END) AS beli_januari,
        SUM(CASE WHEN EXTRACT(MONTH FROM TO_DATE(tgl_order, 'MM/DD/YYYY')) = 2 THEN REPLACE(qty_sc_reg, ',', '')::NUMERIC ELSE 0::NUMERIC END) AS beli_februari,
        SUM(CASE WHEN EXTRACT(MONTH FROM TO_DATE(tgl_order, 'MM/DD/YYYY')) = 3 THEN REPLACE(qty_sc_reg, ',', '')::NUMERIC ELSE 0::NUMERIC END) AS beli_maret,
        SUM(CASE WHEN EXTRACT(MONTH FROM TO_DATE(tgl_order, 'MM/DD/YYYY')) = 4 THEN REPLACE(qty_sc_reg, ',', '')::NUMERIC ELSE 0::NUMERIC END) AS beli_april,
        SUM(CASE WHEN EXTRACT(MONTH FROM TO_DATE(tgl_order, 'MM/DD/YYYY')) = 5 THEN REPLACE(qty_sc_reg, ',', '')::NUMERIC ELSE 0::NUMERIC END) AS beli_mei,
        SUM(CASE WHEN EXTRACT(MONTH FROM TO_DATE(tgl_order, 'MM/DD/YYYY')) = 6 THEN REPLACE(qty_sc_reg, ',', '')::NUMERIC ELSE 0::NUMERIC END) AS beli_juni,
        SUM(CASE WHEN EXTRACT(MONTH FROM TO_DATE(tgl_order, 'MM/DD/YYYY')) = 7 THEN REPLACE(qty_sc_reg, ',', '')::NUMERIC ELSE 0::NUMERIC END) AS beli_juli,
        SUM(CASE WHEN EXTRACT(MONTH FROM TO_DATE(tgl_order, 'MM/DD/YYYY')) = 8 THEN REPLACE(qty_sc_reg, ',', '')::NUMERIC ELSE 0::NUMERIC END) AS beli_agustus,
        SUM(CASE WHEN EXTRACT(MONTH FROM TO_DATE(tgl_order, 'MM/DD/YYYY')) = 9 THEN REPLACE(qty_sc_reg, ',', '')::NUMERIC ELSE 0::NUMERIC END) AS beli_september,
        SUM(CASE WHEN EXTRACT(MONTH FROM TO_DATE(tgl_order, 'MM/DD/YYYY')) = 10 THEN REPLACE(qty_sc_reg, ',', '')::NUMERIC ELSE 0::NUMERIC END) AS beli_oktober,
        SUM(CASE WHEN EXTRACT(MONTH FROM TO_DATE(tgl_order, 'MM/DD/YYYY')) = 11 THEN REPLACE(qty_sc_reg, ',', '')::NUMERIC ELSE 0::NUMERIC END) AS beli_november,
        SUM(CASE WHEN EXTRACT(MONTH FROM TO_DATE(tgl_order, 'MM/DD/YYYY')) = 12 THEN REPLACE(qty_sc_reg, ',', '')::NUMERIC ELSE 0::NUMERIC END) AS beli_desember
    FROM p_o_custs
    GROUP BY dist_code, mtg_code, branch_code, EXTRACT(YEAR FROM TO_DATE(tgl_order, 'MM/DD/YYYY'))
)
SELECT
    s.tahun,
    s.dist_code,
    s.chnl_code,
    s.item_code,
    uc.brand_code,
    uc.brand_name,
    uc.parent_code,
    uc.item_name,
    uc.status_product,
    mk.kategori,
    mcb.region_name,
    mcb.area_code,
    mcb.area_name,
    s.kode_cabang,
    mcb.nama_cabang,
    -- Aggregated Sales Data
    s.januari, s.februari, s.maret, s.april, s.mei, s.juni,
    s.juli, s.agustus, s.september, s.oktober, s.november, s.desember,
    -- Aggregated Stock Data
    COALESCE(st.januari1, 0) AS januari1, COALESCE(st.februari1, 0) AS februari1, COALESCE(st.maret1, 0) AS maret1,
    COALESCE(st.april1, 0) AS april1, COALESCE(st.mei1, 0) AS mei1, COALESCE(st.juni1, 0) AS juni1,
    COALESCE(st.juli1, 0) AS juli1, COALESCE(st.agustus1, 0) AS agustus1, COALESCE(st.september1, 0) AS september1,
    COALESCE(st.oktober1, 0) AS oktober1, COALESCE(st.november1, 0) AS november1, COALESCE(st.desember1, 0) AS desember1,
    -- Aggregated PO Data
    COALESCE(po.beli_januari, 0) AS beli_januari, COALESCE(po.beli_februari, 0) AS beli_februari, COALESCE(po.beli_maret, 0) AS beli_maret,
    COALESCE(po.beli_april, 0) AS beli_april, COALESCE(po.beli_mei, 0) AS beli_mei, COALESCE(po.beli_juni, 0) AS beli_juni,
    COALESCE(po.beli_juli, 0) AS beli_juli, COALESCE(po.beli_agustus, 0) AS beli_agustus, COALESCE(po.beli_september, 0) AS beli_september,
    COALESCE(po.beli_oktober, 0) AS beli_oktober, COALESCE(po.beli_november, 0) AS beli_november, COALESCE(po.beli_desember, 0) AS beli_desember
FROM
    aggregated_sales s
LEFT JOIN aggregated_stock st ON s.item_code = st.item_code 
    AND s.dist_code = st.dist_code 
    AND s.kode_cabang = st.kode_cabang
LEFT JOIN aggregated_po po ON s.dist_code = po.dist_code 
    AND s.kode_cabang = po.branch_code 
    AND s.item_code = po.item_code
    AND s.tahun = po.po_year
JOIN m__products uc ON s.item_code = uc.item_code
LEFT JOIN m__kategoris mk ON uc.parent_code = mk.parent_code
JOIN m__cabangs mcb ON s.kode_cabang = mcb.kode_cabang;
        ");
        
        // Cek apakah $data tidak kosong
        if (empty($data)) {
            return "Tidak ada data yang dihasilkan dari query.";
        }
    
        // Bungkus proses insert dalam transaksi
        DB::beginTransaction();
        try {
            foreach ($data as $row) {
                DB::table('trend_gs')->insert([
                    'dist_code' => $row->dist_code,
                    'chnl_code' => $row->chnl_code,
                    'region_name' => $row->region_name,
                    'area_name' => $row->area_name,
                    'kode_cabang' => $row->kode_cabang,
                    'nama_cabang' => $row->nama_cabang,
                    'parent_code' => $row->parent_code,
                    'item_code' => $row->item_code,
                    'item_name' => $row->item_name,
                    'brand_name' => $row->brand_name,
                    'kategori' => $row->kategori,
                    'status_product' => $row->status_product,
                    'tahun' => $row->tahun,
                    'januari' => $row->januari,
                    'februari' => $row->februari,
                    'maret' => $row->maret,
                    'april' => $row->april,
                    'mei' => $row->mei,
                    'juni' => $row->juni,
                    'juli' => $row->juli,
                    'agustus' => $row->agustus,
                    'september' => $row->september,
                    'oktober' => $row->oktober,
                    'november' => $row->november,
                    'desember' => $row->desember,
                    'beli_januari' => $row->beli_januari,
                    'beli_februari' => $row->beli_februari,
                    'beli_maret' => $row->beli_maret,
                    'beli_april' => $row->beli_april,
                    'beli_mei' => $row->beli_mei,
                    'beli_juni' => $row->beli_juni,
                    'beli_juli' => $row->beli_juli,
                    'beli_agustus' => $row->beli_agustus,
                    'beli_september' => $row->beli_september,
                    'beli_oktober' => $row->beli_oktober,
                    'beli_november' => $row->beli_november,
                    'beli_desember' => $row->beli_desember,
                    'januari1' => $row->januari1,
                    'februari1' => $row->februari1,
                    'maret1' => $row->maret1,
                    'april1' => $row->april1,
                    'mei1' => $row->mei1,
                    'juni1' => $row->juni1,
                    'juli1' => $row->juli1,
                    'agustus1' => $row->agustus1,
                    'september1' => $row->september1,
                    'oktober1' => $row->oktober1,
                    'november1' => $row->november1,
                    'desember1' => $row->desember1,
                ]);
            }
            DB::commit();
            return "Data berhasil dimasukkan ke tabel trend_gs.";
        } catch (\Exception $e) {
            DB::rollBack();
            return "Error saat memasukkan data: " . $e->getMessage();
        }
    }
    
}