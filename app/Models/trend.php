<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\support\Facades\DB;
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
    public function get_data_($search, $arr_pagination)
    {
        // Jika ada pencarian, reset offset pagination ke 0
        if (!empty($search)) {
            $arr_pagination['offset'] = 0;
        }

        $search = strtolower($search);

        // Query dengan pencarian dan paginasi
        $data = trend::join('m__regions', 'trends.region_name', '=', 'm__regions.region_name') // Join berdasarkan region_name
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
            )->whereRaw("
            (lower(m__customers.chnl_code) like '%$search%'
            OR lower(m__customers.dist_code) like '%$search%'
            OR lower(m__regions.region_name) like '%$search%'
            OR lower(m__areas.area_name) like '%$search%'
            OR lower(m__cabangs.nama_cabang) like '%$search%'
            OR lower(m__products.item_code) like '%$search%'
            OR lower(m__products.item_name) like '%$search%'
            OR lower(m__products.brand_name) like '%$search%'
            OR lower(m__kategoris.kategori) like '%$search%'
            OR lower(m__products.status_product) like '%$search%'
            OR lower(tahun) like '%$search%'
            OR lower(januari) like '%$search%'
            OR lower(februari) like '%$search%'
            OR lower(maret) like '%$search%'
            OR lower(april) like '%$search%'
            OR lower(mei) like '%$search%'
            OR lower(juni) like '%$search%'
            OR lower(juli) like '%$search%'
            OR lower(agustus) like '%$search%'
            OR lower(september) like '%$search%'
            OR lower(oktober) like '%$search%'
            OR lower(november) like '%$search%'
            OR lower(desember) like '%$search%'
            OR lower(unit12) like '%$search%'
            OR lower(value12) like '%$search%'
            OR lower(unit9) like '%$search%'
            OR lower(value9) like '%$search%'
            OR lower(unit6) like '%$search%'
            OR lower(value6) like '%$search%'
            OR lower(unit3) like '%$search%'
            OR lower(value3) like '%$search%'
            OR lower(beli_januariuari) like '%$search%'
            OR lower(januari1) like '%$search%'
            OR lower(beli_februariruari) like '%$search%'
            OR lower(februari1) like '%$search%'
            OR lower(beli_maretet) like '%$search%'
            OR lower(maret1) like '%$search%'
            OR lower(beli_aprilil) like '%$search%'
            OR lower(april1) like '%$search%'
            OR lower(beli_mei) like '%$search%'
            OR lower(mei1) like '%$search%'
            OR lower(beli_junii) like '%$search%'
            OR lower(juni1) like '%$search%'
            OR lower(beli_julii) like '%$search%'
            OR lower(juli1) like '%$search%'
            OR lower(beli_agustus) like '%$search%'
            OR lower(agustus1) like '%$search%'
            OR lower(beli_septembertember) like '%$search%'
            OR lower(september1) like '%$search%'
            OR lower(beli_oktober) like '%$search%'
            OR lower(oktober1) like '%$search%'
            OR lower(beli_novemberember) like '%$search%'
            OR lower(november1) like '%$search%'
            OR lower(beli_desember) like '%$search%'
            OR lower(desember1) like '%$search%'
            OR lower(doi3bulan) like '%$search%'
            OR lower(status_trend) like '%$search%'
            OR lower(delta) like '%$search%'
            OR lower(pic) like '%$search%'
            OR lower(average_sales) like '%$search%'
            OR lower(purchase_suggestion) like '%$search%'
            OR lower(purchase_value) like '%$search%' )
            AND trends.deleted_by IS NULL 
        ")
            ->select(
                'trends.id',
                'm__customers.dist_code',
                'm__customers.chnl_code',
                'm__regions.region_name',
                'm__areas.area_name',
                'm__cabangs.nama_cabang',
                'm__products.item_code',
                'm__products.item_name',
                'm__products.brand_name',
                'm__kategoris.kategori',
                'm__products.status_product',
                'tahun',
                'januari',
                'februari',
                'maret',
                'april',
                'mei',
                'juni',
                'juli',
                'agustus',
                'september',
                'oktober',
                'november',
                'desember',
                'unit12',
                'value12',
                'unit9',
                'value9',
                'unit6',
                'value6',
                'unit3',
                'value3',
                'beli_januariuari',
                'januari1',
                'beli_februariruari',
                'februari1',
                'beli_maretet',
                'maret1',
                'beli_aprilil',
                'april1',
                'beli_mei',
                'mei1',
                'beli_junii',
                'juni1',
                'beli_julii',
                'juli1',
                'beli_agustus',
                'agustus1',
                'beli_septembertember',
                'september1',
                'beli_oktober',
                'oktober1',
                'beli_novemberember',
                'november1',
                'beli_desember',
                'desember1',
                'doi3bulan',
                'status_trend',
                'delta',
                'pic',
                'average_sales',
                'purchase_suggestion',
                'purchase_value',
            ) // Join berdasarkan cabang_code
            ->offset($arr_pagination['offset'])
            ->limit($arr_pagination['limit'])
            ->orderBy('id', 'ASC');


        return $data;
    }

    public function count_data_($search)
    {
        $search = strtolower($search);

        // Query dengan pencarian dan paginasi
        $data = trend::join('m__regions', 'trends.region_name', '=', 'm__regions.region_name') // Join berdasarkan region_name
            ->join('m__areas', 'm__regions.region_name', '=', 'm__areas.region_name')         // Join berdasarkan area_name
            ->join('m__cabangs', 'm__areas.area_name', '=', 'm__cabangs.nama_cabang') // Join berdasarkan cabang_code
            ->join('m__customers', 'trends.dist_code', '=', 'm__customers.dist_code') // Join berdasarkan cabang_code
            ->orderBy('id', 'ASC')
            ->count();

        return $data;
    }

    public function getMonthlySalesData($search, $request, $arr_pagination, $selected_month, $selected_year)
    {
        // Jika ada pencarian, reset offset pagination ke 0
        if (!empty($search)) {
            $arr_pagination['offset'] = 0;
        }

        $search = strtolower($search);

        return DB::select("
   WITH aggregated_sales AS (
    SELECT 
        CAST(s.tahun AS INTEGER) AS tahun,
        s.item_code,
        s.dist_code,
        s.kode_cabang,
        s.chnl_code,
        CAST(CASE WHEN s.bulan ~ '^[0-9]+$' THEN s.bulan ELSE NULL END AS INTEGER) AS bulan,
        SUM(CASE WHEN s.net_sales_unit ~ '^[0-9,]+$' THEN NULLIF(REPLACE(s.net_sales_unit, ',', ''), '')::NUMERIC ELSE 0 END)::INTEGER AS net_sales_unit
    FROM sales__units s
    WHERE s.bulan ~ '^[0-9]+$'
    GROUP BY CAST(s.tahun AS INTEGER), s.item_code, s.dist_code, s.kode_cabang, s.chnl_code, CAST(CASE WHEN s.bulan ~ '^[0-9]+$' THEN s.bulan ELSE NULL END AS INTEGER)
),
aggregated_stock AS (
    SELECT 
        CAST(st.tahun AS INTEGER) AS tahun,
        st.item_code,
        st.dist_code,
        st.kode_cabang,
        CAST(CASE WHEN st.bulan ~ '^[0-9]+$' THEN st.bulan ELSE NULL END AS INTEGER) AS bulan,
        SUM(CASE WHEN st.on_hand_unit ~ '^[0-9,]+$' THEN NULLIF(REPLACE(st.on_hand_unit, ',', ''), '')::NUMERIC ELSE 0 END)::INTEGER AS on_hand_unit
    FROM stock__details st
    WHERE st.bulan ~ '^[0-9]+$'
    GROUP BY CAST(st.tahun AS INTEGER), st.item_code, st.dist_code, st.kode_cabang, CAST(CASE WHEN st.bulan ~ '^[0-9]+$' THEN st.bulan ELSE NULL END AS INTEGER)
),
aggregated_po AS (
    SELECT 
        po.dist_code,
        po.mtg_code AS item_code,  
        po.branch_code AS kode_cabang,
        EXTRACT(YEAR FROM TO_DATE(po.tgl_order, 'MM/DD/YYYY'))::INTEGER AS po_year,
        EXTRACT(MONTH FROM TO_DATE(po.tgl_order, 'MM/DD/YYYY'))::INTEGER AS po_month,
        SUM(CASE WHEN po.qty_po ~ '^[0-9,]+$' THEN NULLIF(REPLACE(po.qty_po, ',', ''), '')::NUMERIC ELSE 0 END)::INTEGER AS qty_po,
        SUM(CASE WHEN po.qty_sc_reg ~ '^[0-9,]+$' THEN NULLIF(REPLACE(po.qty_sc_reg, ',', ''), '')::NUMERIC ELSE 0 END)::INTEGER AS qty_sc_reg
    FROM p_o_custs po
    WHERE po.qty_po ~ '^[0-9,]+$'
      AND EXTRACT(MONTH FROM TO_DATE(po.tgl_order, 'MM/DD/YYYY')) = :selected_month
    GROUP BY po.dist_code, po.mtg_code, po.branch_code, EXTRACT(YEAR FROM TO_DATE(po.tgl_order, 'MM/DD/YYYY')), EXTRACT(MONTH FROM TO_DATE(po.tgl_order, 'MM/DD/YYYY'))
),
shifted_sales AS (
    SELECT
        s.tahun,
        s.item_code,
        s.dist_code,
        s.kode_cabang,
        s.chnl_code,
        COALESCE(MAX(CASE 
            WHEN (s.tahun = CAST(:selected_year AS INTEGER) AND s.bulan = CAST(:selected_month AS INTEGER) - 11) OR
                 (s.tahun = CAST(:selected_year AS INTEGER) - 1 AND s.bulan = CAST(:selected_month AS INTEGER) + 1)
            THEN s.net_sales_unit END), 0) AS month_1,
        COALESCE(MAX(CASE 
            WHEN (s.tahun = CAST(:selected_year AS INTEGER) AND s.bulan = CAST(:selected_month AS INTEGER) - 10) OR
                 (s.tahun = CAST(:selected_year AS INTEGER) - 1 AND s.bulan = CAST(:selected_month AS INTEGER) + 2)
            THEN s.net_sales_unit END), 0) AS month_2,
        COALESCE(MAX(CASE 
            WHEN (s.tahun = CAST(:selected_year AS INTEGER) AND s.bulan = CAST(:selected_month AS INTEGER) - 9) OR
                 (s.tahun = CAST(:selected_year AS INTEGER) - 1 AND s.bulan = CAST(:selected_month AS INTEGER) + 3)
            THEN s.net_sales_unit END), 0) AS month_3,
        COALESCE(MAX(CASE 
            WHEN (s.tahun = CAST(:selected_year AS INTEGER) AND s.bulan = CAST(:selected_month AS INTEGER) - 8) OR
                 (s.tahun = CAST(:selected_year AS INTEGER) - 1 AND s.bulan = CAST(:selected_month AS INTEGER) + 4)
            THEN s.net_sales_unit END), 0) AS month_4,
        COALESCE(MAX(CASE 
            WHEN (s.tahun = CAST(:selected_year AS INTEGER) AND s.bulan = CAST(:selected_month AS INTEGER) - 7) OR
                 (s.tahun = CAST(:selected_year AS INTEGER) - 1 AND s.bulan = CAST(:selected_month AS INTEGER) + 5)
            THEN s.net_sales_unit END), 0) AS month_5,
        COALESCE(MAX(CASE 
            WHEN (s.tahun = CAST(:selected_year AS INTEGER) AND s.bulan = CAST(:selected_month AS INTEGER) - 6) OR
                 (s.tahun = CAST(:selected_year AS INTEGER) - 1 AND s.bulan = CAST(:selected_month AS INTEGER) + 6)
            THEN s.net_sales_unit END), 0) AS month_6,
        COALESCE(MAX(CASE 
            WHEN (s.tahun = CAST(:selected_year AS INTEGER) AND s.bulan = CAST(:selected_month AS INTEGER) - 5) OR
                 (s.tahun = CAST(:selected_year AS INTEGER) - 1 AND s.bulan = CAST(:selected_month AS INTEGER) + 7)
            THEN s.net_sales_unit END), 0) AS month_7,
        COALESCE(MAX(CASE 
            WHEN (s.tahun = CAST(:selected_year AS INTEGER) AND s.bulan = CAST(:selected_month AS INTEGER) - 4) OR
                 (s.tahun = CAST(:selected_year AS INTEGER) - 1 AND s.bulan = CAST(:selected_month AS INTEGER) + 8)
            THEN s.net_sales_unit END), 0) AS month_8,
        COALESCE(MAX(CASE 
            WHEN (s.tahun = CAST(:selected_year AS INTEGER) AND s.bulan = CAST(:selected_month AS INTEGER) - 3) OR
                 (s.tahun = CAST(:selected_year AS INTEGER) - 1 AND s.bulan = CAST(:selected_month AS INTEGER) + 9)
            THEN s.net_sales_unit END), 0) AS month_9,
        COALESCE(MAX(CASE 
            WHEN (s.tahun = CAST(:selected_year AS INTEGER) AND s.bulan = CAST(:selected_month AS INTEGER) - 2) OR
                 (s.tahun = CAST(:selected_year AS INTEGER) - 1 AND s.bulan = CAST(:selected_month AS INTEGER) + 10)
            THEN s.net_sales_unit END), 0) AS month_10,
        COALESCE(MAX(CASE 
            WHEN (s.tahun = CAST(:selected_year AS INTEGER) AND s.bulan = CAST(:selected_month AS INTEGER) - 1) OR
                 (s.tahun = CAST(:selected_year AS INTEGER) - 1 AND s.bulan = CAST(:selected_month AS INTEGER) + 11)
            THEN s.net_sales_unit END), 0) AS month_11,
        COALESCE(MAX(CASE 
            WHEN s.tahun = CAST(:selected_year AS INTEGER) AND s.bulan = CAST(:selected_month AS INTEGER)
            THEN s.net_sales_unit END), 0) AS month_12,
        COALESCE(MAX(CASE 
            WHEN s.tahun = CAST(:selected_year AS INTEGER) AND s.bulan = CAST(:selected_month AS INTEGER)
            THEN st.on_hand_unit END), 0) AS stock_on_hand_unit
    FROM aggregated_sales s
    LEFT JOIN aggregated_stock st 
        ON s.item_code = st.item_code 
        AND s.dist_code = st.dist_code 
        AND s.kode_cabang = st.kode_cabang
        AND s.tahun = st.tahun
        AND s.bulan = st.bulan
    GROUP BY 
        s.tahun, s.item_code, s.dist_code, s.kode_cabang, s.chnl_code
),
trend_calculation AS (
    SELECT 
        ss.*,
        po.qty_po,
        po.qty_sc_reg,
        uc.brand_code,
        uc.brand_name,
        uc.parent_code,
        uc.item_name,
        NULLIF(REPLACE(uc.price, ',', ''), '')::NUMERIC AS price,
        uc.status_product,
        mk.kategori,
        mcb.region_name,
        mcb.area_code,
        mcb.area_name,
        mcb.nama_cabang,
        ROUND((COALESCE(ss.month_1, 0) + COALESCE(ss.month_2, 0) + COALESCE(ss.month_3, 0) +
            COALESCE(ss.month_4, 0) + COALESCE(ss.month_5, 0) + COALESCE(ss.month_6, 0) +
            COALESCE(ss.month_7, 0) + COALESCE(ss.month_8, 0) + COALESCE(ss.month_9, 0) +
            COALESCE(ss.month_10, 0) + COALESCE(ss.month_11, 0) + COALESCE(ss.month_12, 0)) / 12)::INTEGER AS yearly_average_unit,
        ROUND((COALESCE(ss.month_1, 0) + COALESCE(ss.month_2, 0) + COALESCE(ss.month_3, 0) +
            COALESCE(ss.month_4, 0) + COALESCE(ss.month_5, 0) + COALESCE(ss.month_6, 0) +
            COALESCE(ss.month_7, 0) + COALESCE(ss.month_8, 0) + COALESCE(ss.month_9, 0) +
            COALESCE(ss.month_10, 0) + COALESCE(ss.month_11, 0) + COALESCE(ss.month_12, 0)) / 12)::INTEGER AS average_sales,
        ROUND(((COALESCE(ss.month_1, 0) + COALESCE(ss.month_2, 0) + COALESCE(ss.month_3, 0) +
            COALESCE(ss.month_4, 0) + COALESCE(ss.month_5, 0) + COALESCE(ss.month_6, 0) +
            COALESCE(ss.month_7, 0) + COALESCE(ss.month_8, 0) + COALESCE(ss.month_9, 0) +
            COALESCE(ss.month_10, 0) + COALESCE(ss.month_11, 0) + COALESCE(ss.month_12, 0)) / 12 * 
            NULLIF(REPLACE(uc.price, ',', ''), '')::NUMERIC))::INTEGER AS yearly_average_value,
        ROUND((COALESCE(ss.month_4, 0) + COALESCE(ss.month_5, 0) + COALESCE(ss.month_6, 0) +
            COALESCE(ss.month_7, 0) + COALESCE(ss.month_8, 0) + COALESCE(ss.month_9, 0) +
            COALESCE(ss.month_10, 0) + COALESCE(ss.month_11, 0) + COALESCE(ss.month_12, 0)) / 9)::INTEGER AS average_9_month_unit,
        ROUND(((COALESCE(ss.month_4, 0) + COALESCE(ss.month_5, 0) + COALESCE(ss.month_6, 0) +
            COALESCE(ss.month_7, 0) + COALESCE(ss.month_8, 0) + COALESCE(ss.month_9, 0) +
            COALESCE(ss.month_10, 0) + COALESCE(ss.month_11, 0) + COALESCE(ss.month_12, 0)) / 9 *
            NULLIF(REPLACE(uc.price, ',', ''), '')::NUMERIC))::INTEGER AS average_9_month_value,
        ROUND((COALESCE(ss.month_7, 0) + COALESCE(ss.month_8, 0) + COALESCE(ss.month_9, 0) +
            COALESCE(ss.month_10, 0) + COALESCE(ss.month_11, 0) + COALESCE(ss.month_12, 0)) / 6)::INTEGER AS average_6_month_unit,
        ROUND(((COALESCE(ss.month_7, 0) + COALESCE(ss.month_8, 0) + COALESCE(ss.month_9, 0) +
            COALESCE(ss.month_10, 0) + COALESCE(ss.month_11, 0) + COALESCE(ss.month_12, 0)) / 6 *
            NULLIF(REPLACE(uc.price, ',', ''), '')::NUMERIC))::INTEGER AS average_6_month_value,
        ROUND((COALESCE(ss.month_10, 0) + COALESCE(ss.month_11, 0) + COALESCE(ss.month_12, 0)) / 3)::INTEGER AS average_3_month_unit,
        ROUND(((COALESCE(ss.month_10, 0) + COALESCE(ss.month_11, 0) + COALESCE(ss.month_12, 0)) / 3 *
            NULLIF(REPLACE(uc.price, ',', ''), '')::NUMERIC))::INTEGER AS average_3_month_value,
        ROUND((po.qty_sc_reg::NUMERIC / NULLIF(po.qty_po, 0)) * 100, 2)::NUMERIC AS service_level
    FROM shifted_sales ss
    LEFT JOIN aggregated_po po 
        ON ss.dist_code = po.dist_code
        AND ss.kode_cabang = po.kode_cabang
        AND ss.item_code = po.item_code
        AND ss.tahun = po.po_year
    JOIN m__products uc 
        ON ss.item_code = uc.item_code
    LEFT JOIN m__kategoris mk 
        ON uc.parent_code = mk.parent_code
    LEFT JOIN m__cabangs mcb
        ON ss.kode_cabang = mcb.kode_cabang
),
trend_with_status AS (
    SELECT *,
        CASE
            WHEN average_3_month_unit > 0 THEN ROUND(stock_on_hand_unit / NULLIF(average_3_month_unit, 1) * 30)
            ELSE 0
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
        ROUND(CASE 
            WHEN yearly_average_unit = 0 THEN NULL
            ELSE ((average_3_month_unit - yearly_average_unit) / yearly_average_unit) * 100
        END, 2) AS delta
    FROM trend_calculation
)
SELECT 
    tahun,
    dist_code,
    chnl_code,
    brand_code,
    brand_name,
    parent_code,
    item_code,
    item_name,
    price,
    status_product,
    kategori,
    region_name,
    area_code,
    area_name,
    kode_cabang,
    nama_cabang,
    month_1,
    month_2,
    month_3,
    month_4,
    month_5,
    month_6,
    month_7,
    month_8,
    month_9,
    month_10,
    month_11,
    month_12,
    yearly_average_unit,
    yearly_average_value,
    average_9_month_unit,
    average_9_month_value,
    average_6_month_unit,
    average_6_month_value,
    average_3_month_unit,
    average_3_month_value,
    average_sales,
    (average_sales - stock_on_hand_unit) AS purchase_suggestion,
    ((average_sales - stock_on_hand_unit) * price) AS purchase_value,
    stock_on_hand_unit,
    COALESCE(doi_3_month, 0) AS doi_3_month,
    status_trend,
    delta,
    COALESCE(qty_po, 0) AS qty_po,
    COALESCE(qty_sc_reg, 0) AS qty_sc_reg,
    service_level,
    CASE 
        WHEN status_trend = 'TREND TAK BERATURAN' AND delta < 0 AND doi_3_month < 45 THEN 'SCM/SALES'
        WHEN status_trend IN ('TREND PROGRESIF TURUN', 'TREND TURUN') AND delta < 0 AND doi_3_month < 45 THEN 'SALES'
        WHEN status_trend IN ('TREND TAK BERATURAN', 'TREND TURUN', 'TREND PROGRESIF TURUN') AND delta < 0 AND doi_3_month >= 45 THEN 'MARKETING'
        WHEN status_trend = 'TREND NAIK' AND delta < 0 AND doi_3_month < 45 THEN 'SCM'
        WHEN status_trend = 'TREND NAIK' AND delta < 0 AND doi_3_month >= 45 THEN 'MARKETING'
        WHEN status_trend = 'TREND NAIK' AND delta > 0 AND doi_3_month < 45 THEN 'SALES'
        ELSE ''
    END AS PIC
FROM trend_with_status
    WHERE 
            (LOWER(trend_with_status.nama_cabang) LIKE :search OR
            LOWER(trend_with_status.chnl_code) LIKE :search OR
            LOWER(CAST(trend_with_status.tahun AS TEXT)) LIKE :search OR
            LOWER(trend_with_status.dist_code) LIKE :search OR
            LOWER(trend_with_status.brand_name) LIKE :search OR
            LOWER(trend_with_status.item_name) LIKE :search OR
            LOWER(trend_with_status.item_code) LIKE :search OR
            LOWER(trend_with_status.region_name) LIKE :search OR
            LOWER(trend_with_status.area_name) LIKE :search OR
            LOWER(trend_with_status.kode_cabang) LIKE :search OR
            LOWER(trend_with_status.kategori) LIKE :search)
        AND (trend_with_status.dist_code LIKE :dist_code
            AND trend_with_status.kode_cabang LIKE :kode_cabang
            AND trend_with_status.region_name LIKE :region_name
            AND trend_with_status.chnl_code LIKE :chnl_code
            AND trend_with_status.brand_name LIKE :brand_name
            AND trend_with_status.status_product LIKE :status_product
            AND CAST(trend_with_status.tahun AS TEXT) LIKE :tahun)
        LIMIT :limit OFFSET :offset
        ", [
            'selected_year' => $request['tahun'],
            'selected_month' => $selected_month,
            'search' => '%' . $search . '%',
            'dist_code' => '%' . $request['dist_code'] . '%',
            'kode_cabang' => '%' . $request['branch'] . '%',
            'region_name' => '%' . $request['region_name'] . '%',
            'chnl_code' => '%' . $request['chnl_code'] . '%',
            'brand_name' => '%' . $request['brand_name'] . '%',
            'status_product' => '%' . $request['status_product'] . '%',
            'tahun' => '%' . $request['tahun'] . '%',
            'limit' => $arr_pagination['limit'],
            'offset' => $arr_pagination['offset'],
        ]);
    }

    public function countMonthlySalesData($search, $request, $selected_month, $selected_year)
    {
        $search = strtolower($search);

        return DB::select("
   WITH aggregated_sales AS (
    SELECT 
        CAST(s.tahun AS INTEGER) AS tahun,
        s.item_code,
        s.dist_code,
        s.kode_cabang,
        s.chnl_code,
        CAST(CASE WHEN s.bulan ~ '^[0-9]+$' THEN s.bulan ELSE NULL END AS INTEGER) AS bulan,
        SUM(CASE WHEN s.net_sales_unit ~ '^[0-9,]+$' THEN NULLIF(REPLACE(s.net_sales_unit, ',', ''), '')::NUMERIC ELSE 0 END)::INTEGER AS net_sales_unit
    FROM sales__units s
    WHERE s.bulan ~ '^[0-9]+$'
    GROUP BY CAST(s.tahun AS INTEGER), s.item_code, s.dist_code, s.kode_cabang, s.chnl_code, CAST(CASE WHEN s.bulan ~ '^[0-9]+$' THEN s.bulan ELSE NULL END AS INTEGER)
),
aggregated_stock AS (
    SELECT 
        CAST(st.tahun AS INTEGER) AS tahun,
        st.item_code,
        st.dist_code,
        st.kode_cabang,
        CAST(CASE WHEN st.bulan ~ '^[0-9]+$' THEN st.bulan ELSE NULL END AS INTEGER) AS bulan,
        SUM(CASE WHEN st.on_hand_unit ~ '^[0-9,]+$' THEN NULLIF(REPLACE(st.on_hand_unit, ',', ''), '')::NUMERIC ELSE 0 END)::INTEGER AS on_hand_unit
    FROM stock__details st
    WHERE st.bulan ~ '^[0-9]+$'
    GROUP BY CAST(st.tahun AS INTEGER), st.item_code, st.dist_code, st.kode_cabang, CAST(CASE WHEN st.bulan ~ '^[0-9]+$' THEN st.bulan ELSE NULL END AS INTEGER)
),
aggregated_po AS (
    SELECT 
        po.dist_code,
        po.mtg_code AS item_code,  
        po.branch_code AS kode_cabang,
        EXTRACT(YEAR FROM TO_DATE(po.tgl_order, 'MM/DD/YYYY'))::INTEGER AS po_year,
        EXTRACT(MONTH FROM TO_DATE(po.tgl_order, 'MM/DD/YYYY'))::INTEGER AS po_month,
        SUM(CASE WHEN po.qty_po ~ '^[0-9,]+$' THEN NULLIF(REPLACE(po.qty_po, ',', ''), '')::NUMERIC ELSE 0 END)::INTEGER AS qty_po,
        SUM(CASE WHEN po.qty_sc_reg ~ '^[0-9,]+$' THEN NULLIF(REPLACE(po.qty_sc_reg, ',', ''), '')::NUMERIC ELSE 0 END)::INTEGER AS qty_sc_reg
    FROM p_o_custs po
    WHERE po.qty_po ~ '^[0-9,]+$'
      AND EXTRACT(MONTH FROM TO_DATE(po.tgl_order, 'MM/DD/YYYY')) = :selected_month
    GROUP BY po.dist_code, po.mtg_code, po.branch_code, EXTRACT(YEAR FROM TO_DATE(po.tgl_order, 'MM/DD/YYYY')), EXTRACT(MONTH FROM TO_DATE(po.tgl_order, 'MM/DD/YYYY'))
),
shifted_sales AS (
    SELECT
        s.tahun,
        s.item_code,
        s.dist_code,
        s.kode_cabang,
        s.chnl_code,
        COALESCE(MAX(CASE 
            WHEN (s.tahun = CAST(:selected_year AS INTEGER) AND s.bulan = CAST(:selected_month AS INTEGER) - 11) OR
                 (s.tahun = CAST(:selected_year AS INTEGER) - 1 AND s.bulan = CAST(:selected_month AS INTEGER) + 1)
            THEN s.net_sales_unit END), 0) AS month_1,
        COALESCE(MAX(CASE 
            WHEN (s.tahun = CAST(:selected_year AS INTEGER) AND s.bulan = CAST(:selected_month AS INTEGER) - 10) OR
                 (s.tahun = CAST(:selected_year AS INTEGER) - 1 AND s.bulan = CAST(:selected_month AS INTEGER) + 2)
            THEN s.net_sales_unit END), 0) AS month_2,
        COALESCE(MAX(CASE 
            WHEN (s.tahun = CAST(:selected_year AS INTEGER) AND s.bulan = CAST(:selected_month AS INTEGER) - 9) OR
                 (s.tahun = CAST(:selected_year AS INTEGER) - 1 AND s.bulan = CAST(:selected_month AS INTEGER) + 3)
            THEN s.net_sales_unit END), 0) AS month_3,
        COALESCE(MAX(CASE 
            WHEN (s.tahun = CAST(:selected_year AS INTEGER) AND s.bulan = CAST(:selected_month AS INTEGER) - 8) OR
                 (s.tahun = CAST(:selected_year AS INTEGER) - 1 AND s.bulan = CAST(:selected_month AS INTEGER) + 4)
            THEN s.net_sales_unit END), 0) AS month_4,
        COALESCE(MAX(CASE 
            WHEN (s.tahun = CAST(:selected_year AS INTEGER) AND s.bulan = CAST(:selected_month AS INTEGER) - 7) OR
                 (s.tahun = CAST(:selected_year AS INTEGER) - 1 AND s.bulan = CAST(:selected_month AS INTEGER) + 5)
            THEN s.net_sales_unit END), 0) AS month_5,
        COALESCE(MAX(CASE 
            WHEN (s.tahun = CAST(:selected_year AS INTEGER) AND s.bulan = CAST(:selected_month AS INTEGER) - 6) OR
                 (s.tahun = CAST(:selected_year AS INTEGER) - 1 AND s.bulan = CAST(:selected_month AS INTEGER) + 6)
            THEN s.net_sales_unit END), 0) AS month_6,
        COALESCE(MAX(CASE 
            WHEN (s.tahun = CAST(:selected_year AS INTEGER) AND s.bulan = CAST(:selected_month AS INTEGER) - 5) OR
                 (s.tahun = CAST(:selected_year AS INTEGER) - 1 AND s.bulan = CAST(:selected_month AS INTEGER) + 7)
            THEN s.net_sales_unit END), 0) AS month_7,
        COALESCE(MAX(CASE 
            WHEN (s.tahun = CAST(:selected_year AS INTEGER) AND s.bulan = CAST(:selected_month AS INTEGER) - 4) OR
                 (s.tahun = CAST(:selected_year AS INTEGER) - 1 AND s.bulan = CAST(:selected_month AS INTEGER) + 8)
            THEN s.net_sales_unit END), 0) AS month_8,
        COALESCE(MAX(CASE 
            WHEN (s.tahun = CAST(:selected_year AS INTEGER) AND s.bulan = CAST(:selected_month AS INTEGER) - 3) OR
                 (s.tahun = CAST(:selected_year AS INTEGER) - 1 AND s.bulan = CAST(:selected_month AS INTEGER) + 9)
            THEN s.net_sales_unit END), 0) AS month_9,
        COALESCE(MAX(CASE 
            WHEN (s.tahun = CAST(:selected_year AS INTEGER) AND s.bulan = CAST(:selected_month AS INTEGER) - 2) OR
                 (s.tahun = CAST(:selected_year AS INTEGER) - 1 AND s.bulan = CAST(:selected_month AS INTEGER) + 10)
            THEN s.net_sales_unit END), 0) AS month_10,
        COALESCE(MAX(CASE 
            WHEN (s.tahun = CAST(:selected_year AS INTEGER) AND s.bulan = CAST(:selected_month AS INTEGER) - 1) OR
                 (s.tahun = CAST(:selected_year AS INTEGER) - 1 AND s.bulan = CAST(:selected_month AS INTEGER) + 11)
            THEN s.net_sales_unit END), 0) AS month_11,
        COALESCE(MAX(CASE 
            WHEN s.tahun = CAST(:selected_year AS INTEGER) AND s.bulan = CAST(:selected_month AS INTEGER)
            THEN s.net_sales_unit END), 0) AS month_12,
        COALESCE(MAX(CASE 
            WHEN s.tahun = CAST(:selected_year AS INTEGER) AND s.bulan = CAST(:selected_month AS INTEGER)
            THEN st.on_hand_unit END), 0) AS stock_on_hand_unit
    FROM aggregated_sales s
    LEFT JOIN aggregated_stock st 
        ON s.item_code = st.item_code 
        AND s.dist_code = st.dist_code 
        AND s.kode_cabang = st.kode_cabang
        AND s.tahun = st.tahun
        AND s.bulan = st.bulan
    GROUP BY 
        s.tahun, s.item_code, s.dist_code, s.kode_cabang, s.chnl_code
),
trend_calculation AS (
    SELECT 
        ss.*,
        po.qty_po,
        po.qty_sc_reg,
        uc.brand_code,
        uc.brand_name,
        uc.parent_code,
        uc.item_name,
        NULLIF(REPLACE(uc.price, ',', ''), '')::NUMERIC AS price,
        uc.status_product,
        mk.kategori,
        mcb.region_name,
        mcb.area_code,
        mcb.area_name,
        mcb.nama_cabang,
        ROUND((COALESCE(ss.month_1, 0) + COALESCE(ss.month_2, 0) + COALESCE(ss.month_3, 0) +
            COALESCE(ss.month_4, 0) + COALESCE(ss.month_5, 0) + COALESCE(ss.month_6, 0) +
            COALESCE(ss.month_7, 0) + COALESCE(ss.month_8, 0) + COALESCE(ss.month_9, 0) +
            COALESCE(ss.month_10, 0) + COALESCE(ss.month_11, 0) + COALESCE(ss.month_12, 0)) / 12)::INTEGER AS yearly_average_unit,
        ROUND((COALESCE(ss.month_1, 0) + COALESCE(ss.month_2, 0) + COALESCE(ss.month_3, 0) +
            COALESCE(ss.month_4, 0) + COALESCE(ss.month_5, 0) + COALESCE(ss.month_6, 0) +
            COALESCE(ss.month_7, 0) + COALESCE(ss.month_8, 0) + COALESCE(ss.month_9, 0) +
            COALESCE(ss.month_10, 0) + COALESCE(ss.month_11, 0) + COALESCE(ss.month_12, 0)) / 12)::INTEGER AS average_sales,
        ROUND(((COALESCE(ss.month_1, 0) + COALESCE(ss.month_2, 0) + COALESCE(ss.month_3, 0) +
            COALESCE(ss.month_4, 0) + COALESCE(ss.month_5, 0) + COALESCE(ss.month_6, 0) +
            COALESCE(ss.month_7, 0) + COALESCE(ss.month_8, 0) + COALESCE(ss.month_9, 0) +
            COALESCE(ss.month_10, 0) + COALESCE(ss.month_11, 0) + COALESCE(ss.month_12, 0)) / 12 * 
            NULLIF(REPLACE(uc.price, ',', ''), '')::NUMERIC))::INTEGER AS yearly_average_value,
        ROUND((COALESCE(ss.month_4, 0) + COALESCE(ss.month_5, 0) + COALESCE(ss.month_6, 0) +
            COALESCE(ss.month_7, 0) + COALESCE(ss.month_8, 0) + COALESCE(ss.month_9, 0) +
            COALESCE(ss.month_10, 0) + COALESCE(ss.month_11, 0) + COALESCE(ss.month_12, 0)) / 9)::INTEGER AS average_9_month_unit,
        ROUND(((COALESCE(ss.month_4, 0) + COALESCE(ss.month_5, 0) + COALESCE(ss.month_6, 0) +
            COALESCE(ss.month_7, 0) + COALESCE(ss.month_8, 0) + COALESCE(ss.month_9, 0) +
            COALESCE(ss.month_10, 0) + COALESCE(ss.month_11, 0) + COALESCE(ss.month_12, 0)) / 9 *
            NULLIF(REPLACE(uc.price, ',', ''), '')::NUMERIC))::INTEGER AS average_9_month_value,
        ROUND((COALESCE(ss.month_7, 0) + COALESCE(ss.month_8, 0) + COALESCE(ss.month_9, 0) +
            COALESCE(ss.month_10, 0) + COALESCE(ss.month_11, 0) + COALESCE(ss.month_12, 0)) / 6)::INTEGER AS average_6_month_unit,
        ROUND(((COALESCE(ss.month_7, 0) + COALESCE(ss.month_8, 0) + COALESCE(ss.month_9, 0) +
            COALESCE(ss.month_10, 0) + COALESCE(ss.month_11, 0) + COALESCE(ss.month_12, 0)) / 6 *
            NULLIF(REPLACE(uc.price, ',', ''), '')::NUMERIC))::INTEGER AS average_6_month_value,
        ROUND((COALESCE(ss.month_10, 0) + COALESCE(ss.month_11, 0) + COALESCE(ss.month_12, 0)) / 3)::INTEGER AS average_3_month_unit,
        ROUND(((COALESCE(ss.month_10, 0) + COALESCE(ss.month_11, 0) + COALESCE(ss.month_12, 0)) / 3 *
            NULLIF(REPLACE(uc.price, ',', ''), '')::NUMERIC))::INTEGER AS average_3_month_value,
        ROUND((po.qty_sc_reg::NUMERIC / NULLIF(po.qty_po, 0)) * 100, 2)::NUMERIC AS service_level
    FROM shifted_sales ss
    LEFT JOIN aggregated_po po 
        ON ss.dist_code = po.dist_code
        AND ss.kode_cabang = po.kode_cabang
        AND ss.item_code = po.item_code
        AND ss.tahun = po.po_year
    JOIN m__products uc 
        ON ss.item_code = uc.item_code
    LEFT JOIN m__kategoris mk 
        ON uc.parent_code = mk.parent_code
    LEFT JOIN m__cabangs mcb
        ON ss.kode_cabang = mcb.kode_cabang
),
trend_with_status AS (
    SELECT *,
        CASE
            WHEN average_3_month_unit > 0 THEN ROUND(stock_on_hand_unit / NULLIF(average_3_month_unit, 1) * 30)
            ELSE 0
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
        ROUND(CASE 
            WHEN yearly_average_unit = 0 THEN NULL
            ELSE ((average_3_month_unit - yearly_average_unit) / yearly_average_unit) * 100
        END, 2) AS delta
    FROM trend_calculation
)
SELECT 
    tahun,
    dist_code,
    chnl_code,
    brand_code,
    brand_name,
    parent_code,
    item_code,
    item_name,
    price,
    status_product,
    kategori,
    region_name,
    area_code,
    area_name,
    kode_cabang,
    nama_cabang,
    month_1,
    month_2,
    month_3,
    month_4,
    month_5,
    month_6,
    month_7,
    month_8,
    month_9,
    month_10,
    month_11,
    month_12,
    yearly_average_unit,
    yearly_average_value,
    average_9_month_unit,
    average_9_month_value,
    average_6_month_unit,
    average_6_month_value,
    average_3_month_unit,
    average_3_month_value,
    average_sales,
    (average_sales - stock_on_hand_unit) AS purchase_suggestion,
    ((average_sales - stock_on_hand_unit) * price) AS purchase_value,
    stock_on_hand_unit,
    COALESCE(doi_3_month, 0) AS doi_3_month,
    status_trend,
    delta,
    COALESCE(qty_po, 0) AS qty_po,
    COALESCE(qty_sc_reg, 0) AS qty_sc_reg,
    service_level,
    CASE 
        WHEN status_trend = 'TREND TAK BERATURAN' AND delta < 0 AND doi_3_month < 45 THEN 'SCM/SALES'
        WHEN status_trend IN ('TREND PROGRESIF TURUN', 'TREND TURUN') AND delta < 0 AND doi_3_month < 45 THEN 'SALES'
        WHEN status_trend IN ('TREND TAK BERATURAN', 'TREND TURUN', 'TREND PROGRESIF TURUN') AND delta < 0 AND doi_3_month >= 45 THEN 'MARKETING'
        WHEN status_trend = 'TREND NAIK' AND delta < 0 AND doi_3_month < 45 THEN 'SCM'
        WHEN status_trend = 'TREND NAIK' AND delta < 0 AND doi_3_month >= 45 THEN 'MARKETING'
        WHEN status_trend = 'TREND NAIK' AND delta > 0 AND doi_3_month < 45 THEN 'SALES'
        ELSE ''
    END AS PIC
FROM trend_with_status
    WHERE 
        (LOWER(trend_with_status.nama_cabang) LIKE :search OR
        LOWER(trend_with_status.chnl_code) LIKE :search OR
        LOWER(CAST(trend_with_status.tahun AS TEXT)) LIKE :search OR
        LOWER(trend_with_status.dist_code) LIKE :search OR
        LOWER(trend_with_status.brand_name) LIKE :search OR
        LOWER(trend_with_status.item_name) LIKE :search OR
        LOWER(trend_with_status.item_code) LIKE :search OR
        LOWER(trend_with_status.region_name) LIKE :search OR
        LOWER(trend_with_status.area_name) LIKE :search OR
        LOWER(trend_with_status.kode_cabang) LIKE :search OR
        LOWER(trend_with_status.kategori) LIKE :search) 
    AND (trend_with_status.dist_code LIKE :dist_code
        AND trend_with_status.kode_cabang LIKE :kode_cabang
        AND trend_with_status.region_name LIKE :region_name
        AND trend_with_status.chnl_code LIKE :chnl_code
        AND trend_with_status.brand_name LIKE :brand_name
        AND trend_with_status.status_product LIKE :status_product
        AND CAST(trend_with_status.tahun AS TEXT) LIKE :tahun)
    ", [
            'selected_year' => $request['tahun'],
            'selected_month' => $selected_month,
            'search' => '%' . $search . '%',
            'dist_code' => '%' . $request['dist_code'] . '%',
            'kode_cabang' => '%' . $request['branch'] . '%',
            'region_name' => '%' . $request['region_name'] . '%',
            'chnl_code' => '%' . $request['chnl_code'] . '%',
            'brand_name' => '%' . $request['brand_name'] . '%',
            'status_product' => '%' . $request['status_product'] . '%',
            'tahun' => '%' . $request['tahun'] . '%'
        ]);
    }

}