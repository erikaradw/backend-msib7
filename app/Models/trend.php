<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\support\Facades\DB;
use Illuminate\support\Facades\Log;
use Illuminate\Database\Eloquent\SoftDeletes;

class trend extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $table = 'trends'; // Nama tabel sesuai dengan migration
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

    public function getTrendAnalysisDownload($request, $selectedYear, $selectedMonth, $search = '', $pagination)
    {
        try {
            if ($pagination['offset'] == 0) {
                DB::beginTransaction();

                // Ambil semua data dari getTrendAnalysis tanpa limit dan offset
                $finalDataResult = $this->getTrendAnalysisDownloadX($request, $selectedYear, $selectedMonth, $search, [
                    'limit' => null,
                    'offset' => null,
                ]);

                $dataCount = $finalDataResult->count();
                Log::info("Jumlah total data dari getTrendAnalysis: {$dataCount}");

                if ($dataCount < 550) {
                    Log::warning("Jumlah data yang ditemukan ({$dataCount}) kurang dari target (550).");
                }

                DB::select("TRUNCATE TABLE trends");

                Log::info('Memulai transaksi untuk truncate dan insert data ke tabel trends.');
                Log::info('Tabel trends berhasil di-TRUNCATE.');

                // Proses memasukkan data ke dalam tabel trends
                foreach ($finalDataResult as $row) {

                    $data = [
                        'dist_code' => $row->dist_code,
                        'chnl_code' => $row->chnl_code,
                        'tahun' => (string) $row->tahun,
                        'item_code' => $row->item_code,
                        'region_name' => $row->region_name ?? '',
                        'area_name' => $row->area_name ?? '',
                        'nama_cabang' => $row->nama_cabang ?? '',
                        'parent_code' => $row->parent_code ?? '',
                        'item_name' => $row->item_name ?? '',
                        'brand_name' => $row->brand_name ?? '',
                        'kategori' => $row->kategori ?? '',
                        'status_product' => $row->status_product ?? '',
                        'month_1' => is_numeric($row->month_1) ? (int) $row->month_1 : 0,
                        'month_2' => is_numeric($row->month_2) ? (int) $row->month_2 : 0,
                        'month_3' => is_numeric($row->month_3) ? (int) $row->month_3 : 0,
                        'month_4' => is_numeric($row->month_4) ? (int) $row->month_4 : 0,
                        'month_5' => is_numeric($row->month_5) ? (int) $row->month_5 : 0,
                        'month_6' => is_numeric($row->month_6) ? (int) $row->month_6 : 0,
                        'month_7' => is_numeric($row->month_7) ? (int) $row->month_7 : 0,
                        'month_8' => is_numeric($row->month_8) ? (int) $row->month_8 : 0,
                        'month_9' => is_numeric($row->month_9) ? (int) $row->month_9 : 0,
                        'month_10' => is_numeric($row->month_10) ? (int) $row->month_10 : 0,
                        'month_11' => is_numeric($row->month_11) ? (int) $row->month_11 : 0,
                        'month_12' => is_numeric($row->month_12) ? (int) $row->month_12 : 0,
                        'yearly_average_unit' => is_numeric($row->yearly_average_unit) ? (int) $row->yearly_average_unit : 0,
                        'yearly_average_value' => (int) str_replace([',', '.'], '', $row->yearly_average_value),
                        'average_9_month_unit' => is_numeric($row->average_9_month_unit) ? (int) $row->average_9_month_unit : 0,
                        'average_9_month_value' => is_numeric($row->average_9_month_value) ? (int) $row->average_9_month_value : 0,
                        'average_6_month_unit' => is_numeric($row->average_6_month_unit) ? (int) $row->average_6_month_unit : 0,
                        'average_6_month_value' => is_numeric($row->average_6_month_value) ? (int) $row->average_6_month_value : 0,
                        'average_3_month_unit' => is_numeric($row->average_3_month_unit) ? (int) $row->average_3_month_unit : 0,
                        'average_3_month_value' => is_numeric($row->average_3_month_value) ? (int) $row->average_3_month_value : 0,
                        'average_sales' => is_numeric($row->average_sales) ? (int) $row->average_sales : 0,
                        'purchase_suggestion' => is_numeric($row->purchase_suggestion) ? (int) $row->purchase_suggestion : 0,
                        'purchase_value' => is_numeric($row->purchase_value) ? (int) ceil($row->purchase_value) : 0,
                        'stock_on_hand_unit' => is_numeric($row->stock_on_hand_unit) ? (int) $row->stock_on_hand_unit : 0,
                        'doi_3_month' => is_numeric($row->doi_3_month) ? (int) $row->doi_3_month : 0,
                        'status_trend' => $row->status_trend ?? '',
                        'delta' => is_string($row->delta) ? (float)str_replace('%', '', $row->delta) : $row->delta,
                        'qty_po' => $row->qty_po ?? 0,
                        'service_level' => is_string($row->service_level) ? (float)str_replace('%', '', $row->service_level) : $row->service_level,
                        'qty_sc_reg' => $row->qty_sc_reg ?? 0,
                        'pic' => $row->pic ?? '',
                    ];

                    // Masukkan data ke tabel trends
                    DB::table('trends')->insert($data);
                }

                Log::info('Data berhasil diinsert ke tabel trends.');

                DB::commit();
            }

            // Ambil data dari tabel trends untuk hasil akhir
            $result = DB::table('trends')
                ->offset($pagination['offset'])->limit($pagination['limit'])->get();
            Log::info('Proses selesai. Data di tabel trends berhasil diambil.');

            return $result;
        } catch (\Exception $e) {
            // Log error untuk debugging
            DB::rollback();
            Log::error('Error in getTrendAnalysisDownload:', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    public function getTrendAnalysisDownloadX($request, $selectedYear, $selectedMonth, $search = '', $pagination = [])
    {
        try {
            $selectedYear = $request['tahun'];
            $selectedMonth = $request['selected_month'];
            $search = $request['search'] ?? '';
            $pagination = [
                'limit' => $request['limit'] ?? 10,
                'offset' => $request['offset'] ?? 0
            ];
            $distCode = $request['dist_code'] ?? '%';
            $branch = $request['branch'] ?? '%';
            $regionName = $request['region_name'] ?? '%';
            $chnlCode = $request['chnl_code'] ?? '%';
            $brandName = $request['brand_name'] ?? '%';
            $statusProduct = $request['status_product'] ?? '%';

            $limit = $pagination['limit'];
            $offset = $pagination['offset'];

            $monthYearMap = [];
            for ($i = 0; $i < 12; $i++) {
                $month = ($selectedMonth - $i) > 0 ? ($selectedMonth - $i) : (12 + ($selectedMonth - $i));
                $year = ($selectedMonth - $i) > 0 ? $selectedYear : ($selectedYear - 1);
                $monthYearMap["month_" . (12 - $i)] = ['month' => $month, 'year' => $year];
            }

            $aggregatedSales = DB::table('vw_sales__units as s')
                ->selectRaw("
                CAST(s.tahun AS INTEGER) AS tahun,
                s.item_code,
                s.dist_code,
                s.kode_cabang,
                s.chnl_code,
                CAST(s.bulan AS INTEGER) AS bulan,
                SUM(COALESCE(NULLIF(REPLACE(s.net_sales_unit, ',', ''), '')::NUMERIC, 0))::INTEGER AS net_sales_unit
            ")
                ->whereRaw("s.bulan IS NOT NULL AND s.bulan <> ''")
                ->groupByRaw("
                CAST(s.tahun AS INTEGER),
                s.item_code,
                s.dist_code,
                s.kode_cabang,
                s.chnl_code,
                CAST(s.bulan AS INTEGER)
            ");

            $aggregatedStock = DB::table('vw_stock__details as st')
                ->selectRaw("
                st.item_code,
                st.dist_code,
                st.kode_cabang,
                SUM(COALESCE(NULLIF(REPLACE(st.on_hand_unit, ',', ''), '')::NUMERIC, 0))::INTEGER AS stock_on_hand_unit
            ")
                ->where('st.tahun', $selectedYear)
                ->where('st.bulan', $selectedMonth)
                ->groupBy('st.item_code', 'st.dist_code', 'st.kode_cabang');

            $poData = DB::table('p_o_custs as po')
                ->selectRaw("
                po.mtg_code AS item_code,
                po.dist_code,
                po.branch_code AS kode_cabang,
                EXTRACT(YEAR FROM CAST(po.tgl_order AS DATE)) AS tahun,
                EXTRACT(MONTH FROM CAST(po.tgl_order AS DATE)) AS bulan,
                SUM(COALESCE(NULLIF(REPLACE(po.qty_po, ',', ''), '')::NUMERIC, 0)) AS qty_po,
                SUM(COALESCE(NULLIF(REPLACE(po.qty_sc_reg, ',', ''), '')::NUMERIC, 0)) AS qty_sc_reg
            ")
                ->whereRaw("
                EXTRACT(YEAR FROM CAST(po.tgl_order AS DATE)) = ? AND EXTRACT(MONTH FROM CAST(po.tgl_order AS DATE)) = ?
            ", [$selectedYear, $selectedMonth])
                ->groupBy('po.mtg_code', 'po.dist_code', 'po.branch_code', 'tahun', 'bulan');

            $salesMonths = DB::table(DB::raw("({$aggregatedSales->toSql()}) as s"))
                ->mergeBindings($aggregatedSales)
                ->selectRaw("
                s.tahun,
                s.item_code,
                s.dist_code,
                s.kode_cabang,
                s.chnl_code,
                s.bulan,
                s.net_sales_unit
            ")
                ->whereRaw("(s.tahun = ? AND s.bulan <= ?) OR (s.tahun = ? - 1 AND s.bulan > ?)", [
                    $selectedYear,
                    $selectedMonth,
                    $selectedYear,
                    $selectedMonth,
                ]);

            $shiftedSales = DB::table(DB::raw("({$salesMonths->toSql()}) as sm"))
                ->mergeBindings($salesMonths)
                ->leftJoinSub($aggregatedStock, 'st', function ($join) {
                    $join->on('sm.item_code', '=', 'st.item_code')
                        ->on('sm.dist_code', '=', 'st.dist_code')
                        ->on('sm.kode_cabang', '=', 'st.kode_cabang');
                })
                ->selectRaw("
                    sm.tahun,
                    sm.item_code,
                    sm.dist_code,
                    sm.kode_cabang,
                    sm.chnl_code,
                    MAX(COALESCE(st.stock_on_hand_unit, 0)) AS stock_on_hand_unit,
                    " . implode(", ", array_map(function ($alias, $data) {
                    return "MAX(CASE WHEN (sm.bulan = {$data['month']} AND sm.tahun = {$data['year']}) THEN sm.net_sales_unit ELSE 0 END) AS {$alias}";
                }, array_keys($monthYearMap), $monthYearMap)) . ",
                    CASE
                    WHEN (
                        COALESCE(SUM(CASE WHEN sm.bulan BETWEEN 1 AND 12 THEN sm.net_sales_unit ELSE 0 END), 0)
                    ) = 0 THEN 0
                    WHEN (
                        COALESCE(SUM(CASE WHEN sm.bulan IN (1, 2, 3) THEN sm.net_sales_unit ELSE 0 END), 0)
                    ) = 0 THEN 0
                    ELSE (
                        ROUND(
                            SUM(CASE WHEN sm.bulan BETWEEN 1 AND 12 THEN sm.net_sales_unit ELSE 0 END) / 
                            NULLIF(
                                -- Jumlah bulan non-nol (1 hingga 12)
                                (SUM(CASE WHEN sm.bulan = 1 AND sm.net_sales_unit > 0 THEN 1 ELSE 0 END) +
                                SUM(CASE WHEN sm.bulan = 2 AND sm.net_sales_unit > 0 THEN 1 ELSE 0 END) +
                                SUM(CASE WHEN sm.bulan = 3 AND sm.net_sales_unit > 0 THEN 1 ELSE 0 END) +
                                SUM(CASE WHEN sm.bulan = 4 AND sm.net_sales_unit > 0 THEN 1 ELSE 0 END) +
                                SUM(CASE WHEN sm.bulan = 5 AND sm.net_sales_unit > 0 THEN 1 ELSE 0 END) +
                                SUM(CASE WHEN sm.bulan = 6 AND sm.net_sales_unit > 0 THEN 1 ELSE 0 END) +
                                SUM(CASE WHEN sm.bulan = 7 AND sm.net_sales_unit > 0 THEN 1 ELSE 0 END) +
                                SUM(CASE WHEN sm.bulan = 8 AND sm.net_sales_unit > 0 THEN 1 ELSE 0 END) +
                                SUM(CASE WHEN sm.bulan = 9 AND sm.net_sales_unit > 0 THEN 1 ELSE 0 END) +
                                SUM(CASE WHEN sm.bulan = 10 AND sm.net_sales_unit > 0 THEN 1 ELSE 0 END) +
                                SUM(CASE WHEN sm.bulan = 11 AND sm.net_sales_unit > 0 THEN 1 ELSE 0 END) +
                                SUM(CASE WHEN sm.bulan = 12 AND sm.net_sales_unit > 0 THEN 1 ELSE 0 END)
                                ), 0
                            )
                        )::INTEGER
                    )
                    END AS yearly_average_unit,

                    CASE
                        WHEN (
                            COALESCE(SUM(CASE WHEN sm.bulan BETWEEN 4 AND 12 THEN sm.net_sales_unit ELSE 0 END), 0)
                        ) = 0 THEN 0
                        ELSE (
                            ROUND(
                                SUM(CASE WHEN sm.bulan BETWEEN 4 AND 12 THEN sm.net_sales_unit ELSE 0 END) / 
                                NULLIF(
                                    -- Jumlah bulan non-nol (April hingga Desember)
                                    (SUM(CASE WHEN sm.bulan = 4 AND sm.net_sales_unit > 0 THEN 1 ELSE 0 END) +
                                    SUM(CASE WHEN sm.bulan = 5 AND sm.net_sales_unit > 0 THEN 1 ELSE 0 END) +
                                    SUM(CASE WHEN sm.bulan = 6 AND sm.net_sales_unit > 0 THEN 1 ELSE 0 END) +
                                    SUM(CASE WHEN sm.bulan = 7 AND sm.net_sales_unit > 0 THEN 1 ELSE 0 END) +
                                    SUM(CASE WHEN sm.bulan = 8 AND sm.net_sales_unit > 0 THEN 1 ELSE 0 END) +
                                    SUM(CASE WHEN sm.bulan = 9 AND sm.net_sales_unit > 0 THEN 1 ELSE 0 END) +
                                    SUM(CASE WHEN sm.bulan = 10 AND sm.net_sales_unit > 0 THEN 1 ELSE 0 END) +
                                    SUM(CASE WHEN sm.bulan = 11 AND sm.net_sales_unit > 0 THEN 1 ELSE 0 END) +
                                    SUM(CASE WHEN sm.bulan = 12 AND sm.net_sales_unit > 0 THEN 1 ELSE 0 END)
                                    ), 0
                                )
                            )::INTEGER
                        )
                    END AS average_9_month_unit,

                    CASE
                        WHEN (
                            COALESCE(SUM(CASE WHEN sm.bulan BETWEEN 7 AND 12 THEN sm.net_sales_unit ELSE 0 END), 0)
                        ) = 0 THEN 0
                        ELSE (
                            ROUND(
                                SUM(CASE WHEN sm.bulan BETWEEN 7 AND 12 THEN sm.net_sales_unit ELSE 0 END) / 
                                NULLIF(
                                    -- Jumlah bulan non-nol (Juli hingga Desember)
                                    (SUM(CASE WHEN sm.bulan = 7 AND sm.net_sales_unit > 0 THEN 1 ELSE 0 END) +
                                    SUM(CASE WHEN sm.bulan = 8 AND sm.net_sales_unit > 0 THEN 1 ELSE 0 END) +
                                    SUM(CASE WHEN sm.bulan = 9 AND sm.net_sales_unit > 0 THEN 1 ELSE 0 END) +
                                    SUM(CASE WHEN sm.bulan = 10 AND sm.net_sales_unit > 0 THEN 1 ELSE 0 END) +
                                    SUM(CASE WHEN sm.bulan = 11 AND sm.net_sales_unit > 0 THEN 1 ELSE 0 END) +
                                    SUM(CASE WHEN sm.bulan = 12 AND sm.net_sales_unit > 0 THEN 1 ELSE 0 END)
                                    ), 0
                                )
                            )::INTEGER
                        )
                    END AS average_6_month_unit,

                    CASE
                        WHEN (
                            COALESCE(SUM(CASE WHEN sm.bulan BETWEEN 10 AND 12 THEN sm.net_sales_unit ELSE 0 END), 0)
                        ) = 0 THEN 0
                        ELSE (
                            ROUND(
                                SUM(CASE WHEN sm.bulan BETWEEN 10 AND 12 THEN sm.net_sales_unit ELSE 0 END) / 
                                NULLIF(
                                    -- Jumlah bulan non-nol (Oktober hingga Desember)
                                    (SUM(CASE WHEN sm.bulan = 10 AND sm.net_sales_unit > 0 THEN 1 ELSE 0 END) +
                                    SUM(CASE WHEN sm.bulan = 11 AND sm.net_sales_unit > 0 THEN 1 ELSE 0 END) +
                                    SUM(CASE WHEN sm.bulan = 12 AND sm.net_sales_unit > 0 THEN 1 ELSE 0 END)
                                    ), 0
                                )
                            )::INTEGER
                        )
                    END AS average_3_month_unit
                ")
                ->groupByRaw("
                    sm.tahun,
                    sm.item_code,
                    sm.dist_code,
                    sm.kode_cabang,
                    sm.chnl_code
                ");

            $finalData = DB::table(DB::raw("({$shiftedSales->toSql()}) as sa"))
                ->mergeBindings($shiftedSales)
                ->leftJoinSub($poData, 'po', function ($join) {
                    $join->on('sa.item_code', '=', 'po.item_code')
                        ->on('sa.dist_code', '=', 'po.dist_code')
                        ->on('sa.kode_cabang', '=', 'po.kode_cabang');
                })
                ->leftJoinSub($aggregatedStock, 'st', function ($join) {
                    $join->on('sa.item_code', '=', 'st.item_code')
                        ->on('sa.dist_code', '=', 'st.dist_code')
                        ->on('sa.kode_cabang', '=', 'st.kode_cabang');
                })
                ->leftJoin('m__products as mp', 'sa.item_code', '=', 'mp.item_code')
                ->leftJoin('m__kategoris as mk', 'mp.parent_code', '=', 'mk.parent_code')
                ->leftJoin('m__cabangs as mc', 'sa.kode_cabang', '=', 'mc.kode_cabang')
                ->selectRaw("
                DISTINCT sa.tahun,
                sa.item_code,
                sa.dist_code,
                sa.kode_cabang,
                sa.chnl_code,
                mp.brand_name,
                mp.status_product,
                mk.kategori,
                mp.parent_code,
                mp.item_name,
                mc.region_name,
                mc.area_name,
                mc.nama_cabang,
                sa.month_1,
                sa.month_2,
                sa.month_3,
                sa.month_4,
                sa.month_5,
                sa.month_6,
                sa.month_7,
                sa.month_8,
                sa.month_9,
                sa.month_10,
                sa.month_11,
                sa.month_12,
                CASE 
                WHEN po.qty_po = 0 OR po.qty_po IS NULL THEN '0%'
                ELSE CONCAT(ROUND((po.qty_sc_reg::NUMERIC / NULLIF(po.qty_po, 0)) * 100), '%')
                END AS service_level,
                CASE
                WHEN (
                    COALESCE(sa.month_1, 0) + COALESCE(sa.month_2, 0) + COALESCE(sa.month_3, 0) + sa.month_4 + sa.month_5 + sa.month_6 +
                    sa.month_7 + sa.month_8 + sa.month_9 + sa.month_10 + sa.month_11 + sa.month_12
                ) = 0 THEN 0
                WHEN (COALESCE(sa.month_1, 0) + COALESCE(sa.month_2, 0) + COALESCE(sa.month_3, 0)) = 0 THEN 0
                ELSE (
                    ROUND((
                        COALESCE(sa.month_1, 0) + COALESCE(sa.month_2, 0) + COALESCE(sa.month_3, 0) + sa.month_4 + sa.month_5 + sa.month_6 +
                        sa.month_7 + sa.month_8 + sa.month_9 + sa.month_10 + sa.month_11 + sa.month_12
                    ) / NULLIF(
                        -- Hitung jumlah bulan yang memiliki nilai > 0
                        (CASE WHEN sa.month_1 > 0 THEN 1 ELSE 0 END +
                        CASE WHEN sa.month_2 > 0 THEN 1 ELSE 0 END +
                        CASE WHEN sa.month_3 > 0 THEN 1 ELSE 0 END +
                        CASE WHEN sa.month_4 > 0 THEN 1 ELSE 0 END +
                        CASE WHEN sa.month_5 > 0 THEN 1 ELSE 0 END +
                        CASE WHEN sa.month_6 > 0 THEN 1 ELSE 0 END +
                        CASE WHEN sa.month_7 > 0 THEN 1 ELSE 0 END +
                        CASE WHEN sa.month_8 > 0 THEN 1 ELSE 0 END +
                        CASE WHEN sa.month_9 > 0 THEN 1 ELSE 0 END +
                        CASE WHEN sa.month_10 > 0 THEN 1 ELSE 0 END +
                        CASE WHEN sa.month_11 > 0 THEN 1 ELSE 0 END +
                        CASE WHEN sa.month_12 > 0 THEN 1 ELSE 0 END
                        ), 0)
                    )::INTEGER
                )
                END AS yearly_average_unit,
                CASE
                WHEN (
                    COALESCE(sa.month_1, 0) + COALESCE(sa.month_2, 0) + COALESCE(sa.month_3, 0) + sa.month_4 + sa.month_5 + sa.month_6 +
                    sa.month_7 + sa.month_8 + sa.month_9 + sa.month_10 + sa.month_11 + sa.month_12
                ) = 0 THEN 0
                WHEN (COALESCE(sa.month_1, 0) + COALESCE(sa.month_2, 0) + COALESCE(sa.month_3, 0)) = 0 THEN 0
                ELSE (
                    ROUND(
                        (
                            COALESCE(sa.month_1, 0) + COALESCE(sa.month_2, 0) + COALESCE(sa.month_3, 0) + sa.month_4 + sa.month_5 + sa.month_6 +
                            sa.month_7 + sa.month_8 + sa.month_9 + sa.month_10 + sa.month_11 + sa.month_12
                        ) / NULLIF(
                            (CASE WHEN sa.month_1 > 0 THEN 1 ELSE 0 END +
                            CASE WHEN sa.month_2 > 0 THEN 1 ELSE 0 END +
                            CASE WHEN sa.month_3 > 0 THEN 1 ELSE 0 END +
                            CASE WHEN sa.month_4 > 0 THEN 1 ELSE 0 END +
                            CASE WHEN sa.month_5 > 0 THEN 1 ELSE 0 END +
                            CASE WHEN sa.month_6 > 0 THEN 1 ELSE 0 END +
                            CASE WHEN sa.month_7 > 0 THEN 1 ELSE 0 END +
                            CASE WHEN sa.month_8 > 0 THEN 1 ELSE 0 END +
                            CASE WHEN sa.month_9 > 0 THEN 1 ELSE 0 END +
                            CASE WHEN sa.month_10 > 0 THEN 1 ELSE 0 END +
                            CASE WHEN sa.month_11 > 0 THEN 1 ELSE 0 END +
                            CASE WHEN sa.month_12 > 0 THEN 1 ELSE 0 END
                            ), 0
                        )
                    ) * 
                    NULLIF(REPLACE(mp.price, ',', ''), '')::NUMERIC
                )
                END AS yearly_average_value,
                    CASE
                    WHEN (
                        COALESCE(sa.month_4, 0) + COALESCE(sa.month_5, 0) + COALESCE(sa.month_6, 0) + sa.month_7 + sa.month_8 + sa.month_9 +
                        sa.month_10 + sa.month_11 + sa.month_12
                    ) = 0 THEN 0
                    WHEN (COALESCE(sa.month_4, 0) + COALESCE(sa.month_5, 0) + COALESCE(sa.month_6, 0)) = 0 THEN 0
                    ELSE (
                        ROUND((
                            COALESCE(sa.month_4, 0) + COALESCE(sa.month_5, 0) + COALESCE(sa.month_6, 0) + sa.month_7 + sa.month_8 + sa.month_9 +
                            sa.month_10 + sa.month_11 + sa.month_12
                        ) / NULLIF(
                            (CASE WHEN sa.month_4 > 0 THEN 1 ELSE 0 END +
                            CASE WHEN sa.month_5 > 0 THEN 1 ELSE 0 END +
                            CASE WHEN sa.month_6 > 0 THEN 1 ELSE 0 END +
                            CASE WHEN sa.month_7 > 0 THEN 1 ELSE 0 END +
                            CASE WHEN sa.month_8 > 0 THEN 1 ELSE 0 END +
                            CASE WHEN sa.month_9 > 0 THEN 1 ELSE 0 END +
                            CASE WHEN sa.month_10 > 0 THEN 1 ELSE 0 END +
                            CASE WHEN sa.month_11 > 0 THEN 1 ELSE 0 END +
                            CASE WHEN sa.month_12 > 0 THEN 1 ELSE 0 END
                            ), 0)
                        )::INTEGER
                    )
                    END AS average_9_month_unit,
                    CASE
                    WHEN (
                        COALESCE(sa.month_4, 0) + COALESCE(sa.month_5, 0) + COALESCE(sa.month_6, 0) + sa.month_7 + sa.month_8 + sa.month_9 +
                        sa.month_10 + sa.month_11 + sa.month_12
                    ) = 0 THEN 0
                    WHEN (COALESCE(sa.month_4, 0) + COALESCE(sa.month_5, 0) + COALESCE(sa.month_6, 0)) = 0 THEN 0
                    ELSE (
                        ROUND(
                            (
                                (COALESCE(sa.month_4, 0) + COALESCE(sa.month_5, 0) + COALESCE(sa.month_6, 0) + sa.month_7 + sa.month_8 + sa.month_9 +
                                sa.month_10 + sa.month_11 + sa.month_12
                                ) / NULLIF(
                                    -- Hitung jumlah bulan dengan nilai > 0
                                    (CASE WHEN sa.month_4 > 0 THEN 1 ELSE 0 END +
                                    CASE WHEN sa.month_5 > 0 THEN 1 ELSE 0 END +
                                    CASE WHEN sa.month_6 > 0 THEN 1 ELSE 0 END +
                                    CASE WHEN sa.month_7 > 0 THEN 1 ELSE 0 END +
                                    CASE WHEN sa.month_8 > 0 THEN 1 ELSE 0 END +
                                    CASE WHEN sa.month_9 > 0 THEN 1 ELSE 0 END +
                                    CASE WHEN sa.month_10 > 0 THEN 1 ELSE 0 END +
                                    CASE WHEN sa.month_11 > 0 THEN 1 ELSE 0 END +
                                    CASE WHEN sa.month_12 > 0 THEN 1 ELSE 0 END
                                    ), 0
                                )
                            ) * 
                            NULLIF(REPLACE(mp.price, ',', ''), '')::NUMERIC
                        )::NUMERIC
                    )
                    END AS average_9_month_value,
                    CASE
                    WHEN (
                        COALESCE(sa.month_7, 0) + COALESCE(sa.month_8, 0) + COALESCE(sa.month_9, 0) + sa.month_10 + sa.month_11 + sa.month_12
                    ) = 0 THEN 0
                    WHEN (COALESCE(sa.month_7, 0) + COALESCE(sa.month_8, 0) + COALESCE(sa.month_9, 0)) = 0 THEN 0
                    ELSE (
                        ROUND((
                            COALESCE(sa.month_7, 0) + COALESCE(sa.month_8, 0) + COALESCE(sa.month_9, 0) + sa.month_10 + sa.month_11 + sa.month_12
                        ) / NULLIF(
                            (CASE WHEN sa.month_7 > 0 THEN 1 ELSE 0 END +
                            CASE WHEN sa.month_8 > 0 THEN 1 ELSE 0 END +
                            CASE WHEN sa.month_9 > 0 THEN 1 ELSE 0 END +
                            CASE WHEN sa.month_10 > 0 THEN 1 ELSE 0 END +
                            CASE WHEN sa.month_11 > 0 THEN 1 ELSE 0 END +
                            CASE WHEN sa.month_12 > 0 THEN 1 ELSE 0 END
                            ), 0)
                        )::INTEGER
                    )
                    END AS average_6_month_unit,
                    CASE
                    WHEN (
                        COALESCE(sa.month_7, 0) + COALESCE(sa.month_8, 0) + COALESCE(sa.month_9, 0) + sa.month_10 + sa.month_11 + sa.month_12
                    ) = 0 THEN 0
                    WHEN (COALESCE(sa.month_7, 0) + COALESCE(sa.month_8, 0) + COALESCE(sa.month_9, 0)) = 0 THEN 0
                    ELSE (
                        ROUND(
                            (
                                COALESCE(sa.month_7, 0) + COALESCE(sa.month_8, 0) + COALESCE(sa.month_9, 0) + sa.month_10 + sa.month_11 + sa.month_12
                            ) / NULLIF(
                                (CASE WHEN sa.month_7 > 0 THEN 1 ELSE 0 END +
                                CASE WHEN sa.month_8 > 0 THEN 1 ELSE 0 END +
                                CASE WHEN sa.month_9 > 0 THEN 1 ELSE 0 END +
                                CASE WHEN sa.month_10 > 0 THEN 1 ELSE 0 END +
                                CASE WHEN sa.month_11 > 0 THEN 1 ELSE 0 END +
                                CASE WHEN sa.month_12 > 0 THEN 1 ELSE 0 END
                                ), 0
                            ) *
                            -- Harga produk dari master product
                            NULLIF(REPLACE(mp.price, ',', ''), '')::NUMERIC
                        )::NUMERIC
                    )
                    END AS average_6_month_value,
                    CASE 
                    WHEN (COALESCE(sa.month_10, 0) + sa.month_11 + sa.month_12) = 0 THEN 0
                    ELSE ROUND((
                        COALESCE(sa.month_10, 0) + sa.month_11 + sa.month_12
                    ) / NULLIF(
                        (CASE WHEN sa.month_10 > 0 THEN 1 ELSE 0 END +
                        CASE WHEN sa.month_11 > 0 THEN 1 ELSE 0 END +
                        CASE WHEN sa.month_12 > 0 THEN 1 ELSE 0 END
                        ), 0
                    )
                    )::INTEGER
                    END AS average_3_month_unit,
                    CASE 
                        WHEN (COALESCE(sa.month_10, 0) + sa.month_11 + sa.month_12) = 0 THEN 0
                        ELSE ROUND(
                            (COALESCE(sa.month_10, 0) + sa.month_11 + sa.month_12) / 
                            NULLIF(
                                (CASE WHEN sa.month_10 > 0 THEN 1 ELSE 0 END +
                                CASE WHEN sa.month_11 > 0 THEN 1 ELSE 0 END +
                                CASE WHEN sa.month_12 > 0 THEN 1 ELSE 0 END
                                ), 0
                            )
                            * NULLIF(REPLACE(mp.price, ',', ''), '')::NUMERIC
                        )::NUMERIC
                    END AS average_3_month_value,
                ROUND((COALESCE(sa.month_1, 0) + sa.month_2 + sa.month_3 + sa.month_4 + sa.month_5 + sa.month_6 + 
                sa.month_7 + sa.month_8 + sa.month_9 + sa.month_10 + sa.month_11 + sa.month_12) / 12.0)::INTEGER AS average_sales,
                (ROUND((COALESCE(sa.month_1, 0) + sa.month_2 + sa.month_3 + sa.month_4 + sa.month_5 + sa.month_6 + 
                        sa.month_7 + sa.month_8 + sa.month_9 + sa.month_10 + sa.month_11 + sa.month_12) / 12.0)::INTEGER - 
                sa.stock_on_hand_unit) AS purchase_suggestion,

                ((ROUND((COALESCE(sa.month_1, 0) + sa.month_2 + sa.month_3 + sa.month_4 + sa.month_5 + sa.month_6 + 
                        sa.month_7 + sa.month_8 + sa.month_9 + sa.month_10 + sa.month_11 + sa.month_12) / 12.0)::INTEGER - 
                sa.stock_on_hand_unit) * NULLIF(REPLACE(mp.price, ',', ''), '')::NUMERIC) AS purchase_value,
                COALESCE(st.stock_on_hand_unit, 0) AS stock_on_hand_unit,
                COALESCE(po.qty_po, 0) AS qty_po,
                COALESCE(po.qty_sc_reg, 0) AS qty_sc_reg,
                CASE 
                WHEN COALESCE(sa.stock_on_hand_unit, 0) = 0 THEN 0
                ELSE ROUND(
                    COALESCE(sa.stock_on_hand_unit, 0) / 
                    NULLIF(
                        (CASE 
                            WHEN (COALESCE(sa.month_10, 0) + sa.month_11 + sa.month_12) = 0 
                            THEN 1 
                            ELSE ROUND((
                                COALESCE(sa.month_10, 0) + sa.month_11 + sa.month_12
                            ) / NULLIF(
                                (CASE WHEN sa.month_10 > 0 THEN 1 ELSE 0 END +
                                CASE WHEN sa.month_11 > 0 THEN 1 ELSE 0 END +
                                CASE WHEN sa.month_12 > 0 THEN 1 ELSE 0 END
                                ), 0
                            ), 0)
                        END) * 30, 1
                    ))
                END AS doi_3_month,
                CASE
                    WHEN yearly_average_unit < 0 AND average_9_month_unit < 0 
                        AND average_6_month_unit < 0 AND average_3_month_unit < 0 
                        THEN 'TREND TAK BERATURAN'
                    WHEN yearly_average_unit > average_9_month_unit 
                        AND average_9_month_unit < average_6_month_unit 
                        AND average_6_month_unit < average_3_month_unit 
                        THEN 'TREND NAIK'
                    WHEN yearly_average_unit < average_9_month_unit 
                        AND average_9_month_unit < average_6_month_unit 
                        AND average_6_month_unit < average_3_month_unit 
                        THEN 'TREND PROGRESIF NAIK'
                    WHEN yearly_average_unit > average_9_month_unit 
                        AND average_9_month_unit > average_6_month_unit 
                        AND average_6_month_unit > average_3_month_unit 
                        THEN 'TREND PROGRESIF TURUN'
                    WHEN yearly_average_unit < average_9_month_unit 
                        AND average_9_month_unit > average_6_month_unit 
                        AND average_6_month_unit > average_3_month_unit 
                        THEN 'TREND TURUN'
                    ELSE 'TREND TAK BERATURAN'
                END AS status_trend,
                CASE
                WHEN yearly_average_unit = 0 THEN '0%'
                ELSE CONCAT(ROUND(((average_3_month_unit - yearly_average_unit) / yearly_average_unit) * 100/100)::TEXT, '%')
                END AS delta
            ")
                ->whereRaw("(sa.dist_code LIKE '%$distCode%'
                AND sa.kode_cabang LIKE '%$branch%'
                AND mc.region_name LIKE '%$regionName%'
                AND sa.chnl_code LIKE '%$chnlCode%'
                AND mp.brand_name LIKE '%$brandName%'
                AND mp.status_product LIKE '%$statusProduct%')")
        
                ->when($search, function ($query) use ($search) {
                    return $query->where(function ($subQuery) use ($search) {
                        $subQuery->where('sa.item_code', 'ILIKE', "%{$search}%")
                            ->orWhere('sa.chnl_code', 'ILIKE', "%{$search}%")
                            ->orWhere('sa.dist_code', 'ILIKE', "%{$search}%")
                            ->orWhere('mc.region_name', 'ILIKE', "%{$search}%")
                            ->orWhere('mc.area_name', 'ILIKE', "%{$search}%")
                            ->orWhere('mk.kategori', 'ILIKE', "%{$search}%")
                            ->orWhere('mp.parent_code', 'ILIKE', "%{$search}%")
                            ->orWhere('mp.item_name', 'ILIKE', "%{$search}%")
                            ->orWhere('mp.status_product', 'ILIKE', "%{$search}%")
                            ->orWhere('mc.nama_cabang', 'ILIKE', "%{$search}%");
                    });
                });
            $finalDataResult = $finalData->get();

            $finalDataWithPIC = $finalDataResult->map(function ($row) {
                // Tentukan DOI berdasarkan distributor
                $doi_limit = match ($row->dist_code) {
                    'TRS' => 30,
                    'PPG' => 45,
                    'PVL' => 60,
                    default => 0, 
                };
            
                // Logika untuk menentukan PIC berdasarkan dist_code
                if ($row->dist_code === 'PVL') {
                    // Rumus untuk PVL
                    if ($row->status_trend === 'TREND TAK BERATURAN' && $row->delta < 0 && $row->doi_3_month < 60 && $row->service_level < 90) {
                        $row->pic = 'SCM';
                    } elseif ($row->status_trend === 'TREND TAK BERATURAN' && $row->delta < 0 && $row->doi_3_month < 60 && $row->service_level > 91) {
                        $row->pic = 'SALES';
                    } elseif ($row->status_trend === 'TREND PROGRESIF TURUN' && $row->delta < 0 && $row->doi_3_month < 60) {
                        $row->pic = 'SALES';
                    } elseif ($row->status_trend === 'TREND TURUN' && $row->delta < 0 && $row->doi_3_month < 60) {
                        $row->pic = 'SALES';
                    } elseif ($row->status_trend === 'TREND TAK BERATURAN' && $row->delta < 0 && $row->doi_3_month > 60) {
                        $row->pic = 'MARKETING';
                    } elseif ($row->status_trend === 'TREND TURUN' && $row->delta < 0 && $row->doi_3_month > 60) {
                        $row->pic = 'MARKETING';
                    } elseif ($row->status_trend === 'TREND PROGRESIF TURUN' && $row->delta < 0 && $row->doi_3_month > 60) {
                        $row->pic = 'MARKETING';
                    } elseif ($row->status_trend === 'TREND NAIK' && $row->delta < 0 && $row->doi_3_month < 60) {
                        $row->pic = 'SCM';
                    } elseif ($row->status_trend === 'TREND NAIK' && $row->delta < 0 && $row->doi_3_month > 60) {
                        $row->pic = 'MARKETING';
                    } elseif ($row->status_trend === 'TREND NAIK' && $row->delta > 0 && $row->doi_3_month < 60) {
                        $row->pic = 'SALES';
                    } else {
                        $row->pic = '';
                    }
                } elseif ($row->dist_code === 'PPG') {
                    // Rumus untuk PPG
                    if ($row->status_trend === 'TREND TAK BERATURAN' && $row->delta < 0 && $row->doi_3_month < 45) {
                        $row->pic = 'SCM/SALES';
                    } elseif ($row->status_trend === 'TREND PROGRESIF TURUN' && $row->delta < 0 && $row->doi_3_month < 45) {
                        $row->pic = 'SALES';
                    } elseif ($row->status_trend === 'TREND TURUN' && $row->delta < 0 && $row->doi_3_month < 45) {
                        $row->pic = 'SALES';
                    } elseif ($row->status_trend === 'TREND TAK BERATURAN' && $row->delta < 0 && $row->doi_3_month > 45) {
                        $row->pic = 'MARKETING';
                    } elseif ($row->status_trend === 'TREND TURUN' && $row->delta < 0 && $row->doi_3_month > 45) {
                        $row->pic = 'MARKETING';
                    } elseif ($row->status_trend === 'TREND PROGRESIF TURUN' && $row->delta < 0 && $row->doi_3_month > 45) {
                        $row->pic = 'MARKETING';
                    } elseif ($row->status_trend === 'TREND NAIK' && $row->delta < 0 && $row->doi_3_month < 45) {
                        $row->pic = 'SCM';
                    } elseif ($row->status_trend === 'TREND NAIK' && $row->delta < 0 && $row->doi_3_month > 45) {
                        $row->pic = 'MARKETING';
                    } elseif ($row->status_trend === 'TREND NAIK' && $row->delta > 0 && $row->doi_3_month < 45) {
                        $row->pic = 'SALES';
                    } else {
                        $row->pic = '';
                    }
                } elseif ($row->dist_code === 'TRS') {
                    // Rumus untuk TRS
                    if ($row->status_trend === 'TREND TAK BERATURAN' && $row->delta < 0 && $row->doi_3_month < 30) {
                        $row->pic = 'SCM/SALES';
                    } elseif ($row->status_trend === 'TREND PROGRESIF TURUN' && $row->delta < 0 && $row->doi_3_month < 30) {
                        $row->pic = 'SALES';
                    } elseif ($row->status_trend === 'TREND TURUN' && $row->delta < 0 && $row->doi_3_month < 30) {
                        $row->pic = 'SALES';
                    } elseif ($row->status_trend === 'TREND TAK BERATURAN' && $row->delta < 0 && $row->doi_3_month > 30) {
                        $row->pic = 'MARKETING';
                    } elseif ($row->status_trend === 'TREND TURUN' && $row->delta < 0 && $row->doi_3_month > 30) {
                        $row->pic = 'MARKETING';
                    } elseif ($row->status_trend === 'TREND PROGRESIF TURUN' && $row->delta < 0 && $row->doi_3_month > 30) {
                        $row->pic = 'MARKETING';
                    } elseif ($row->status_trend === 'TREND NAIK' && $row->delta < 0 && $row->doi_3_month < 30) {
                        $row->pic = 'SCM';
                    } elseif ($row->status_trend === 'TREND NAIK' && $row->delta < 0 && $row->doi_3_month > 30) {
                        $row->pic = 'MARKETING';
                    } elseif ($row->status_trend === 'TREND NAIK' && $row->delta > 0 && $row->doi_3_month < 30) {
                        $row->pic = 'SALES';
                    } else {
                        $row->pic = '';
                    }
                }
            
                return $row;
            });

            return $finalDataWithPIC;
        } catch (\Exception $e) {
            Log::error('Error in getTrendAnalysis:', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    public function getTrendAnalysis($request, $selectedYear, $selectedMonth, $search = '', $pagination = [])
    {
        try {
            $selectedYear = $request['tahun'];
            $selectedMonth = $request['selected_month'];
            $search = $request['search'] ?? '';
            $pagination = [
                'limit' => $request['limit'] ?? 10,
                'offset' => $request['offset'] ?? 0
            ];
            $distCode = $request['dist_code'] ?? '%';
            $branch = $request['branch'] ?? '%';
            $regionName = $request['region_name'] ?? '%';
            $chnlCode = $request['chnl_code'] ?? '%';
            $brandName = $request['brand_name'] ?? '%';
            $statusProduct = $request['status_product'] ?? '%';

            $limit = $pagination['limit'];
            $offset = $pagination['offset'];

            $monthYearMap = [];
            for ($i = 0; $i < 12; $i++) {
                $month = ($selectedMonth - $i) > 0 ? ($selectedMonth - $i) : (12 + ($selectedMonth - $i));
                $year = ($selectedMonth - $i) > 0 ? $selectedYear : ($selectedYear - 1);
                $monthYearMap["month_" . (12 - $i)] = ['month' => $month, 'year' => $year];
            }

            $aggregatedSales = DB::table('vw_sales__units as s')
                ->selectRaw("
                CAST(s.tahun AS INTEGER) AS tahun,
                s.item_code,
                s.dist_code,
                s.kode_cabang,
                s.chnl_code,
                CAST(s.bulan AS INTEGER) AS bulan,
                SUM(COALESCE(NULLIF(REPLACE(s.net_sales_unit, ',', ''), '')::NUMERIC, 0))::INTEGER AS net_sales_unit
            ")
                ->whereRaw("s.bulan IS NOT NULL AND s.bulan <> ''")
                ->groupByRaw("
                CAST(s.tahun AS INTEGER),
                s.item_code,
                s.dist_code,
                s.kode_cabang,
                s.chnl_code,
                CAST(s.bulan AS INTEGER)
            ");

            $aggregatedStock = DB::table('vw_stock__details as st')
                ->selectRaw("
                st.item_code,
                st.dist_code,
                st.kode_cabang,
                SUM(COALESCE(NULLIF(REPLACE(st.on_hand_unit, ',', ''), '')::NUMERIC, 0))::INTEGER AS stock_on_hand_unit
            ")
                ->where('st.tahun', $selectedYear)
                ->where('st.bulan', $selectedMonth)
                ->groupBy('st.item_code', 'st.dist_code', 'st.kode_cabang');

            $poData = DB::table('p_o_custs as po')
                ->selectRaw("
                po.mtg_code AS item_code,
                po.dist_code,
                po.branch_code AS kode_cabang,
                EXTRACT(YEAR FROM CAST(po.tgl_order AS DATE)) AS tahun,
                EXTRACT(MONTH FROM CAST(po.tgl_order AS DATE)) AS bulan,
                SUM(COALESCE(NULLIF(REPLACE(po.qty_po, ',', ''), '')::NUMERIC, 0)) AS qty_po,
                SUM(COALESCE(NULLIF(REPLACE(po.qty_sc_reg, ',', ''), '')::NUMERIC, 0)) AS qty_sc_reg
            ")
                ->whereRaw("
                EXTRACT(YEAR FROM CAST(po.tgl_order AS DATE)) = ? AND EXTRACT(MONTH FROM CAST(po.tgl_order AS DATE)) = ?
            ", [$selectedYear, $selectedMonth])
                ->groupBy('po.mtg_code', 'po.dist_code', 'po.branch_code', 'tahun', 'bulan');

            $salesMonths = DB::table(DB::raw("({$aggregatedSales->toSql()}) as s"))
                ->mergeBindings($aggregatedSales)
                ->selectRaw("
                s.tahun,
                s.item_code,
                s.dist_code,
                s.kode_cabang,
                s.chnl_code,
                s.bulan,
                s.net_sales_unit
            ")
                ->whereRaw("(s.tahun = ? AND s.bulan <= ?) OR (s.tahun = ? - 1 AND s.bulan > ?)", [
                    $selectedYear,
                    $selectedMonth,
                    $selectedYear,
                    $selectedMonth,
                ]);

            $shiftedSales = DB::table(DB::raw("({$salesMonths->toSql()}) as sm"))
                ->mergeBindings($salesMonths)
                ->leftJoinSub($aggregatedStock, 'st', function ($join) {
                    $join->on('sm.item_code', '=', 'st.item_code')
                        ->on('sm.dist_code', '=', 'st.dist_code')
                        ->on('sm.kode_cabang', '=', 'st.kode_cabang');
                })
                ->selectRaw("
                    sm.tahun,
                    sm.item_code,
                    sm.dist_code,
                    sm.kode_cabang,
                    sm.chnl_code,
                    MAX(COALESCE(st.stock_on_hand_unit, 0)) AS stock_on_hand_unit,
                    " . implode(", ", array_map(function ($alias, $data) {
                    return "MAX(CASE WHEN (sm.bulan = {$data['month']} AND sm.tahun = {$data['year']}) THEN sm.net_sales_unit ELSE 0 END) AS {$alias}";
                    }, array_keys($monthYearMap), $monthYearMap)) . ",
                    CASE
                    WHEN (
                        COALESCE(SUM(CASE WHEN sm.bulan BETWEEN 1 AND 12 THEN sm.net_sales_unit ELSE 0 END), 0)
                    ) = 0 THEN 0
                    WHEN (
                        COALESCE(SUM(CASE WHEN sm.bulan IN (1, 2, 3) THEN sm.net_sales_unit ELSE 0 END), 0)
                    ) = 0 THEN 0
                    ELSE (
                        ROUND(
                            SUM(CASE WHEN sm.bulan BETWEEN 1 AND 12 THEN sm.net_sales_unit ELSE 0 END) / 
                            NULLIF(
                                -- Jumlah bulan non-nol (1 hingga 12)
                                (SUM(CASE WHEN sm.bulan = 1 AND sm.net_sales_unit > 0 THEN 1 ELSE 0 END) +
                                SUM(CASE WHEN sm.bulan = 2 AND sm.net_sales_unit > 0 THEN 1 ELSE 0 END) +
                                SUM(CASE WHEN sm.bulan = 3 AND sm.net_sales_unit > 0 THEN 1 ELSE 0 END) +
                                SUM(CASE WHEN sm.bulan = 4 AND sm.net_sales_unit > 0 THEN 1 ELSE 0 END) +
                                SUM(CASE WHEN sm.bulan = 5 AND sm.net_sales_unit > 0 THEN 1 ELSE 0 END) +
                                SUM(CASE WHEN sm.bulan = 6 AND sm.net_sales_unit > 0 THEN 1 ELSE 0 END) +
                                SUM(CASE WHEN sm.bulan = 7 AND sm.net_sales_unit > 0 THEN 1 ELSE 0 END) +
                                SUM(CASE WHEN sm.bulan = 8 AND sm.net_sales_unit > 0 THEN 1 ELSE 0 END) +
                                SUM(CASE WHEN sm.bulan = 9 AND sm.net_sales_unit > 0 THEN 1 ELSE 0 END) +
                                SUM(CASE WHEN sm.bulan = 10 AND sm.net_sales_unit > 0 THEN 1 ELSE 0 END) +
                                SUM(CASE WHEN sm.bulan = 11 AND sm.net_sales_unit > 0 THEN 1 ELSE 0 END) +
                                SUM(CASE WHEN sm.bulan = 12 AND sm.net_sales_unit > 0 THEN 1 ELSE 0 END)
                                ), 0
                            )
                        )::INTEGER
                    )
                END AS yearly_average_unit,

                CASE
                    WHEN (
                        COALESCE(SUM(CASE WHEN sm.bulan BETWEEN 4 AND 12 THEN sm.net_sales_unit ELSE 0 END), 0)
                    ) = 0 THEN 0
                    ELSE (
                        ROUND(
                            SUM(CASE WHEN sm.bulan BETWEEN 4 AND 12 THEN sm.net_sales_unit ELSE 0 END) / 
                            NULLIF(
                                (SUM(CASE WHEN sm.bulan = 4 AND sm.net_sales_unit > 0 THEN 1 ELSE 0 END) +
                                SUM(CASE WHEN sm.bulan = 5 AND sm.net_sales_unit > 0 THEN 1 ELSE 0 END) +
                                SUM(CASE WHEN sm.bulan = 6 AND sm.net_sales_unit > 0 THEN 1 ELSE 0 END) +
                                SUM(CASE WHEN sm.bulan = 7 AND sm.net_sales_unit > 0 THEN 1 ELSE 0 END) +
                                SUM(CASE WHEN sm.bulan = 8 AND sm.net_sales_unit > 0 THEN 1 ELSE 0 END) +
                                SUM(CASE WHEN sm.bulan = 9 AND sm.net_sales_unit > 0 THEN 1 ELSE 0 END) +
                                SUM(CASE WHEN sm.bulan = 10 AND sm.net_sales_unit > 0 THEN 1 ELSE 0 END) +
                                SUM(CASE WHEN sm.bulan = 11 AND sm.net_sales_unit > 0 THEN 1 ELSE 0 END) +
                                SUM(CASE WHEN sm.bulan = 12 AND sm.net_sales_unit > 0 THEN 1 ELSE 0 END)
                                ), 0
                            )
                        )::INTEGER
                    )
                END AS average_9_month_unit,

                CASE
                    WHEN (
                        COALESCE(SUM(CASE WHEN sm.bulan BETWEEN 7 AND 12 THEN sm.net_sales_unit ELSE 0 END), 0)
                    ) = 0 THEN 0
                    ELSE (
                        ROUND(
                            SUM(CASE WHEN sm.bulan BETWEEN 7 AND 12 THEN sm.net_sales_unit ELSE 0 END) / 
                            NULLIF(
                                -- Jumlah bulan non-nol (Juli hingga Desember)
                                (SUM(CASE WHEN sm.bulan = 7 AND sm.net_sales_unit > 0 THEN 1 ELSE 0 END) +
                                SUM(CASE WHEN sm.bulan = 8 AND sm.net_sales_unit > 0 THEN 1 ELSE 0 END) +
                                SUM(CASE WHEN sm.bulan = 9 AND sm.net_sales_unit > 0 THEN 1 ELSE 0 END) +
                                SUM(CASE WHEN sm.bulan = 10 AND sm.net_sales_unit > 0 THEN 1 ELSE 0 END) +
                                SUM(CASE WHEN sm.bulan = 11 AND sm.net_sales_unit > 0 THEN 1 ELSE 0 END) +
                                SUM(CASE WHEN sm.bulan = 12 AND sm.net_sales_unit > 0 THEN 1 ELSE 0 END)
                                ), 0
                            )
                        )::INTEGER
                    )
                END AS average_6_month_unit,

                CASE
                    WHEN (
                        COALESCE(SUM(CASE WHEN sm.bulan BETWEEN 10 AND 12 THEN sm.net_sales_unit ELSE 0 END), 0)
                    ) = 0 THEN 0
                    ELSE (
                        ROUND(
                            SUM(CASE WHEN sm.bulan BETWEEN 10 AND 12 THEN sm.net_sales_unit ELSE 0 END) / 
                            NULLIF(
                                -- Jumlah bulan non-nol (Oktober hingga Desember)
                                (SUM(CASE WHEN sm.bulan = 10 AND sm.net_sales_unit > 0 THEN 1 ELSE 0 END) +
                                SUM(CASE WHEN sm.bulan = 11 AND sm.net_sales_unit > 0 THEN 1 ELSE 0 END) +
                                SUM(CASE WHEN sm.bulan = 12 AND sm.net_sales_unit > 0 THEN 1 ELSE 0 END)
                                ), 0
                            )
                        )::INTEGER
                    )
                END AS average_3_month_unit
                ")
                ->groupByRaw("
                    sm.tahun,
                    sm.item_code,
                    sm.dist_code,
                    sm.kode_cabang,
                    sm.chnl_code
                ");

            $finalData = DB::table(DB::raw("({$shiftedSales->toSql()}) as sa"))
                ->mergeBindings($shiftedSales)
                ->leftJoinSub($poData, 'po', function ($join) {
                    $join->on('sa.item_code', '=', 'po.item_code')
                        ->on('sa.dist_code', '=', 'po.dist_code')
                        ->on('sa.kode_cabang', '=', 'po.kode_cabang');
                })
                ->leftJoinSub($aggregatedStock, 'st', function ($join) {
                    $join->on('sa.item_code', '=', 'st.item_code')
                        ->on('sa.dist_code', '=', 'st.dist_code')
                        ->on('sa.kode_cabang', '=', 'st.kode_cabang');
                })
                ->leftJoin('m__products as mp', 'sa.item_code', '=', 'mp.item_code')
                ->leftJoin('m__kategoris as mk', 'mp.parent_code', '=', 'mk.parent_code')
                ->leftJoin('m__cabangs as mc', 'sa.kode_cabang', '=', 'mc.kode_cabang')
                ->selectRaw("
                DISTINCT sa.tahun,
                sa.item_code,
                sa.dist_code,
                sa.kode_cabang,
                sa.chnl_code,
                mp.brand_name,
                mp.status_product,
                mk.kategori,
                mp.parent_code,
                mp.item_name,
                mc.region_name,
                mc.area_name,
                mc.nama_cabang,
                sa.month_1,
                sa.month_2,
                sa.month_3,
                sa.month_4,
                sa.month_5,
                sa.month_6,
                sa.month_7,
                sa.month_8,
                sa.month_9,
                sa.month_10,
                sa.month_11,
                sa.month_12,
                CASE 
                WHEN po.qty_po = 0 OR po.qty_po IS NULL THEN '0%'
                ELSE CONCAT(ROUND((po.qty_sc_reg::NUMERIC / NULLIF(po.qty_po, 0)) * 100), '%')
                END AS service_level,
                CASE
                WHEN (
                    COALESCE(sa.month_1, 0) + COALESCE(sa.month_2, 0) + COALESCE(sa.month_3, 0) + sa.month_4 + sa.month_5 + sa.month_6 +
                    sa.month_7 + sa.month_8 + sa.month_9 + sa.month_10 + sa.month_11 + sa.month_12
                ) = 0 THEN 0
                WHEN (COALESCE(sa.month_1, 0) + COALESCE(sa.month_2, 0) + COALESCE(sa.month_3, 0)) = 0 THEN 0
                ELSE (
                    ROUND((
                        COALESCE(sa.month_1, 0) + COALESCE(sa.month_2, 0) + COALESCE(sa.month_3, 0) + sa.month_4 + sa.month_5 + sa.month_6 +
                        sa.month_7 + sa.month_8 + sa.month_9 + sa.month_10 + sa.month_11 + sa.month_12
                    ) / NULLIF(
                        -- Hitung jumlah bulan yang memiliki nilai > 0
                        (CASE WHEN sa.month_1 > 0 THEN 1 ELSE 0 END +
                        CASE WHEN sa.month_2 > 0 THEN 1 ELSE 0 END +
                        CASE WHEN sa.month_3 > 0 THEN 1 ELSE 0 END +
                        CASE WHEN sa.month_4 > 0 THEN 1 ELSE 0 END +
                        CASE WHEN sa.month_5 > 0 THEN 1 ELSE 0 END +
                        CASE WHEN sa.month_6 > 0 THEN 1 ELSE 0 END +
                        CASE WHEN sa.month_7 > 0 THEN 1 ELSE 0 END +
                        CASE WHEN sa.month_8 > 0 THEN 1 ELSE 0 END +
                        CASE WHEN sa.month_9 > 0 THEN 1 ELSE 0 END +
                        CASE WHEN sa.month_10 > 0 THEN 1 ELSE 0 END +
                        CASE WHEN sa.month_11 > 0 THEN 1 ELSE 0 END +
                        CASE WHEN sa.month_12 > 0 THEN 1 ELSE 0 END
                        ), 0)
                    )::INTEGER
                )
                END AS yearly_average_unit,
                CASE
                    WHEN (
                        COALESCE(sa.month_1, 0) + COALESCE(sa.month_2, 0) + COALESCE(sa.month_3, 0) + sa.month_4 + sa.month_5 + sa.month_6 +
                        sa.month_7 + sa.month_8 + sa.month_9 + sa.month_10 + sa.month_11 + sa.month_12
                    ) = 0 THEN 0
                    WHEN (COALESCE(sa.month_1, 0) + COALESCE(sa.month_2, 0) + COALESCE(sa.month_3, 0)) = 0 THEN 0
                    ELSE (
                        ROUND(
                            (
                                COALESCE(sa.month_1, 0) + COALESCE(sa.month_2, 0) + COALESCE(sa.month_3, 0) + sa.month_4 + sa.month_5 + sa.month_6 +
                                sa.month_7 + sa.month_8 + sa.month_9 + sa.month_10 + sa.month_11 + sa.month_12
                            ) / NULLIF(
                                (CASE WHEN sa.month_1 > 0 THEN 1 ELSE 0 END +
                                CASE WHEN sa.month_2 > 0 THEN 1 ELSE 0 END +
                                CASE WHEN sa.month_3 > 0 THEN 1 ELSE 0 END +
                                CASE WHEN sa.month_4 > 0 THEN 1 ELSE 0 END +
                                CASE WHEN sa.month_5 > 0 THEN 1 ELSE 0 END +
                                CASE WHEN sa.month_6 > 0 THEN 1 ELSE 0 END +
                                CASE WHEN sa.month_7 > 0 THEN 1 ELSE 0 END +
                                CASE WHEN sa.month_8 > 0 THEN 1 ELSE 0 END +
                                CASE WHEN sa.month_9 > 0 THEN 1 ELSE 0 END +
                                CASE WHEN sa.month_10 > 0 THEN 1 ELSE 0 END +
                                CASE WHEN sa.month_11 > 0 THEN 1 ELSE 0 END +
                                CASE WHEN sa.month_12 > 0 THEN 1 ELSE 0 END
                                ), 0
                            )
                        ) * 
                        NULLIF(REPLACE(mp.price, ',', ''), '')::NUMERIC
                    )
                END AS yearly_average_value,
                    CASE
                    WHEN (
                        COALESCE(sa.month_4, 0) + COALESCE(sa.month_5, 0) + COALESCE(sa.month_6, 0) + sa.month_7 + sa.month_8 + sa.month_9 +
                        sa.month_10 + sa.month_11 + sa.month_12
                    ) = 0 THEN 0
                    WHEN (COALESCE(sa.month_4, 0) + COALESCE(sa.month_5, 0) + COALESCE(sa.month_6, 0)) = 0 THEN 0
                    ELSE (
                        ROUND((
                            COALESCE(sa.month_4, 0) + COALESCE(sa.month_5, 0) + COALESCE(sa.month_6, 0) + sa.month_7 + sa.month_8 + sa.month_9 +
                            sa.month_10 + sa.month_11 + sa.month_12
                        ) / NULLIF(
                            (CASE WHEN sa.month_4 > 0 THEN 1 ELSE 0 END +
                            CASE WHEN sa.month_5 > 0 THEN 1 ELSE 0 END +
                            CASE WHEN sa.month_6 > 0 THEN 1 ELSE 0 END +
                            CASE WHEN sa.month_7 > 0 THEN 1 ELSE 0 END +
                            CASE WHEN sa.month_8 > 0 THEN 1 ELSE 0 END +
                            CASE WHEN sa.month_9 > 0 THEN 1 ELSE 0 END +
                            CASE WHEN sa.month_10 > 0 THEN 1 ELSE 0 END +
                            CASE WHEN sa.month_11 > 0 THEN 1 ELSE 0 END +
                            CASE WHEN sa.month_12 > 0 THEN 1 ELSE 0 END
                            ), 0)
                        )::INTEGER
                    )
                    END AS average_9_month_unit,
                    CASE
                    WHEN (
                        COALESCE(sa.month_4, 0) + COALESCE(sa.month_5, 0) + COALESCE(sa.month_6, 0) + sa.month_7 + sa.month_8 + sa.month_9 +
                        sa.month_10 + sa.month_11 + sa.month_12
                    ) = 0 THEN 0
                    WHEN (COALESCE(sa.month_4, 0) + COALESCE(sa.month_5, 0) + COALESCE(sa.month_6, 0)) = 0 THEN 0
                    ELSE (
                        ROUND(
                            (
                                (COALESCE(sa.month_4, 0) + COALESCE(sa.month_5, 0) + COALESCE(sa.month_6, 0) + sa.month_7 + sa.month_8 + sa.month_9 +
                                sa.month_10 + sa.month_11 + sa.month_12
                                ) / NULLIF(
                                    -- Hitung jumlah bulan dengan nilai > 0
                                    (CASE WHEN sa.month_4 > 0 THEN 1 ELSE 0 END +
                                    CASE WHEN sa.month_5 > 0 THEN 1 ELSE 0 END +
                                    CASE WHEN sa.month_6 > 0 THEN 1 ELSE 0 END +
                                    CASE WHEN sa.month_7 > 0 THEN 1 ELSE 0 END +
                                    CASE WHEN sa.month_8 > 0 THEN 1 ELSE 0 END +
                                    CASE WHEN sa.month_9 > 0 THEN 1 ELSE 0 END +
                                    CASE WHEN sa.month_10 > 0 THEN 1 ELSE 0 END +
                                    CASE WHEN sa.month_11 > 0 THEN 1 ELSE 0 END +
                                    CASE WHEN sa.month_12 > 0 THEN 1 ELSE 0 END
                                    ), 0
                                )
                            ) * 
                            NULLIF(REPLACE(mp.price, ',', ''), '')::NUMERIC
                        )::NUMERIC
                    )
                    END AS average_9_month_value,
                    CASE
                    WHEN (
                        COALESCE(sa.month_7, 0) + COALESCE(sa.month_8, 0) + COALESCE(sa.month_9, 0) + sa.month_10 + sa.month_11 + sa.month_12
                    ) = 0 THEN 0
                    WHEN (COALESCE(sa.month_7, 0) + COALESCE(sa.month_8, 0) + COALESCE(sa.month_9, 0)) = 0 THEN 0
                    ELSE (
                        ROUND((
                            COALESCE(sa.month_7, 0) + COALESCE(sa.month_8, 0) + COALESCE(sa.month_9, 0) + sa.month_10 + sa.month_11 + sa.month_12
                        ) / NULLIF(
                            (CASE WHEN sa.month_7 > 0 THEN 1 ELSE 0 END +
                            CASE WHEN sa.month_8 > 0 THEN 1 ELSE 0 END +
                            CASE WHEN sa.month_9 > 0 THEN 1 ELSE 0 END +
                            CASE WHEN sa.month_10 > 0 THEN 1 ELSE 0 END +
                            CASE WHEN sa.month_11 > 0 THEN 1 ELSE 0 END +
                            CASE WHEN sa.month_12 > 0 THEN 1 ELSE 0 END
                            ), 0)
                        )::INTEGER
                    )
                    END AS average_6_month_unit,
                    CASE
                    WHEN (
                        COALESCE(sa.month_7, 0) + COALESCE(sa.month_8, 0) + COALESCE(sa.month_9, 0) + sa.month_10 + sa.month_11 + sa.month_12
                    ) = 0 THEN 0
                    WHEN (COALESCE(sa.month_7, 0) + COALESCE(sa.month_8, 0) + COALESCE(sa.month_9, 0)) = 0 THEN 0
                    ELSE (
                        ROUND(
                            (
                                COALESCE(sa.month_7, 0) + COALESCE(sa.month_8, 0) + COALESCE(sa.month_9, 0) + sa.month_10 + sa.month_11 + sa.month_12
                            ) / NULLIF(
                                (CASE WHEN sa.month_7 > 0 THEN 1 ELSE 0 END +
                                CASE WHEN sa.month_8 > 0 THEN 1 ELSE 0 END +
                                CASE WHEN sa.month_9 > 0 THEN 1 ELSE 0 END +
                                CASE WHEN sa.month_10 > 0 THEN 1 ELSE 0 END +
                                CASE WHEN sa.month_11 > 0 THEN 1 ELSE 0 END +
                                CASE WHEN sa.month_12 > 0 THEN 1 ELSE 0 END
                                ), 0
                            ) *
                            -- Harga produk dari master product
                            NULLIF(REPLACE(mp.price, ',', ''), '')::NUMERIC
                        )::NUMERIC
                    )
                    END AS average_6_month_value,
                    CASE 
                    WHEN (COALESCE(sa.month_10, 0) + sa.month_11 + sa.month_12) = 0 THEN 0
                    ELSE ROUND((
                        COALESCE(sa.month_10, 0) + sa.month_11 + sa.month_12
                    ) / NULLIF(
                        (CASE WHEN sa.month_10 > 0 THEN 1 ELSE 0 END +
                        CASE WHEN sa.month_11 > 0 THEN 1 ELSE 0 END +
                        CASE WHEN sa.month_12 > 0 THEN 1 ELSE 0 END
                        ), 0
                    )
                    )::INTEGER
                    END AS average_3_month_unit,
                    CASE 
                        WHEN (COALESCE(sa.month_10, 0) + sa.month_11 + sa.month_12) = 0 THEN 0
                        ELSE ROUND(
                            (COALESCE(sa.month_10, 0) + sa.month_11 + sa.month_12) / 
                            NULLIF(
                                (CASE WHEN sa.month_10 > 0 THEN 1 ELSE 0 END +
                                CASE WHEN sa.month_11 > 0 THEN 1 ELSE 0 END +
                                CASE WHEN sa.month_12 > 0 THEN 1 ELSE 0 END
                                ), 0
                            )
                            * NULLIF(REPLACE(mp.price, ',', ''), '')::NUMERIC
                        )::NUMERIC
                    END AS average_3_month_value,
                ROUND((COALESCE(sa.month_1, 0) + sa.month_2 + sa.month_3 + sa.month_4 + sa.month_5 + sa.month_6 + 
                sa.month_7 + sa.month_8 + sa.month_9 + sa.month_10 + sa.month_11 + sa.month_12) / 12.0)::INTEGER AS average_sales,
                (ROUND((COALESCE(sa.month_1, 0) + sa.month_2 + sa.month_3 + sa.month_4 + sa.month_5 + sa.month_6 + 
                        sa.month_7 + sa.month_8 + sa.month_9 + sa.month_10 + sa.month_11 + sa.month_12) / 12.0)::INTEGER - 
                sa.stock_on_hand_unit) AS purchase_suggestion,

                ((ROUND((COALESCE(sa.month_1, 0) + sa.month_2 + sa.month_3 + sa.month_4 + sa.month_5 + sa.month_6 + 
                        sa.month_7 + sa.month_8 + sa.month_9 + sa.month_10 + sa.month_11 + sa.month_12) / 12.0)::INTEGER - 
                sa.stock_on_hand_unit) * NULLIF(REPLACE(mp.price, ',', ''), '')::NUMERIC) AS purchase_value,
                COALESCE(st.stock_on_hand_unit, 0) AS stock_on_hand_unit,
                COALESCE(po.qty_po, 0) AS qty_po,
                COALESCE(po.qty_sc_reg, 0) AS qty_sc_reg,
                CASE 
                WHEN COALESCE(sa.stock_on_hand_unit, 0) = 0 THEN 0
                ELSE ROUND(
                    COALESCE(sa.stock_on_hand_unit, 0) / 
                    NULLIF(
                        (CASE 
                            WHEN (COALESCE(sa.month_10, 0) + sa.month_11 + sa.month_12) = 0 
                            THEN 1 
                            ELSE ROUND((
                                COALESCE(sa.month_10, 0) + sa.month_11 + sa.month_12
                            ) / NULLIF(
                                (CASE WHEN sa.month_10 > 0 THEN 1 ELSE 0 END +
                                CASE WHEN sa.month_11 > 0 THEN 1 ELSE 0 END +
                                CASE WHEN sa.month_12 > 0 THEN 1 ELSE 0 END
                                ), 0
                            ), 0)
                        END) * 30, 1
                    ))
                END AS doi_3_month,
                CASE
                    WHEN yearly_average_unit < 0 AND average_9_month_unit < 0 
                        AND average_6_month_unit < 0 AND average_3_month_unit < 0 
                        THEN 'TREND TAK BERATURAN'
                    WHEN yearly_average_unit > average_9_month_unit 
                        AND average_9_month_unit < average_6_month_unit 
                        AND average_6_month_unit < average_3_month_unit 
                        THEN 'TREND NAIK'
                    WHEN yearly_average_unit < average_9_month_unit 
                        AND average_9_month_unit < average_6_month_unit 
                        AND average_6_month_unit < average_3_month_unit 
                        THEN 'TREND PROGRESIF NAIK'
                    WHEN yearly_average_unit > average_9_month_unit 
                        AND average_9_month_unit > average_6_month_unit 
                        AND average_6_month_unit > average_3_month_unit 
                        THEN 'TREND PROGRESIF TURUN'
                    WHEN yearly_average_unit < average_9_month_unit 
                        AND average_9_month_unit > average_6_month_unit 
                        AND average_6_month_unit > average_3_month_unit 
                        THEN 'TREND TURUN'
                    ELSE 'TREND TAK BERATURAN'
                END AS status_trend,
                CASE
                WHEN yearly_average_unit = 0 THEN '0%'
                ELSE CONCAT(ROUND(((average_3_month_unit - yearly_average_unit) / yearly_average_unit) * 100/100)::TEXT, '%')
                END AS delta
            ")
                ->whereRaw("(sa.dist_code LIKE '%$distCode%'
                AND sa.kode_cabang LIKE '%$branch%'
                AND mc.region_name LIKE '%$regionName%'
                AND sa.chnl_code LIKE '%$chnlCode%'
                AND mp.brand_name LIKE '%$brandName%'
                AND mp.status_product LIKE '%$statusProduct%')")
                
                ->when($search, function ($query) use ($search) {
                    return $query->where(function ($subQuery) use ($search) {
                        $subQuery->where('sa.item_code', 'ILIKE', "%{$search}%")
                            ->orWhere('sa.chnl_code', 'ILIKE', "%{$search}%")
                            ->orWhere('sa.dist_code', 'ILIKE', "%{$search}%")
                            ->orWhere('mc.region_name', 'ILIKE', "%{$search}%")
                            ->orWhere('mc.area_name', 'ILIKE', "%{$search}%")
                            ->orWhere('mk.kategori', 'ILIKE', "%{$search}%")
                            ->orWhere('mp.parent_code', 'ILIKE', "%{$search}%")
                            ->orWhere('mp.item_name', 'ILIKE', "%{$search}%")
                            ->orWhere('mp.status_product', 'ILIKE', "%{$search}%")
                            ->orWhere('mc.nama_cabang', 'ILIKE', "%{$search}%");
                    });
                })
                ->offset($offset)
                ->limit($limit);
            $finalDataResult = $finalData->get();

            // Tambahkan kolom PIC berdasarkan kondisi
            $finalDataWithPIC = $finalDataResult->map(function ($row) {
                // Tentukan DOI berdasarkan distributor
                $doi_limit = match ($row->dist_code) {
                    'TRS' => 30,
                    'PPG' => 45,
                    'PVL' => 60,
                    default => 0, 
                };
            
                // Logika untuk menentukan PIC berdasarkan dist_code
                if ($row->dist_code === 'PVL') {
                    // Rumus untuk PVL
                    if ($row->status_trend === 'TREND TAK BERATURAN' && $row->delta < 0 && $row->doi_3_month < 60 && $row->service_level < 90) {
                        $row->pic = 'SCM';
                    } elseif ($row->status_trend === 'TREND TAK BERATURAN' && $row->delta < 0 && $row->doi_3_month < 60 && $row->service_level > 91) {
                        $row->pic = 'SALES';
                    } elseif ($row->status_trend === 'TREND PROGRESIF TURUN' && $row->delta < 0 && $row->doi_3_month < 60) {
                        $row->pic = 'SALES';
                    } elseif ($row->status_trend === 'TREND TURUN' && $row->delta < 0 && $row->doi_3_month < 60) {
                        $row->pic = 'SALES';
                    } elseif ($row->status_trend === 'TREND TAK BERATURAN' && $row->delta < 0 && $row->doi_3_month > 60) {
                        $row->pic = 'MARKETING';
                    } elseif ($row->status_trend === 'TREND TURUN' && $row->delta < 0 && $row->doi_3_month > 60) {
                        $row->pic = 'MARKETING';
                    } elseif ($row->status_trend === 'TREND PROGRESIF TURUN' && $row->delta < 0 && $row->doi_3_month > 60) {
                        $row->pic = 'MARKETING';
                    } elseif ($row->status_trend === 'TREND NAIK' && $row->delta < 0 && $row->doi_3_month < 60) {
                        $row->pic = 'SCM';
                    } elseif ($row->status_trend === 'TREND NAIK' && $row->delta < 0 && $row->doi_3_month > 60) {
                        $row->pic = 'MARKETING';
                    } elseif ($row->status_trend === 'TREND NAIK' && $row->delta > 0 && $row->doi_3_month < 60) {
                        $row->pic = 'SALES';
                    } else {
                        $row->pic = '';
                    }
                } elseif ($row->dist_code === 'PPG') {
                    // Rumus untuk PPG
                    if ($row->status_trend === 'TREND TAK BERATURAN' && $row->delta < 0 && $row->doi_3_month < 45) {
                        $row->pic = 'SCM/SALES';
                    } elseif ($row->status_trend === 'TREND PROGRESIF TURUN' && $row->delta < 0 && $row->doi_3_month < 45) {
                        $row->pic = 'SALES';
                    } elseif ($row->status_trend === 'TREND TURUN' && $row->delta < 0 && $row->doi_3_month < 45) {
                        $row->pic = 'SALES';
                    } elseif ($row->status_trend === 'TREND TAK BERATURAN' && $row->delta < 0 && $row->doi_3_month > 45) {
                        $row->pic = 'MARKETING';
                    } elseif ($row->status_trend === 'TREND TURUN' && $row->delta < 0 && $row->doi_3_month > 45) {
                        $row->pic = 'MARKETING';
                    } elseif ($row->status_trend === 'TREND PROGRESIF TURUN' && $row->delta < 0 && $row->doi_3_month > 45) {
                        $row->pic = 'MARKETING';
                    } elseif ($row->status_trend === 'TREND NAIK' && $row->delta < 0 && $row->doi_3_month < 45) {
                        $row->pic = 'SCM';
                    } elseif ($row->status_trend === 'TREND NAIK' && $row->delta < 0 && $row->doi_3_month > 45) {
                        $row->pic = 'MARKETING';
                    } elseif ($row->status_trend === 'TREND NAIK' && $row->delta > 0 && $row->doi_3_month < 45) {
                        $row->pic = 'SALES';
                    } else {
                        $row->pic = '';
                    }
                } elseif ($row->dist_code === 'TRS') {
                    // Rumus untuk TRS
                    if ($row->status_trend === 'TREND TAK BERATURAN' && $row->delta < 0 && $row->doi_3_month < 30) {
                        $row->pic = 'SCM/SALES';
                    } elseif ($row->status_trend === 'TREND PROGRESIF TURUN' && $row->delta < 0 && $row->doi_3_month < 30) {
                        $row->pic = 'SALES';
                    } elseif ($row->status_trend === 'TREND TURUN' && $row->delta < 0 && $row->doi_3_month < 30) {
                        $row->pic = 'SALES';
                    } elseif ($row->status_trend === 'TREND TAK BERATURAN' && $row->delta < 0 && $row->doi_3_month > 30) {
                        $row->pic = 'MARKETING';
                    } elseif ($row->status_trend === 'TREND TURUN' && $row->delta < 0 && $row->doi_3_month > 30) {
                        $row->pic = 'MARKETING';
                    } elseif ($row->status_trend === 'TREND PROGRESIF TURUN' && $row->delta < 0 && $row->doi_3_month > 30) {
                        $row->pic = 'MARKETING';
                    } elseif ($row->status_trend === 'TREND NAIK' && $row->delta < 0 && $row->doi_3_month < 30) {
                        $row->pic = 'SCM';
                    } elseif ($row->status_trend === 'TREND NAIK' && $row->delta < 0 && $row->doi_3_month > 30) {
                        $row->pic = 'MARKETING';
                    } elseif ($row->status_trend === 'TREND NAIK' && $row->delta > 0 && $row->doi_3_month < 30) {
                        $row->pic = 'SALES';
                    } else {
                        $row->pic = '';
                    }
                }
            
                return $row;
            });
            

            return $finalDataWithPIC;
        } catch (\Exception $e) {
            Log::error('Error in getTrendAnalysis:', ['message' => $e->getMessage()]);
            throw $e;
        }
    }

    public function countTrendAnalysis($request, $selectedYear, $selectedMonth, $search = '')
    {
        try {
            $selectedYear = $request['tahun'];
            $selectedMonth = $request['selected_month'];
            $search = $request['search'] ?? '';
            $pagination = [
                'limit' => $request['limit'] ?? 10,
                'offset' => $request['offset'] ?? 0
            ];
            $distCode = $request['dist_code'] ?? '%';
            $branch = $request['branch'] ?? '%';
            $regionName = $request['region_name'] ?? '%';
            $chnlCode = $request['chnl_code'] ?? '%';
            $brandName = $request['brand_name'] ?? '%';
            $statusProduct = $request['status_product'] ?? '%';

            $monthYearMap = [];
            for ($i = 0; $i < 12; $i++) {
                $month = ($selectedMonth - $i) > 0 ? ($selectedMonth - $i) : (12 + ($selectedMonth - $i));
                $year = ($selectedMonth - $i) > 0 ? $selectedYear : ($selectedYear - 1);
                $monthYearMap["month_" . (12 - $i)] = ['month' => $month, 'year' => $year];
            }

            $aggregatedSales = DB::table('vw_sales__units as s')
                ->selectRaw("
                CAST(s.tahun AS INTEGER) AS tahun,
                s.item_code,
                s.dist_code,
                s.kode_cabang,
                s.chnl_code,
                CAST(s.bulan AS INTEGER) AS bulan,
                SUM(COALESCE(NULLIF(REPLACE(s.net_sales_unit, ',', ''), '')::NUMERIC, 0))::INTEGER AS net_sales_unit
            ")
                ->whereRaw("s.bulan IS NOT NULL AND s.bulan <> ''")
                ->groupByRaw("
                CAST(s.tahun AS INTEGER),
                s.item_code,
                s.dist_code,
                s.kode_cabang,
                s.chnl_code,
                CAST(s.bulan AS INTEGER)
            ");

            $aggregatedStock = DB::table('vw_stock__details as st')
                ->selectRaw("
                st.item_code,
                st.dist_code,
                st.kode_cabang,
                SUM(COALESCE(NULLIF(REPLACE(st.on_hand_unit, ',', ''), '')::NUMERIC, 0))::INTEGER AS stock_on_hand_unit
            ")
                ->where('st.tahun', $selectedYear)
                ->where('st.bulan', $selectedMonth)
                ->groupBy('st.item_code', 'st.dist_code', 'st.kode_cabang');

            $poData = DB::table('p_o_custs as po')
                ->selectRaw("
                po.mtg_code AS item_code,
                po.dist_code,
                po.branch_code AS kode_cabang,
                EXTRACT(YEAR FROM CAST(po.tgl_order AS DATE)) AS tahun,
                EXTRACT(MONTH FROM CAST(po.tgl_order AS DATE)) AS bulan,
                SUM(COALESCE(NULLIF(REPLACE(po.qty_po, ',', ''), '')::NUMERIC, 0)) AS qty_po,
                SUM(COALESCE(NULLIF(REPLACE(po.qty_sc_reg, ',', ''), '')::NUMERIC, 0)) AS qty_sc_reg
            ")
                ->whereRaw("
                EXTRACT(YEAR FROM CAST(po.tgl_order AS DATE)) = ? AND EXTRACT(MONTH FROM CAST(po.tgl_order AS DATE)) = ?
            ", [$selectedYear, $selectedMonth])
                ->groupBy('po.mtg_code', 'po.dist_code', 'po.branch_code', 'tahun', 'bulan');

            $salesMonths = DB::table(DB::raw("({$aggregatedSales->toSql()}) as s"))
                ->mergeBindings($aggregatedSales)
                ->selectRaw("
                s.tahun,
                s.item_code,
                s.dist_code,
                s.kode_cabang,
                s.chnl_code,
                s.bulan,
                s.net_sales_unit
            ")
                ->whereRaw("(s.tahun = ? AND s.bulan <= ?) OR (s.tahun = ? - 1 AND s.bulan > ?)", [
                    $selectedYear,
                    $selectedMonth,
                    $selectedYear,
                    $selectedMonth,
                ]);

            $shiftedSales = DB::table(DB::raw("({$salesMonths->toSql()}) as sm"))
                ->mergeBindings($salesMonths)
                ->leftJoinSub($aggregatedStock, 'st', function ($join) {
                    $join->on('sm.item_code', '=', 'st.item_code')
                        ->on('sm.dist_code', '=', 'st.dist_code')
                        ->on('sm.kode_cabang', '=', 'st.kode_cabang');
                })
                ->selectRaw("
                    sm.tahun,
                    sm.item_code,
                    sm.dist_code,
                    sm.kode_cabang,
                    sm.chnl_code,
                    MAX(COALESCE(st.stock_on_hand_unit, 0)) AS stock_on_hand_unit,
                    " . implode(", ", array_map(function ($alias, $data) {
                    return "MAX(CASE WHEN (sm.bulan = {$data['month']} AND sm.tahun = {$data['year']}) THEN sm.net_sales_unit ELSE 0 END) AS {$alias}";
                    }, array_keys($monthYearMap), $monthYearMap)) . ",
                    CASE
                        WHEN (
                            COALESCE(SUM(CASE WHEN sm.bulan BETWEEN 1 AND 12 THEN sm.net_sales_unit ELSE 0 END), 0)
                        ) = 0 THEN 0
                        WHEN (
                            COALESCE(SUM(CASE WHEN sm.bulan IN (1, 2, 3) THEN sm.net_sales_unit ELSE 0 END), 0)
                        ) = 0 THEN 0
                        ELSE (
                            ROUND(
                                SUM(CASE WHEN sm.bulan BETWEEN 1 AND 12 THEN sm.net_sales_unit ELSE 0 END) / 
                                NULLIF(
                                    -- Jumlah bulan non-nol (1 hingga 12)
                                    (SUM(CASE WHEN sm.bulan = 1 AND sm.net_sales_unit > 0 THEN 1 ELSE 0 END) +
                                    SUM(CASE WHEN sm.bulan = 2 AND sm.net_sales_unit > 0 THEN 1 ELSE 0 END) +
                                    SUM(CASE WHEN sm.bulan = 3 AND sm.net_sales_unit > 0 THEN 1 ELSE 0 END) +
                                    SUM(CASE WHEN sm.bulan = 4 AND sm.net_sales_unit > 0 THEN 1 ELSE 0 END) +
                                    SUM(CASE WHEN sm.bulan = 5 AND sm.net_sales_unit > 0 THEN 1 ELSE 0 END) +
                                    SUM(CASE WHEN sm.bulan = 6 AND sm.net_sales_unit > 0 THEN 1 ELSE 0 END) +
                                    SUM(CASE WHEN sm.bulan = 7 AND sm.net_sales_unit > 0 THEN 1 ELSE 0 END) +
                                    SUM(CASE WHEN sm.bulan = 8 AND sm.net_sales_unit > 0 THEN 1 ELSE 0 END) +
                                    SUM(CASE WHEN sm.bulan = 9 AND sm.net_sales_unit > 0 THEN 1 ELSE 0 END) +
                                    SUM(CASE WHEN sm.bulan = 10 AND sm.net_sales_unit > 0 THEN 1 ELSE 0 END) +
                                    SUM(CASE WHEN sm.bulan = 11 AND sm.net_sales_unit > 0 THEN 1 ELSE 0 END) +
                                    SUM(CASE WHEN sm.bulan = 12 AND sm.net_sales_unit > 0 THEN 1 ELSE 0 END)
                                    ), 0
                                )
                            )::INTEGER
                        )
                    END AS yearly_average_unit,

                    -- Average 9-Month Unit (April to December)
                    CASE
                        WHEN (
                            COALESCE(SUM(CASE WHEN sm.bulan BETWEEN 4 AND 12 THEN sm.net_sales_unit ELSE 0 END), 0)
                        ) = 0 THEN 0
                        ELSE (
                            ROUND(
                                SUM(CASE WHEN sm.bulan BETWEEN 4 AND 12 THEN sm.net_sales_unit ELSE 0 END) / 
                                NULLIF(
                                    -- Jumlah bulan non-nol (April hingga Desember)
                                    (SUM(CASE WHEN sm.bulan = 4 AND sm.net_sales_unit > 0 THEN 1 ELSE 0 END) +
                                    SUM(CASE WHEN sm.bulan = 5 AND sm.net_sales_unit > 0 THEN 1 ELSE 0 END) +
                                    SUM(CASE WHEN sm.bulan = 6 AND sm.net_sales_unit > 0 THEN 1 ELSE 0 END) +
                                    SUM(CASE WHEN sm.bulan = 7 AND sm.net_sales_unit > 0 THEN 1 ELSE 0 END) +
                                    SUM(CASE WHEN sm.bulan = 8 AND sm.net_sales_unit > 0 THEN 1 ELSE 0 END) +
                                    SUM(CASE WHEN sm.bulan = 9 AND sm.net_sales_unit > 0 THEN 1 ELSE 0 END) +
                                    SUM(CASE WHEN sm.bulan = 10 AND sm.net_sales_unit > 0 THEN 1 ELSE 0 END) +
                                    SUM(CASE WHEN sm.bulan = 11 AND sm.net_sales_unit > 0 THEN 1 ELSE 0 END) +
                                    SUM(CASE WHEN sm.bulan = 12 AND sm.net_sales_unit > 0 THEN 1 ELSE 0 END)
                                    ), 0
                                )
                            )::INTEGER
                        )
                    END AS average_9_month_unit,

                    -- Average 6-Month Unit (July to December)
                    CASE
                        WHEN (
                            COALESCE(SUM(CASE WHEN sm.bulan BETWEEN 7 AND 12 THEN sm.net_sales_unit ELSE 0 END), 0)
                        ) = 0 THEN 0
                        ELSE (
                            ROUND(
                                SUM(CASE WHEN sm.bulan BETWEEN 7 AND 12 THEN sm.net_sales_unit ELSE 0 END) / 
                                NULLIF(
                                    -- Jumlah bulan non-nol (Juli hingga Desember)
                                    (SUM(CASE WHEN sm.bulan = 7 AND sm.net_sales_unit > 0 THEN 1 ELSE 0 END) +
                                    SUM(CASE WHEN sm.bulan = 8 AND sm.net_sales_unit > 0 THEN 1 ELSE 0 END) +
                                    SUM(CASE WHEN sm.bulan = 9 AND sm.net_sales_unit > 0 THEN 1 ELSE 0 END) +
                                    SUM(CASE WHEN sm.bulan = 10 AND sm.net_sales_unit > 0 THEN 1 ELSE 0 END) +
                                    SUM(CASE WHEN sm.bulan = 11 AND sm.net_sales_unit > 0 THEN 1 ELSE 0 END) +
                                    SUM(CASE WHEN sm.bulan = 12 AND sm.net_sales_unit > 0 THEN 1 ELSE 0 END)
                                    ), 0
                                )
                            )::INTEGER
                        )
                    END AS average_6_month_unit,

                    CASE
                        WHEN (
                            COALESCE(SUM(CASE WHEN sm.bulan BETWEEN 10 AND 12 THEN sm.net_sales_unit ELSE 0 END), 0)
                        ) = 0 THEN 0
                        ELSE (
                            ROUND(
                                SUM(CASE WHEN sm.bulan BETWEEN 10 AND 12 THEN sm.net_sales_unit ELSE 0 END) / 
                                NULLIF(
                                    -- Jumlah bulan non-nol (Oktober hingga Desember)
                                    (SUM(CASE WHEN sm.bulan = 10 AND sm.net_sales_unit > 0 THEN 1 ELSE 0 END) +
                                    SUM(CASE WHEN sm.bulan = 11 AND sm.net_sales_unit > 0 THEN 1 ELSE 0 END) +
                                    SUM(CASE WHEN sm.bulan = 12 AND sm.net_sales_unit > 0 THEN 1 ELSE 0 END)
                                    ), 0
                                )
                            )::INTEGER
                        )
                    END AS average_3_month_unit
                ")
                ->groupByRaw("
                    sm.tahun,
                    sm.item_code,
                    sm.dist_code,
                    sm.kode_cabang,
                    sm.chnl_code
                ");

            $count = DB::table(DB::raw("({$shiftedSales->toSql()}) as sa"))
                ->mergeBindings($shiftedSales)
                ->leftJoinSub($poData, 'po', function ($join) {
                    $join->on('sa.item_code', '=', 'po.item_code')
                        ->on('sa.dist_code', '=', 'po.dist_code')
                        ->on('sa.kode_cabang', '=', 'po.kode_cabang');
                })
                ->leftJoinSub($aggregatedStock, 'st', function ($join) {
                    $join->on('sa.item_code', '=', 'st.item_code')
                        ->on('sa.dist_code', '=', 'st.dist_code')
                        ->on('sa.kode_cabang', '=', 'st.kode_cabang');
                })
                ->leftJoin('m__products as mp', 'sa.item_code', '=', 'mp.item_code')
                ->leftJoin('m__kategoris as mk', 'mp.parent_code', '=', 'mk.parent_code')
                ->leftJoin('m__cabangs as mc', 'sa.kode_cabang', '=', 'mc.kode_cabang')
                ->selectRaw("
                DISTINCT sa.tahun,
                sa.item_code,
                sa.dist_code,
                sa.kode_cabang,
                sa.chnl_code,
                mp.brand_name,
                mp.status_product,
                mk.kategori,
                mp.parent_code,
                mp.item_name,
                mc.region_name,
                mc.area_name,
                mc.nama_cabang,
                sa.month_1,
                sa.month_2,
                sa.month_3,
                sa.month_4,
                sa.month_5,
                sa.month_6,
                sa.month_7,
                sa.month_8,
                sa.month_9,
                sa.month_10,
                sa.month_11,
                sa.month_12,
                CASE 
                WHEN po.qty_po = 0 OR po.qty_po IS NULL THEN '0%'
                ELSE CONCAT(ROUND((po.qty_sc_reg::NUMERIC / NULLIF(po.qty_po, 0)) * 100), '%')
                END AS service_level,
                CASE
                WHEN (
                    COALESCE(sa.month_1, 0) + COALESCE(sa.month_2, 0) + COALESCE(sa.month_3, 0) + sa.month_4 + sa.month_5 + sa.month_6 +
                    sa.month_7 + sa.month_8 + sa.month_9 + sa.month_10 + sa.month_11 + sa.month_12
                ) = 0 THEN 0
                WHEN (COALESCE(sa.month_1, 0) + COALESCE(sa.month_2, 0) + COALESCE(sa.month_3, 0)) = 0 THEN 0
                ELSE (
                    ROUND((
                        COALESCE(sa.month_1, 0) + COALESCE(sa.month_2, 0) + COALESCE(sa.month_3, 0) + sa.month_4 + sa.month_5 + sa.month_6 +
                        sa.month_7 + sa.month_8 + sa.month_9 + sa.month_10 + sa.month_11 + sa.month_12
                    ) / NULLIF(
                        -- Hitung jumlah bulan yang memiliki nilai > 0
                        (CASE WHEN sa.month_1 > 0 THEN 1 ELSE 0 END +
                        CASE WHEN sa.month_2 > 0 THEN 1 ELSE 0 END +
                        CASE WHEN sa.month_3 > 0 THEN 1 ELSE 0 END +
                        CASE WHEN sa.month_4 > 0 THEN 1 ELSE 0 END +
                        CASE WHEN sa.month_5 > 0 THEN 1 ELSE 0 END +
                        CASE WHEN sa.month_6 > 0 THEN 1 ELSE 0 END +
                        CASE WHEN sa.month_7 > 0 THEN 1 ELSE 0 END +
                        CASE WHEN sa.month_8 > 0 THEN 1 ELSE 0 END +
                        CASE WHEN sa.month_9 > 0 THEN 1 ELSE 0 END +
                        CASE WHEN sa.month_10 > 0 THEN 1 ELSE 0 END +
                        CASE WHEN sa.month_11 > 0 THEN 1 ELSE 0 END +
                        CASE WHEN sa.month_12 > 0 THEN 1 ELSE 0 END
                        ), 0)
                    )::INTEGER
                )
                END AS yearly_average_unit,
                CASE
                    WHEN (
                        COALESCE(sa.month_1, 0) + COALESCE(sa.month_2, 0) + COALESCE(sa.month_3, 0) + sa.month_4 + sa.month_5 + sa.month_6 +
                        sa.month_7 + sa.month_8 + sa.month_9 + sa.month_10 + sa.month_11 + sa.month_12
                    ) = 0 THEN 0
                    WHEN (COALESCE(sa.month_1, 0) + COALESCE(sa.month_2, 0) + COALESCE(sa.month_3, 0)) = 0 THEN 0
                    ELSE (
                        ROUND(
                            (
                                COALESCE(sa.month_1, 0) + COALESCE(sa.month_2, 0) + COALESCE(sa.month_3, 0) + sa.month_4 + sa.month_5 + sa.month_6 +
                                sa.month_7 + sa.month_8 + sa.month_9 + sa.month_10 + sa.month_11 + sa.month_12
                            ) / NULLIF(
                                (CASE WHEN sa.month_1 > 0 THEN 1 ELSE 0 END +
                                CASE WHEN sa.month_2 > 0 THEN 1 ELSE 0 END +
                                CASE WHEN sa.month_3 > 0 THEN 1 ELSE 0 END +
                                CASE WHEN sa.month_4 > 0 THEN 1 ELSE 0 END +
                                CASE WHEN sa.month_5 > 0 THEN 1 ELSE 0 END +
                                CASE WHEN sa.month_6 > 0 THEN 1 ELSE 0 END +
                                CASE WHEN sa.month_7 > 0 THEN 1 ELSE 0 END +
                                CASE WHEN sa.month_8 > 0 THEN 1 ELSE 0 END +
                                CASE WHEN sa.month_9 > 0 THEN 1 ELSE 0 END +
                                CASE WHEN sa.month_10 > 0 THEN 1 ELSE 0 END +
                                CASE WHEN sa.month_11 > 0 THEN 1 ELSE 0 END +
                                CASE WHEN sa.month_12 > 0 THEN 1 ELSE 0 END
                                ), 0
                            )
                        ) * 
                        NULLIF(REPLACE(mp.price, ',', ''), '')::NUMERIC
                    )
                END AS yearly_average_value,
                    CASE
                    WHEN (
                        COALESCE(sa.month_4, 0) + COALESCE(sa.month_5, 0) + COALESCE(sa.month_6, 0) + sa.month_7 + sa.month_8 + sa.month_9 +
                        sa.month_10 + sa.month_11 + sa.month_12
                    ) = 0 THEN 0
                    WHEN (COALESCE(sa.month_4, 0) + COALESCE(sa.month_5, 0) + COALESCE(sa.month_6, 0)) = 0 THEN 0
                    ELSE (
                        ROUND((
                            COALESCE(sa.month_4, 0) + COALESCE(sa.month_5, 0) + COALESCE(sa.month_6, 0) + sa.month_7 + sa.month_8 + sa.month_9 +
                            sa.month_10 + sa.month_11 + sa.month_12
                        ) / NULLIF(
                            -- Hitung jumlah bulan yang memiliki nilai > 0
                            (CASE WHEN sa.month_4 > 0 THEN 1 ELSE 0 END +
                            CASE WHEN sa.month_5 > 0 THEN 1 ELSE 0 END +
                            CASE WHEN sa.month_6 > 0 THEN 1 ELSE 0 END +
                            CASE WHEN sa.month_7 > 0 THEN 1 ELSE 0 END +
                            CASE WHEN sa.month_8 > 0 THEN 1 ELSE 0 END +
                            CASE WHEN sa.month_9 > 0 THEN 1 ELSE 0 END +
                            CASE WHEN sa.month_10 > 0 THEN 1 ELSE 0 END +
                            CASE WHEN sa.month_11 > 0 THEN 1 ELSE 0 END +
                            CASE WHEN sa.month_12 > 0 THEN 1 ELSE 0 END
                            ), 0)
                        )::INTEGER
                    )
                    END AS average_9_month_unit,
                    CASE
                    WHEN (
                        COALESCE(sa.month_4, 0) + COALESCE(sa.month_5, 0) + COALESCE(sa.month_6, 0) + sa.month_7 + sa.month_8 + sa.month_9 +
                        sa.month_10 + sa.month_11 + sa.month_12
                    ) = 0 THEN 0
                    WHEN (COALESCE(sa.month_4, 0) + COALESCE(sa.month_5, 0) + COALESCE(sa.month_6, 0)) = 0 THEN 0
                    ELSE (
                        ROUND(
                            (
                                (COALESCE(sa.month_4, 0) + COALESCE(sa.month_5, 0) + COALESCE(sa.month_6, 0) + sa.month_7 + sa.month_8 + sa.month_9 +
                                sa.month_10 + sa.month_11 + sa.month_12
                                ) / NULLIF(
                                    -- Hitung jumlah bulan dengan nilai > 0
                                    (CASE WHEN sa.month_4 > 0 THEN 1 ELSE 0 END +
                                    CASE WHEN sa.month_5 > 0 THEN 1 ELSE 0 END +
                                    CASE WHEN sa.month_6 > 0 THEN 1 ELSE 0 END +
                                    CASE WHEN sa.month_7 > 0 THEN 1 ELSE 0 END +
                                    CASE WHEN sa.month_8 > 0 THEN 1 ELSE 0 END +
                                    CASE WHEN sa.month_9 > 0 THEN 1 ELSE 0 END +
                                    CASE WHEN sa.month_10 > 0 THEN 1 ELSE 0 END +
                                    CASE WHEN sa.month_11 > 0 THEN 1 ELSE 0 END +
                                    CASE WHEN sa.month_12 > 0 THEN 1 ELSE 0 END
                                    ), 0
                                )
                            ) * 
                            NULLIF(REPLACE(mp.price, ',', ''), '')::NUMERIC
                        )::NUMERIC
                    )
                    END AS average_9_month_value,
                    CASE
                    WHEN (
                        COALESCE(sa.month_7, 0) + COALESCE(sa.month_8, 0) + COALESCE(sa.month_9, 0) + sa.month_10 + sa.month_11 + sa.month_12
                    ) = 0 THEN 0
                    WHEN (COALESCE(sa.month_7, 0) + COALESCE(sa.month_8, 0) + COALESCE(sa.month_9, 0)) = 0 THEN 0
                    ELSE (
                        ROUND((
                            COALESCE(sa.month_7, 0) + COALESCE(sa.month_8, 0) + COALESCE(sa.month_9, 0) + sa.month_10 + sa.month_11 + sa.month_12
                        ) / NULLIF(
                            (CASE WHEN sa.month_7 > 0 THEN 1 ELSE 0 END +
                            CASE WHEN sa.month_8 > 0 THEN 1 ELSE 0 END +
                            CASE WHEN sa.month_9 > 0 THEN 1 ELSE 0 END +
                            CASE WHEN sa.month_10 > 0 THEN 1 ELSE 0 END +
                            CASE WHEN sa.month_11 > 0 THEN 1 ELSE 0 END +
                            CASE WHEN sa.month_12 > 0 THEN 1 ELSE 0 END
                            ), 0)
                        )::INTEGER
                    )
                    END AS average_6_month_unit,
                    CASE
                    WHEN (
                        COALESCE(sa.month_7, 0) + COALESCE(sa.month_8, 0) + COALESCE(sa.month_9, 0) + sa.month_10 + sa.month_11 + sa.month_12
                    ) = 0 THEN 0
                    WHEN (COALESCE(sa.month_7, 0) + COALESCE(sa.month_8, 0) + COALESCE(sa.month_9, 0)) = 0 THEN 0
                    ELSE (
                        ROUND(
                            (
                                COALESCE(sa.month_7, 0) + COALESCE(sa.month_8, 0) + COALESCE(sa.month_9, 0) + sa.month_10 + sa.month_11 + sa.month_12
                            ) / NULLIF(
                                (CASE WHEN sa.month_7 > 0 THEN 1 ELSE 0 END +
                                CASE WHEN sa.month_8 > 0 THEN 1 ELSE 0 END +
                                CASE WHEN sa.month_9 > 0 THEN 1 ELSE 0 END +
                                CASE WHEN sa.month_10 > 0 THEN 1 ELSE 0 END +
                                CASE WHEN sa.month_11 > 0 THEN 1 ELSE 0 END +
                                CASE WHEN sa.month_12 > 0 THEN 1 ELSE 0 END
                                ), 0
                            ) *
                            -- Harga produk dari master product
                            NULLIF(REPLACE(mp.price, ',', ''), '')::NUMERIC
                        )::NUMERIC
                    )
                    END AS average_6_month_value,
                    CASE 
                    WHEN (COALESCE(sa.month_10, 0) + sa.month_11 + sa.month_12) = 0 THEN 0
                    ELSE ROUND((
                        COALESCE(sa.month_10, 0) + sa.month_11 + sa.month_12
                    ) / NULLIF(
                        (CASE WHEN sa.month_10 > 0 THEN 1 ELSE 0 END +
                        CASE WHEN sa.month_11 > 0 THEN 1 ELSE 0 END +
                        CASE WHEN sa.month_12 > 0 THEN 1 ELSE 0 END
                        ), 0
                    )
                    )::INTEGER
                    END AS average_3_month_unit,
                    CASE 
                        WHEN (COALESCE(sa.month_10, 0) + sa.month_11 + sa.month_12) = 0 THEN 0
                        ELSE ROUND(
                            (COALESCE(sa.month_10, 0) + sa.month_11 + sa.month_12) / 
                            NULLIF(
                                (CASE WHEN sa.month_10 > 0 THEN 1 ELSE 0 END +
                                CASE WHEN sa.month_11 > 0 THEN 1 ELSE 0 END +
                                CASE WHEN sa.month_12 > 0 THEN 1 ELSE 0 END
                                ), 0
                            )
                            * NULLIF(REPLACE(mp.price, ',', ''), '')::NUMERIC
                        )::NUMERIC
                    END AS average_3_month_value,
                ROUND((COALESCE(sa.month_1, 0) + sa.month_2 + sa.month_3 + sa.month_4 + sa.month_5 + sa.month_6 + 
                sa.month_7 + sa.month_8 + sa.month_9 + sa.month_10 + sa.month_11 + sa.month_12) / 12.0)::INTEGER AS average_sales,
                (ROUND((COALESCE(sa.month_1, 0) + sa.month_2 + sa.month_3 + sa.month_4 + sa.month_5 + sa.month_6 + 
                        sa.month_7 + sa.month_8 + sa.month_9 + sa.month_10 + sa.month_11 + sa.month_12) / 12.0)::INTEGER - 
                sa.stock_on_hand_unit) AS purchase_suggestion,

                ((ROUND((COALESCE(sa.month_1, 0) + sa.month_2 + sa.month_3 + sa.month_4 + sa.month_5 + sa.month_6 + 
                        sa.month_7 + sa.month_8 + sa.month_9 + sa.month_10 + sa.month_11 + sa.month_12) / 12.0)::INTEGER - 
                sa.stock_on_hand_unit) * NULLIF(REPLACE(mp.price, ',', ''), '')::NUMERIC) AS purchase_value,
                COALESCE(st.stock_on_hand_unit, 0) AS stock_on_hand_unit,
                COALESCE(po.qty_po, 0) AS qty_po,
                COALESCE(po.qty_sc_reg, 0) AS qty_sc_reg,
                CASE 
                WHEN COALESCE(sa.stock_on_hand_unit, 0) = 0 THEN 0
                ELSE ROUND(
                    COALESCE(sa.stock_on_hand_unit, 0) / 
                    NULLIF(
                        (CASE 
                            WHEN (COALESCE(sa.month_10, 0) + sa.month_11 + sa.month_12) = 0 
                            THEN 1 
                            ELSE ROUND((
                                COALESCE(sa.month_10, 0) + sa.month_11 + sa.month_12
                            ) / NULLIF(
                                (CASE WHEN sa.month_10 > 0 THEN 1 ELSE 0 END +
                                CASE WHEN sa.month_11 > 0 THEN 1 ELSE 0 END +
                                CASE WHEN sa.month_12 > 0 THEN 1 ELSE 0 END
                                ), 0
                            ), 0)
                        END) * 30, 1
                    ))
                END AS doi_3_month,
                CASE
                    WHEN yearly_average_unit < 0 AND average_9_month_unit < 0 
                        AND average_6_month_unit < 0 AND average_3_month_unit < 0 
                        THEN 'TREND TAK BERATURAN'
                    WHEN yearly_average_unit > average_9_month_unit 
                        AND average_9_month_unit < average_6_month_unit 
                        AND average_6_month_unit < average_3_month_unit 
                        THEN 'TREND NAIK'
                    WHEN yearly_average_unit < average_9_month_unit 
                        AND average_9_month_unit < average_6_month_unit 
                        AND average_6_month_unit < average_3_month_unit 
                        THEN 'TREND PROGRESIF NAIK'
                    WHEN yearly_average_unit > average_9_month_unit 
                        AND average_9_month_unit > average_6_month_unit 
                        AND average_6_month_unit > average_3_month_unit 
                        THEN 'TREND PROGRESIF TURUN'
                    WHEN yearly_average_unit < average_9_month_unit 
                        AND average_9_month_unit > average_6_month_unit 
                        AND average_6_month_unit > average_3_month_unit 
                        THEN 'TREND TURUN'
                    ELSE 'TREND TAK BERATURAN'
                END AS status_trend,
                CASE
                WHEN yearly_average_unit = 0 THEN '0%'
                ELSE CONCAT(ROUND(((average_3_month_unit - yearly_average_unit) / yearly_average_unit) * 100/100)::TEXT, '%')
                END AS delta
        ")
                ->whereRaw("(sa.dist_code LIKE '%$distCode%'
                AND sa.kode_cabang LIKE '%$branch%'
                AND mc.region_name LIKE '%$regionName%'
                AND sa.chnl_code LIKE '%$chnlCode%'
                AND mp.brand_name LIKE '%$brandName%'
                AND mp.status_product LIKE '%$statusProduct%')")
                
                ->when($search, function ($query) use ($search) {
                    return $query->where(function ($subQuery) use ($search) {
                        $subQuery->where('sa.item_code', 'ILIKE', "%{$search}%")
                            ->orWhere('sa.chnl_code', 'ILIKE', "%{$search}%")
                            ->orWhere('sa.dist_code', 'ILIKE', "%{$search}%")
                            ->orWhere('mc.region_name', 'ILIKE', "%{$search}%")
                            ->orWhere('mc.area_name', 'ILIKE', "%{$search}%")
                            ->orWhere('mk.kategori', 'ILIKE', "%{$search}%")
                            ->orWhere('mp.parent_code', 'ILIKE', "%{$search}%")
                            ->orWhere('mp.item_name', 'ILIKE', "%{$search}%")
                            ->orWhere('mp.status_product', 'ILIKE', "%{$search}%")
                            ->orWhere('mc.nama_cabang', 'ILIKE', "%{$search}%");
                    });
                });

            return $count->get();
        } catch (\Exception $e) {
            Log::error('Error in countTrendAnalysis:', ['message' => $e->getMessage()]);
            throw $e;
        }
    }
}
