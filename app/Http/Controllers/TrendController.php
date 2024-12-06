<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\JsonResponse;
use App\Models\trend;
use App\Models\PublicModel;
use Illuminate\Support\Facades\Log;

use Maatwebsite\Excel\Facades\Excel;
use App\Imports\YourDataImport;
use App\Models\Sales_Unit;
use App\Models\M_Product;

class TrendController extends Controller
{
    protected $judul_halaman_notif;

    public function __construct()
    {
        $this->judul_halaman_notif = 'Trend';
    }

    public function getMonthlySalesDataDownload(Request $request)
    {
        $URL = URL::current();
        $selected_month = $request->input('selected_month', 1);
        $selected_year = $request->input('tahun', date('Y'));
        $search = $request->input('search', '');

        if (empty($search)) {
            $count = count((new trend())->countTrendAnalysis($request, $selected_year, $selected_month, $search));
            $arr_pagination = (new PublicModel())->pagination_without_search(
                $URL,
                $request->limit,
                $request->offset
            );

            $todos = (new trend())->getTrendAnalysisDownload($request, $selected_year, $selected_month, $search, $arr_pagination);
        } else {
            $arr_pagination = (new PublicModel())->pagination_without_search(
                $URL,
                $request->limit,
                $request->offset,
                $search
            );
            $todos = (new trend())->getTrendAnalysisDownload($request, $selected_year, $selected_month, $search, $arr_pagination);
            $count = count((new trend())->countTrendAnalysis($request, $selected_year, $selected_month, $search));
        }

        return response()->json(
            (new PublicModel())->array_respon_200_table($todos, $count, $arr_pagination),
            200
        );
    }
    public function getMonthlySalesData(Request $request)
    {
        $URL = URL::current();
        $selected_month = $request->input('selected_month', 1);
        $selected_year = $request->input('tahun', date('Y'));
        $search = $request->input('search', '');

        if (empty($search)) {
            $count = count((new trend())->countTrendAnalysis($request, $selected_year, $selected_month, $search));
            $arr_pagination = (new PublicModel())->pagination_without_search(
                $URL,
                $request->limit,
                $request->offset
            );
            $todos = (new trend())->getTrendAnalysis($request, $selected_year, $selected_month, $search, $arr_pagination);
        } else {
            $arr_pagination = (new PublicModel())->pagination_without_search(
                $URL,
                $request->limit,
                $request->offset,
                $search
            );
            $todos = (new trend())->getTrendAnalysis($request, $selected_year, $selected_month, $search, $arr_pagination);
            $count = count((new trend())->countTrendAnalysis($request, $selected_year, $selected_month, $search));
        }

        return response()->json(
            (new PublicModel())->array_respon_200_table($todos, $count, $arr_pagination),
            200
        );
    }

    // public function getMonthlySalesData(Request $request)
    // {
    //     $URL = URL::current();

    //     // Ambil selected_month dari request, default ke Januari (1) jika tidak disediakan
    //     $selected_month = $request->input('selected_month', 1);

    //     // Ambil selected_year dari request, default ke tahun saat ini jika tidak disediakan
    //     $selected_year = $request->input('tahun', date('Y'));

    //     if (!isset($request->search)) {
    //         $count = count((new trend())->countTrendAnalysis($request, $selected_year, $selected_month, $request->search));
    //         $arr_pagination = (new PublicModel())->pagination_without_search(
    //             $URL,
    //             $request->limit,
    //             $request->offset
    //         );
    //         $todos = (new trend())->getTrendAnalysis($request, $selected_year, $selected_month, $request->search, $arr_pagination);
    //     } else {
    //         $arr_pagination = (new PublicModel())->pagination_without_search(
    //             $URL,
    //             $request->limit,
    //             $request->offset,
    //             $request->search
    //         );
    //         $todos = (new trend())->getTrendAnalysis($selected_year, $selected_month, $request->search, $arr_pagination);
    //         $count = count((new trend())->countTrendAnalysis($selected_year, $selected_month, $request->search));
    //     }

    //     return response()->json(
    //         (new PublicModel())->array_respon_200_table($todos, $count, $arr_pagination),
    //         200
    //     );
    // }

    public function grafikTrend()
    {
        $data = trend::select(
            'nama_cabang',
            DB::raw('SUM(yearly_average_unit) AS total_yearly_average_unit'),
            DB::raw('SUM(average_9_month_unit) AS total_9_month_average_unit'),
            DB::raw('SUM(average_6_month_unit) AS total_6_month_average_unit'),
            DB::raw('SUM(average_3_month_unit) AS total_3_month_average_unit')
        )

            ->groupBy('nama_cabang')
            ->orderBy('nama_cabang')
            ->get();
        return response()->json([
            'code' => 201,
            'status' => true,
            'data' => $data,
        ], 201);
    }
    
    public function grafikTrendByBrand()
    {
        $data = trend::select(
            'brand_name',
            DB::raw('SUM(yearly_average_unit) AS total_yearly_average_unit'),
            DB::raw('SUM(average_9_month_unit) AS total_9_month_average_unit'),
            DB::raw('SUM(average_6_month_unit) AS total_6_month_average_unit'),
            DB::raw('SUM(average_3_month_unit) AS total_3_month_average_unit')
        )
            ->groupBy('brand_name')
            ->orderBy('brand_name')
            ->get();
    
        return response()->json([
            'code' => 201,
            'status' => true,
            'data' => $data,
        ], 201);
    }

    public function grafikTrendBySKU()
    {
        $data = trend::select(
            'item_name',
            DB::raw('SUM(yearly_average_unit) AS total_yearly_average_unit'),
            DB::raw('SUM(average_9_month_unit) AS total_9_month_average_unit'),
            DB::raw('SUM(average_6_month_unit) AS total_6_month_average_unit'),
            DB::raw('SUM(average_3_month_unit) AS total_3_month_average_unit')
        )
            ->groupBy('item_name')
            ->orderBy('item_name')
            ->get();
    
        return response()->json([
            'code' => 201,
            'status' => true,
            'data' => $data,
        ], 201);
    }
    
    public function paging(Request $request): JsonResponse
    {
        $URL = URL::current();
        if (!isset($request->search)) {
            $count = (new trend())->count_data_($request->search);
            $arr_pagination = (new PublicModel())->pagination_without_search(
                $URL,
                $request->limit,
                $request->offset
            );
            $todos = (new trend())->get_data_($request->search, $arr_pagination);
        } else {
            $arr_pagination = (new PublicModel())->pagination_without_search(
                $URL,
                $request->limit,
                $request->offset,
                $request->search
            );
            $todos = (new trend())->get_data_($request->search, $arr_pagination);
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
                $data['unit12'] = $value['unit12'];
                $data['value12'] = $value['value12'];
                $data['unit9'] = $value['unit9'];
                $data['value9'] = $value['value9'];
                $data['unit6'] = $value['unit6'];
                $data['value6'] = $value['value6'];
                $data['unit3'] = $value['unit3'];
                $data['value3'] = $value['value3'];
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
                $data['doi3bulan'] = $value['doi3bulan']; // Tambahkan kolom doi3bulan
                $data['status_trend'] = $value['status_trend']; // Tambahkan kolom status_trend
                $data['delta'] = $value['delta']; // Tambahkan kolom delta
                $data['pic'] = $value['pic']; // Tambahkan kolom pic
                $data['average_sales'] = $value['average_sales']; // Tambahkan kolom average_sales
                $data['purchase_suggestion'] = $value['purchase_suggestion']; // Ubah kolom PURCHASE_SUGGESTION menjadi purchase_suggestion
                $data['purchase_value'] = $value['purchase_value']; // Ubah kolom PURCHASE_VALUE menjadi purchase_value
                $data['created_by'] = $req->userid;
                $data['updated_by'] = $req->userid;
                $todos = trend::create($data);
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
            $data = trend::when($request->search, function ($query) use ($request) {
                $search = $request->search;
                return $query->where('dist_code', 'like', "%$search%")
                    ->orWhere('chnl_code', 'like', "%$search%")
                    ->orWhere('region_name', 'like', "%$search%")
                    ->orWhere('area_name', 'like', "%$search%")
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
                    'Unit 12',
                    'Value 12',
                    'Unit 9',
                    'Value 9',
                    'Unit 6',
                    'Value 6',
                    'Unit 3',
                    'Value 3',
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
            $count = trend::count();
            // Lakukan pagination tanpa pencarian
            $arr_pagination = (new PublicModel())->pagination_without_search($URL, $limit, $offset);
            $todos = trend::skip($offset)->take($limit)->get();
        } else {
            // Lakukan pagination dengan pencarian
            $search = $request->search;
            $todos = trend::where('dist_code', 'like', "%$search%")
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
            'unit12' => 'required',
            'value12' => 'required',
            'unit9' => 'required',
            'value9' => 'required',
            'unit6' => 'required',
            'value6' => 'required',
            'unit3' => 'required',
            'value3' => 'required',
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
            'doi3bulan' => 'required', // Tambahkan kolom doi3bulan
            'status_trend' => 'required', // Tambahkan kolom status_trend
            'delta' => 'required', // Tambahkan kolom delta
            'pic' => 'required', // Tambahkan kolom pic
            'average_sales' => 'required', // Tambahkan kolom average_sales
            'purchase_suggestion' => 'required', // Tambahkan kolom purchase_suggestion
            'purchase_value' => 'required', // Tambahkan kolom purchase_value
        ]);

        try {
            $data['created_by'] = $user_id;
            trend::create($data);

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
            $item = trend::findOrFail($id);

            trend::where('id', $id)->update(['deleted_by' => $user_id]);
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
            $item = trend::findOrFail($id);
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
    public function getAllData()
    {
        try {
            $todo = Sales_Unit::select('chnl_code', 'tahun')
                ->where('chnl_code', '!=', '')
                ->orderBy('chnl_code', 'asc')
                ->orderBy('tahun', 'asc')
                ->groupBy('chnl_code', 'tahun')
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

    public function getAllDatas()
    {
        try {
            $todo = M_Product::select('brand_name', 'status_product')
                ->where('brand_name', '!=', '')
                ->orderBy('brand_name', 'asc')
                ->orderBy('status_product', 'asc')
                ->groupBy('brand_name', 'status_product')
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

    public function getTrendData(Request $request)
    {
        try {
            // Query untuk mengambil data trends, m__regions, m__areas, m__cabangs, dan m__customers
            $data = DB::table('trends')
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
            $rowCount = DB::table('trends')->count();
            DB::table('trends')->truncate();

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
            'unit12' => 'required',
            'value12' => 'required',
            'unit9' => 'required',
            'value9' => 'required',
            'unit6' => 'required',
            'value6' => 'required',
            'unit3' => 'required',
            'value3' => 'required',
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
            'doi3bulan' => 'required', // Tambahkan kolom doi3bulan
            'status_trend' => 'required', // Tambahkan kolom status_trend
            'delta' => 'required', // Tambahkan kolom delta
            'pic' => 'required', // Tambahkan kolom pic
            'average_sales' => 'required', // Tambahkan kolom average_sales
            'purchase_suggestion' => 'required', // Tambahkan kolom purchase_suggestion
            'purchase_value' => 'required', // Tambahkan kolom purchase_value
        ]);

        try {
            $item = trend::findOrFail($id);
            $item->fill($data)->save();

            trend::where('id', $id)->update([
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