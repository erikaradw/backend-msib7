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


    public function getMonthlySalesData($search, $request, $arr_pagination)
    {
        // Jika ada pencarian, reset offset pagination ke 0
        if (!empty($search)) {
            $arr_pagination['offset'] = 0;
        }

        $search = strtolower($search);

        return DB::select("
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
, calculated_averages AS (
SELECT
    s.tahun,
    s.dist_code,
    s.chnl_code,
    s.item_code,
    uc.brand_code,
    uc.brand_name,
    uc.parent_code,
    uc.item_name,
    uc.price::NUMERIC AS price,  -- Cast price to NUMERIC here
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
    COALESCE(po.beli_oktober, 0) AS beli_oktober, COALESCE(po.beli_november, 0) AS beli_november, COALESCE(po.beli_desember, 0) AS beli_desember,
    (COALESCE(s.januari, 0) + COALESCE(s.februari, 0) + COALESCE(s.maret, 0) +
     COALESCE(s.april, 0) + COALESCE(s.mei, 0) + COALESCE(s.juni, 0) +
     COALESCE(s.juli, 0) + COALESCE(s.agustus, 0) + COALESCE(s.september, 0) + 
     COALESCE(s.oktober, 0) + COALESCE(s.november, 0) + COALESCE(s.desember, 0)) / 12 AS average_sales,
      ((COALESCE(s.januari, 0) + COALESCE(s.februari, 0) + COALESCE(s.maret, 0) +
          COALESCE(s.april, 0) + COALESCE(s.mei, 0) + COALESCE(s.juni, 0) +
          COALESCE(s.juli, 0) + COALESCE(s.agustus, 0) + COALESCE(s.september, 0) + 
          COALESCE(s.oktober, 0) + COALESCE(s.november, 0) + COALESCE(s.desember, 0)) / 12) - COALESCE(st.desember1, 0) AS purchase_suggestion,
        (((COALESCE(s.januari, 0) + COALESCE(s.februari, 0) + COALESCE(s.maret, 0) +
        COALESCE(s.april, 0) + COALESCE(s.mei, 0) + COALESCE(s.juni, 0) +
        COALESCE(s.juli, 0) + COALESCE(s.agustus, 0) + COALESCE(s.september, 0) + 
        COALESCE(s.oktober, 0) + COALESCE(s.november, 0) + COALESCE(s.desember, 0)) / 12 - COALESCE(st.desember1, 0)) * uc.price::NUMERIC) AS purchase_value,
    -- Yearly Average Unit
    CASE
        WHEN (COALESCE(s.april, 0) + COALESCE(s.mei, 0) + COALESCE(s.juni, 0) + 
              COALESCE(s.juli, 0) + COALESCE(s.agustus, 0) + COALESCE(s.september, 0) +
              COALESCE(s.oktober, 0) + COALESCE(s.november, 0) + COALESCE(s.desember, 0)) = 0
        THEN 0
        ELSE (NULLIF(
            (COALESCE(s.januari, 0) + COALESCE(s.februari, 0) + COALESCE(s.maret, 0) +
             COALESCE(s.april, 0) + COALESCE(s.mei, 0) + COALESCE(s.juni, 0) +
             COALESCE(s.juli, 0) + COALESCE(s.agustus, 0) + COALESCE(s.september, 0) + 
             COALESCE(s.oktober, 0) + COALESCE(s.november, 0) + COALESCE(s.desember, 0)
            ), 0) /
            NULLIF(
                (CASE WHEN s.januari <> 0 THEN 1 ELSE 0 END + 
                 CASE WHEN s.februari <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.maret <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.april <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.mei <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.juni <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.juli <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.agustus <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.september <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.oktober <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.november <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.desember <> 0 THEN 1 ELSE 0 END), 0)
        )
    END AS unit12,
      (CASE
        WHEN (COALESCE(s.april, 0) + COALESCE(s.mei, 0) + COALESCE(s.juni, 0) + 
              COALESCE(s.juli, 0) + COALESCE(s.agustus, 0) + COALESCE(s.september, 0) +
              COALESCE(s.oktober, 0) + COALESCE(s.november, 0) + COALESCE(s.desember, 0)) = 0
        THEN 0
        ELSE (NULLIF(
            (COALESCE(s.januari, 0) + COALESCE(s.februari, 0) + COALESCE(s.maret, 0) +
             COALESCE(s.april, 0) + COALESCE(s.mei, 0) + COALESCE(s.juni, 0) +
             COALESCE(s.juli, 0) + COALESCE(s.agustus, 0) + COALESCE(s.september, 0) + 
             COALESCE(s.oktober, 0) + COALESCE(s.november, 0) + COALESCE(s.desember, 0)
            ), 0) /
            NULLIF(
                (CASE WHEN s.januari <> 0 THEN 1 ELSE 0 END + 
                 CASE WHEN s.februari <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.maret <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.april <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.mei <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.juni <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.juli <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.agustus <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.september <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.oktober <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.november <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.desember <> 0 THEN 1 ELSE 0 END), 0) * uc.price::NUMERIC
        )
    END) AS value12,
     CASE
        WHEN (COALESCE(s.juli, 0) + COALESCE(s.agustus, 0) + COALESCE(s.september, 0) +
              COALESCE(s.oktober, 0) + COALESCE(s.november, 0) + COALESCE(s.desember, 0)) = 0
        THEN 0
        ELSE (NULLIF(
            (COALESCE(s.april, 0) + COALESCE(s.mei, 0) + COALESCE(s.juni, 0) +
             COALESCE(s.juli, 0) + COALESCE(s.agustus, 0) + COALESCE(s.september, 0) +
             COALESCE(s.oktober, 0) + COALESCE(s.november, 0) + COALESCE(s.desember, 0)
            ), 0) /
            NULLIF(
                (CASE WHEN s.april <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.mei <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.juni <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.juli <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.agustus <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.september <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.oktober <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.november <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.desember <> 0 THEN 1 ELSE 0 END), 0)
        )
    END AS unit9,
    -- Average 9-Month Value (Average 9-Month Unit * Price)
    (CASE
        WHEN (COALESCE(s.juli, 0) + COALESCE(s.agustus, 0) + COALESCE(s.september, 0) +
              COALESCE(s.oktober, 0) + COALESCE(s.november, 0) + COALESCE(s.desember, 0)) = 0
        THEN 0
        ELSE (NULLIF(
            (COALESCE(s.april, 0) + COALESCE(s.mei, 0) + COALESCE(s.juni, 0) +
             COALESCE(s.juli, 0) + COALESCE(s.agustus, 0) + COALESCE(s.september, 0) +
             COALESCE(s.oktober, 0) + COALESCE(s.november, 0) + COALESCE(s.desember, 0)
            ), 0) /
            NULLIF(
                (CASE WHEN s.april <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.mei <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.juni <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.juli <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.agustus <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.september <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.oktober <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.november <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.desember <> 0 THEN 1 ELSE 0 END), 0) * uc.price::NUMERIC
        )
    END) AS value9,
     CASE
        WHEN (COALESCE(s.oktober, 0) + COALESCE(s.november, 0) + COALESCE(s.desember, 0)) = 0
        THEN 0
        ELSE (NULLIF(
            (COALESCE(s.juli, 0) + COALESCE(s.agustus, 0) + COALESCE(s.september, 0) +
             COALESCE(s.oktober, 0) + COALESCE(s.november, 0) + COALESCE(s.desember, 0)
            ), 0) /
            NULLIF(
                (CASE WHEN s.juli <> 0 THEN 1 ELSE 0 END + 
                 CASE WHEN s.agustus <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.september <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.oktober <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.november <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.desember <> 0 THEN 1 ELSE 0 END), 0)
        )
    END AS unit6,
    -- Yearly Average 6-Month Value (Yearly Average 6-Month Unit * Price)
    (CASE
        WHEN (COALESCE(s.oktober, 0) + COALESCE(s.november, 0) + COALESCE(s.desember, 0)) = 0
        THEN 0
        ELSE (NULLIF(
            (COALESCE(s.juli, 0) + COALESCE(s.agustus, 0) + COALESCE(s.september, 0) +
             COALESCE(s.oktober, 0) + COALESCE(s.november, 0) + COALESCE(s.desember, 0)
            ), 0) /
            NULLIF(
                (CASE WHEN s.juli <> 0 THEN 1 ELSE 0 END + 
                 CASE WHEN s.agustus <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.september <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.oktober <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.november <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.desember <> 0 THEN 1 ELSE 0 END), 0) * uc.price::NUMERIC
        )
    END) AS value6,
     (COALESCE(s.oktober, 0) + COALESCE(s.november, 0) + COALESCE(s.desember, 0)) / 3.0 AS unit3,
    -- Yearly Average 3-Month Value (Average 3-Month Unit * Price)
    ((COALESCE(s.oktober, 0) + COALESCE(s.november, 0) + COALESCE(s.desember, 0)) / 3.0) * uc.price::NUMERIC AS value3,
    CASE
        WHEN (COALESCE(s.oktober, 0) + COALESCE(s.november, 0) + COALESCE(s.desember, 0)) = 0
        THEN 0
        ELSE COALESCE(st.desember1, 0) / ((COALESCE(s.oktober, 0) + COALESCE(s.november, 0) + COALESCE(s.desember, 0)) / 3.0) * 30
    END AS doi3bulan
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
JOIN m__cabangs mcb ON s.kode_cabang = mcb.kode_cabang ),
trend_calculation AS (
SELECT *,
    CASE
        WHEN unit12 < 0 AND unit9 < 0 AND unit6 < 0 AND unit3 < 0 THEN 'TREND TAK BERATURAN'
        WHEN unit12 > unit9 AND unit9 < unit6 AND unit6 < unit3 THEN 'TREND NAIK'
        WHEN unit12 < unit9 AND unit9 < unit6 AND unit6 < unit3 THEN 'TREND PROGRESIF NAIK'
        WHEN unit12 > unit9 AND unit9 > unit6 AND unit6 > unit3 THEN 'TREND PROGRESIF TURUN'
        WHEN unit12 < unit9 AND unit9 > unit6 AND unit6 > unit3 THEN 'TREND TURUN'
        ELSE 'TREND TAK BERATURAN'
    END AS status_trend,
    CASE 
        WHEN unit12 != 0 THEN ((unit3 - unit12) / unit12) * 100
        ELSE 0 
    END AS delta
FROM calculated_averages )
SELECT *,
    CASE
        WHEN status_trend = 'TREND TAK BERATURAN' AND delta < 0 AND doi3bulan < 45 THEN 'SCM/SALES'
        WHEN status_trend = 'TREND PROGRESIF TURUN' AND delta < 0 AND doi3bulan < 45 THEN 'SALES'
        WHEN status_trend = 'TREND TURUN' AND delta < 0 AND doi3bulan < 45 THEN 'SALES'
        WHEN status_trend = 'TREND TAK BERATURAN' AND delta < 0 AND doi3bulan > 45 THEN 'MARKETING'
        WHEN status_trend = 'TREND TURUN' AND delta < 0 AND doi3bulan > 45 THEN 'MARKETING'
        WHEN status_trend = 'TREND PROGRESIF TURUN' AND delta < 0 AND doi3bulan > 45 THEN 'MARKETING'
        WHEN status_trend = 'TREND NAIK' AND delta < 0 AND doi3bulan < 45 THEN 'SCM'
        WHEN status_trend = 'TREND NAIK' AND delta < 0 AND doi3bulan > 45 THEN 'MARKETING'
        WHEN status_trend = 'TREND NAIK' AND delta > 0 AND doi3bulan < 45 THEN 'SALES'
        ELSE ''
    END AS pic
FROM trend_calculation
 WHERE 
        (LOWER(trend_calculation.nama_cabang) LIKE '%' || LOWER(?) || '%' OR
         LOWER(trend_calculation.chnl_code) LIKE '%' || LOWER(?) || '%' OR
         LOWER(CAST(trend_calculation.tahun AS TEXT)) LIKE '%' || LOWER(?) || '%' OR
         LOWER(trend_calculation.dist_code) LIKE '%' || LOWER(?) || '%' OR
         LOWER(trend_calculation.brand_name) LIKE '%' || LOWER(?) || '%' OR
         LOWER(trend_calculation.item_name) LIKE '%' || LOWER(?) || '%' OR
         LOWER(trend_calculation.item_code) LIKE '%' || LOWER(?) || '%' OR
         LOWER(trend_calculation.region_name) LIKE '%' || LOWER(?) || '%' OR
         LOWER(trend_calculation.area_name) LIKE '%' || LOWER(?) || '%' OR
         LOWER(trend_calculation.kode_cabang) LIKE '%' || LOWER(?) || '%' OR
         LOWER(trend_calculation.kategori) LIKE '%' || LOWER(?) || '%') 
    AND (trend_calculation.dist_code LIKE '%' || ?  || '%'
        AND trend_calculation.kode_cabang LIKE '%' || ?  || '%'
        AND trend_calculation.region_name LIKE '%' || ?  || '%'
        AND trend_calculation.chnl_code LIKE '%' || ?  || '%'
        AND trend_calculation.brand_name LIKE '%' || ?  || '%'
        AND trend_calculation.status_product LIKE '%' || ?  || '%'
        AND trend_calculation.tahun LIKE '%' || ?  || '%')
    LIMIT ? OFFSET ?
", [$search, $search, $search, $search, $search, $search, $search, $search, $search, $search, $search, $request['dist_code'], $request['branch'], $request['region_name'], $request['chnl_code'], $request['brand_name'], $request['status_product'], $request['tahun'], $arr_pagination['limit'], $arr_pagination['offset']]);
    }

    public function countMonthlySalesData($search, $request)
    {
        $search = strtolower($search);

        return DB::select("
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
, calculated_averages AS (
SELECT
    s.tahun,
    s.dist_code,
    s.chnl_code,
    s.item_code,
    uc.brand_code,
    uc.brand_name,
    uc.parent_code,
    uc.item_name,
    uc.price::NUMERIC AS price,  -- Cast price to NUMERIC here
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
    COALESCE(po.beli_oktober, 0) AS beli_oktober, COALESCE(po.beli_november, 0) AS beli_november, COALESCE(po.beli_desember, 0) AS beli_desember,
    (COALESCE(s.januari, 0) + COALESCE(s.februari, 0) + COALESCE(s.maret, 0) +
     COALESCE(s.april, 0) + COALESCE(s.mei, 0) + COALESCE(s.juni, 0) +
     COALESCE(s.juli, 0) + COALESCE(s.agustus, 0) + COALESCE(s.september, 0) + 
     COALESCE(s.oktober, 0) + COALESCE(s.november, 0) + COALESCE(s.desember, 0)) / 12 AS average_sales,
      ((COALESCE(s.januari, 0) + COALESCE(s.februari, 0) + COALESCE(s.maret, 0) +
          COALESCE(s.april, 0) + COALESCE(s.mei, 0) + COALESCE(s.juni, 0) +
          COALESCE(s.juli, 0) + COALESCE(s.agustus, 0) + COALESCE(s.september, 0) + 
          COALESCE(s.oktober, 0) + COALESCE(s.november, 0) + COALESCE(s.desember, 0)) / 12) - COALESCE(st.desember1, 0) AS purchase_suggestion,
        (((COALESCE(s.januari, 0) + COALESCE(s.februari, 0) + COALESCE(s.maret, 0) +
        COALESCE(s.april, 0) + COALESCE(s.mei, 0) + COALESCE(s.juni, 0) +
        COALESCE(s.juli, 0) + COALESCE(s.agustus, 0) + COALESCE(s.september, 0) + 
        COALESCE(s.oktober, 0) + COALESCE(s.november, 0) + COALESCE(s.desember, 0)) / 12 - COALESCE(st.desember1, 0)) * uc.price::NUMERIC) AS purchase_value,
    -- Yearly Average Unit
    CASE
        WHEN (COALESCE(s.april, 0) + COALESCE(s.mei, 0) + COALESCE(s.juni, 0) + 
              COALESCE(s.juli, 0) + COALESCE(s.agustus, 0) + COALESCE(s.september, 0) +
              COALESCE(s.oktober, 0) + COALESCE(s.november, 0) + COALESCE(s.desember, 0)) = 0
        THEN 0
        ELSE (NULLIF(
            (COALESCE(s.januari, 0) + COALESCE(s.februari, 0) + COALESCE(s.maret, 0) +
             COALESCE(s.april, 0) + COALESCE(s.mei, 0) + COALESCE(s.juni, 0) +
             COALESCE(s.juli, 0) + COALESCE(s.agustus, 0) + COALESCE(s.september, 0) + 
             COALESCE(s.oktober, 0) + COALESCE(s.november, 0) + COALESCE(s.desember, 0)
            ), 0) /
            NULLIF(
                (CASE WHEN s.januari <> 0 THEN 1 ELSE 0 END + 
                 CASE WHEN s.februari <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.maret <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.april <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.mei <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.juni <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.juli <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.agustus <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.september <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.oktober <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.november <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.desember <> 0 THEN 1 ELSE 0 END), 0)
        )
    END AS unit12,
      (CASE
        WHEN (COALESCE(s.april, 0) + COALESCE(s.mei, 0) + COALESCE(s.juni, 0) + 
              COALESCE(s.juli, 0) + COALESCE(s.agustus, 0) + COALESCE(s.september, 0) +
              COALESCE(s.oktober, 0) + COALESCE(s.november, 0) + COALESCE(s.desember, 0)) = 0
        THEN 0
        ELSE (NULLIF(
            (COALESCE(s.januari, 0) + COALESCE(s.februari, 0) + COALESCE(s.maret, 0) +
             COALESCE(s.april, 0) + COALESCE(s.mei, 0) + COALESCE(s.juni, 0) +
             COALESCE(s.juli, 0) + COALESCE(s.agustus, 0) + COALESCE(s.september, 0) + 
             COALESCE(s.oktober, 0) + COALESCE(s.november, 0) + COALESCE(s.desember, 0)
            ), 0) /
            NULLIF(
                (CASE WHEN s.januari <> 0 THEN 1 ELSE 0 END + 
                 CASE WHEN s.februari <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.maret <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.april <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.mei <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.juni <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.juli <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.agustus <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.september <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.oktober <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.november <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.desember <> 0 THEN 1 ELSE 0 END), 0) * uc.price::NUMERIC
        )
    END) AS value12,
     CASE
        WHEN (COALESCE(s.juli, 0) + COALESCE(s.agustus, 0) + COALESCE(s.september, 0) +
              COALESCE(s.oktober, 0) + COALESCE(s.november, 0) + COALESCE(s.desember, 0)) = 0
        THEN 0
        ELSE (NULLIF(
            (COALESCE(s.april, 0) + COALESCE(s.mei, 0) + COALESCE(s.juni, 0) +
             COALESCE(s.juli, 0) + COALESCE(s.agustus, 0) + COALESCE(s.september, 0) +
             COALESCE(s.oktober, 0) + COALESCE(s.november, 0) + COALESCE(s.desember, 0)
            ), 0) /
            NULLIF(
                (CASE WHEN s.april <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.mei <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.juni <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.juli <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.agustus <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.september <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.oktober <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.november <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.desember <> 0 THEN 1 ELSE 0 END), 0)
        )
    END AS unit9,
    -- Average 9-Month Value (Average 9-Month Unit * Price)
    (CASE
        WHEN (COALESCE(s.juli, 0) + COALESCE(s.agustus, 0) + COALESCE(s.september, 0) +
              COALESCE(s.oktober, 0) + COALESCE(s.november, 0) + COALESCE(s.desember, 0)) = 0
        THEN 0
        ELSE (NULLIF(
            (COALESCE(s.april, 0) + COALESCE(s.mei, 0) + COALESCE(s.juni, 0) +
             COALESCE(s.juli, 0) + COALESCE(s.agustus, 0) + COALESCE(s.september, 0) +
             COALESCE(s.oktober, 0) + COALESCE(s.november, 0) + COALESCE(s.desember, 0)
            ), 0) /
            NULLIF(
                (CASE WHEN s.april <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.mei <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.juni <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.juli <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.agustus <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.september <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.oktober <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.november <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.desember <> 0 THEN 1 ELSE 0 END), 0) * uc.price::NUMERIC
        )
    END) AS value9,
     CASE
        WHEN (COALESCE(s.oktober, 0) + COALESCE(s.november, 0) + COALESCE(s.desember, 0)) = 0
        THEN 0
        ELSE (NULLIF(
            (COALESCE(s.juli, 0) + COALESCE(s.agustus, 0) + COALESCE(s.september, 0) +
             COALESCE(s.oktober, 0) + COALESCE(s.november, 0) + COALESCE(s.desember, 0)
            ), 0) /
            NULLIF(
                (CASE WHEN s.juli <> 0 THEN 1 ELSE 0 END + 
                 CASE WHEN s.agustus <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.september <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.oktober <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.november <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.desember <> 0 THEN 1 ELSE 0 END), 0)
        )
    END AS unit6,
    -- Yearly Average 6-Month Value (Yearly Average 6-Month Unit * Price)
    (CASE
        WHEN (COALESCE(s.oktober, 0) + COALESCE(s.november, 0) + COALESCE(s.desember, 0)) = 0
        THEN 0
        ELSE (NULLIF(
            (COALESCE(s.juli, 0) + COALESCE(s.agustus, 0) + COALESCE(s.september, 0) +
             COALESCE(s.oktober, 0) + COALESCE(s.november, 0) + COALESCE(s.desember, 0)
            ), 0) /
            NULLIF(
                (CASE WHEN s.juli <> 0 THEN 1 ELSE 0 END + 
                 CASE WHEN s.agustus <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.september <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.oktober <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.november <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.desember <> 0 THEN 1 ELSE 0 END), 0) * uc.price::NUMERIC
        )
    END) AS value6,
     (COALESCE(s.oktober, 0) + COALESCE(s.november, 0) + COALESCE(s.desember, 0)) / 3.0 AS unit3,
    -- Yearly Average 3-Month Value (Average 3-Month Unit * Price)
    ((COALESCE(s.oktober, 0) + COALESCE(s.november, 0) + COALESCE(s.desember, 0)) / 3.0) * uc.price::NUMERIC AS value3,
    CASE
        WHEN (COALESCE(s.oktober, 0) + COALESCE(s.november, 0) + COALESCE(s.desember, 0)) = 0
        THEN 0
        ELSE COALESCE(st.desember1, 0) / ((COALESCE(s.oktober, 0) + COALESCE(s.november, 0) + COALESCE(s.desember, 0)) / 3.0) * 30
    END AS doi3bulan
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
JOIN m__cabangs mcb ON s.kode_cabang = mcb.kode_cabang ),
trend_calculation AS (
SELECT *,
    CASE
        WHEN unit12 < 0 AND unit9 < 0 AND unit6 < 0 AND unit3 < 0 THEN 'TREND TAK BERATURAN'
        WHEN unit12 > unit9 AND unit9 < unit6 AND unit6 < unit3 THEN 'TREND NAIK'
        WHEN unit12 < unit9 AND unit9 < unit6 AND unit6 < unit3 THEN 'TREND PROGRESIF NAIK'
        WHEN unit12 > unit9 AND unit9 > unit6 AND unit6 > unit3 THEN 'TREND PROGRESIF TURUN'
        WHEN unit12 < unit9 AND unit9 > unit6 AND unit6 > unit3 THEN 'TREND TURUN'
        ELSE 'TREND TAK BERATURAN'
    END AS status_trend,
    CASE 
        WHEN unit12 != 0 THEN ((unit3 - unit12) / unit12) * 100
        ELSE 0 
    END AS delta
FROM calculated_averages )
SELECT *,
    CASE
        WHEN status_trend = 'TREND TAK BERATURAN' AND delta < 0 AND doi3bulan < 45 THEN 'SCM/SALES'
        WHEN status_trend = 'TREND PROGRESIF TURUN' AND delta < 0 AND doi3bulan < 45 THEN 'SALES'
        WHEN status_trend = 'TREND TURUN' AND delta < 0 AND doi3bulan < 45 THEN 'SALES'
        WHEN status_trend = 'TREND TAK BERATURAN' AND delta < 0 AND doi3bulan > 45 THEN 'MARKETING'
        WHEN status_trend = 'TREND TURUN' AND delta < 0 AND doi3bulan > 45 THEN 'MARKETING'
        WHEN status_trend = 'TREND PROGRESIF TURUN' AND delta < 0 AND doi3bulan > 45 THEN 'MARKETING'
        WHEN status_trend = 'TREND NAIK' AND delta < 0 AND doi3bulan < 45 THEN 'SCM'
        WHEN status_trend = 'TREND NAIK' AND delta < 0 AND doi3bulan > 45 THEN 'MARKETING'
        WHEN status_trend = 'TREND NAIK' AND delta > 0 AND doi3bulan < 45 THEN 'SALES'
        ELSE ''
    END AS pic
FROM trend_calculation
 WHERE 
        (LOWER(trend_calculation.nama_cabang) LIKE '%' || LOWER(?) || '%' OR
         LOWER(trend_calculation.chnl_code) LIKE '%' || LOWER(?) || '%' OR
         LOWER(CAST(trend_calculation.tahun AS TEXT)) LIKE '%' || LOWER(?) || '%' OR
         LOWER(trend_calculation.dist_code) LIKE '%' || LOWER(?) || '%' OR
         LOWER(trend_calculation.brand_name) LIKE '%' || LOWER(?) || '%' OR
         LOWER(trend_calculation.item_name) LIKE '%' || LOWER(?) || '%' OR
         LOWER(trend_calculation.item_code) LIKE '%' || LOWER(?) || '%' OR
         LOWER(trend_calculation.region_name) LIKE '%' || LOWER(?) || '%' OR
         LOWER(trend_calculation.area_name) LIKE '%' || LOWER(?) || '%' OR
         LOWER(trend_calculation.kode_cabang) LIKE '%' || LOWER(?) || '%' OR
         LOWER(trend_calculation.kategori) LIKE '%' || LOWER(?) || '%')
    AND (trend_calculation.dist_code LIKE '%' || ?  || '%'
        AND trend_calculation.kode_cabang LIKE '%' || ?  || '%'
        AND trend_calculation.region_name LIKE '%' || ?  || '%'
        AND trend_calculation.chnl_code LIKE '%' || ?  || '%'
        AND trend_calculation.brand_name LIKE '%' || ?  || '%'
        AND trend_calculation.status_product LIKE '%' || ?  || '%'
        AND trend_calculation.tahun LIKE '%' || ?  || '%')
", [$search, $search, $search, $search, $search, $search, $search, $search, $search, $search, $search, $request['dist_code'], $request['branch'], $request['region_name'], $request['chnl_code'], $request['brand_name'], $request['status_product'], $request['tahun']]);
    }

    public function insertTrendsData()
    {
        // Menjalankan query untuk mendapatkan data
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
, calculated_averages AS (
SELECT
    s.tahun,
    s.dist_code,
    s.chnl_code,
    s.item_code,
    uc.brand_code,
    uc.brand_name,
    uc.parent_code,
    uc.item_name,
    uc.price::NUMERIC AS price,  -- Cast price to NUMERIC here
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
    COALESCE(po.beli_oktober, 0) AS beli_oktober, COALESCE(po.beli_november, 0) AS beli_november, COALESCE(po.beli_desember, 0) AS beli_desember,
    (COALESCE(s.januari, 0) + COALESCE(s.februari, 0) + COALESCE(s.maret, 0) +
     COALESCE(s.april, 0) + COALESCE(s.mei, 0) + COALESCE(s.juni, 0) +
     COALESCE(s.juli, 0) + COALESCE(s.agustus, 0) + COALESCE(s.september, 0) + 
     COALESCE(s.oktober, 0) + COALESCE(s.november, 0) + COALESCE(s.desember, 0)) / 12 AS average_sales,
      ((COALESCE(s.januari, 0) + COALESCE(s.februari, 0) + COALESCE(s.maret, 0) +
          COALESCE(s.april, 0) + COALESCE(s.mei, 0) + COALESCE(s.juni, 0) +
          COALESCE(s.juli, 0) + COALESCE(s.agustus, 0) + COALESCE(s.september, 0) + 
          COALESCE(s.oktober, 0) + COALESCE(s.november, 0) + COALESCE(s.desember, 0)) / 12) - COALESCE(st.desember1, 0) AS purchase_suggestion,
        (((COALESCE(s.januari, 0) + COALESCE(s.februari, 0) + COALESCE(s.maret, 0) +
        COALESCE(s.april, 0) + COALESCE(s.mei, 0) + COALESCE(s.juni, 0) +
        COALESCE(s.juli, 0) + COALESCE(s.agustus, 0) + COALESCE(s.september, 0) + 
        COALESCE(s.oktober, 0) + COALESCE(s.november, 0) + COALESCE(s.desember, 0)) / 12 - COALESCE(st.desember1, 0)) * uc.price::NUMERIC) AS purchase_value,
    -- Yearly Average Unit
    CASE
        WHEN (COALESCE(s.april, 0) + COALESCE(s.mei, 0) + COALESCE(s.juni, 0) + 
              COALESCE(s.juli, 0) + COALESCE(s.agustus, 0) + COALESCE(s.september, 0) +
              COALESCE(s.oktober, 0) + COALESCE(s.november, 0) + COALESCE(s.desember, 0)) = 0
        THEN 0
        ELSE (NULLIF(
            (COALESCE(s.januari, 0) + COALESCE(s.februari, 0) + COALESCE(s.maret, 0) +
             COALESCE(s.april, 0) + COALESCE(s.mei, 0) + COALESCE(s.juni, 0) +
             COALESCE(s.juli, 0) + COALESCE(s.agustus, 0) + COALESCE(s.september, 0) + 
             COALESCE(s.oktober, 0) + COALESCE(s.november, 0) + COALESCE(s.desember, 0)
            ), 0) /
            NULLIF(
                (CASE WHEN s.januari <> 0 THEN 1 ELSE 0 END + 
                 CASE WHEN s.februari <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.maret <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.april <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.mei <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.juni <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.juli <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.agustus <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.september <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.oktober <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.november <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.desember <> 0 THEN 1 ELSE 0 END), 0)
        )
    END AS unit12,
      (CASE
        WHEN (COALESCE(s.april, 0) + COALESCE(s.mei, 0) + COALESCE(s.juni, 0) + 
              COALESCE(s.juli, 0) + COALESCE(s.agustus, 0) + COALESCE(s.september, 0) +
              COALESCE(s.oktober, 0) + COALESCE(s.november, 0) + COALESCE(s.desember, 0)) = 0
        THEN 0
        ELSE (NULLIF(
            (COALESCE(s.januari, 0) + COALESCE(s.februari, 0) + COALESCE(s.maret, 0) +
             COALESCE(s.april, 0) + COALESCE(s.mei, 0) + COALESCE(s.juni, 0) +
             COALESCE(s.juli, 0) + COALESCE(s.agustus, 0) + COALESCE(s.september, 0) + 
             COALESCE(s.oktober, 0) + COALESCE(s.november, 0) + COALESCE(s.desember, 0)
            ), 0) /
            NULLIF(
                (CASE WHEN s.januari <> 0 THEN 1 ELSE 0 END + 
                 CASE WHEN s.februari <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.maret <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.april <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.mei <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.juni <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.juli <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.agustus <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.september <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.oktober <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.november <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.desember <> 0 THEN 1 ELSE 0 END), 0) * uc.price::NUMERIC
        )
    END) AS value12,
     CASE
        WHEN (COALESCE(s.juli, 0) + COALESCE(s.agustus, 0) + COALESCE(s.september, 0) +
              COALESCE(s.oktober, 0) + COALESCE(s.november, 0) + COALESCE(s.desember, 0)) = 0
        THEN 0
        ELSE (NULLIF(
            (COALESCE(s.april, 0) + COALESCE(s.mei, 0) + COALESCE(s.juni, 0) +
             COALESCE(s.juli, 0) + COALESCE(s.agustus, 0) + COALESCE(s.september, 0) +
             COALESCE(s.oktober, 0) + COALESCE(s.november, 0) + COALESCE(s.desember, 0)
            ), 0) /
            NULLIF(
                (CASE WHEN s.april <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.mei <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.juni <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.juli <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.agustus <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.september <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.oktober <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.november <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.desember <> 0 THEN 1 ELSE 0 END), 0)
        )
    END AS unit9,
    -- Average 9-Month Value (Average 9-Month Unit * Price)
    (CASE
        WHEN (COALESCE(s.juli, 0) + COALESCE(s.agustus, 0) + COALESCE(s.september, 0) +
              COALESCE(s.oktober, 0) + COALESCE(s.november, 0) + COALESCE(s.desember, 0)) = 0
        THEN 0
        ELSE (NULLIF(
            (COALESCE(s.april, 0) + COALESCE(s.mei, 0) + COALESCE(s.juni, 0) +
             COALESCE(s.juli, 0) + COALESCE(s.agustus, 0) + COALESCE(s.september, 0) +
             COALESCE(s.oktober, 0) + COALESCE(s.november, 0) + COALESCE(s.desember, 0)
            ), 0) /
            NULLIF(
                (CASE WHEN s.april <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.mei <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.juni <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.juli <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.agustus <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.september <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.oktober <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.november <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.desember <> 0 THEN 1 ELSE 0 END), 0) * uc.price::NUMERIC
        )
    END) AS value9,
     CASE
        WHEN (COALESCE(s.oktober, 0) + COALESCE(s.november, 0) + COALESCE(s.desember, 0)) = 0
        THEN 0
        ELSE (NULLIF(
            (COALESCE(s.juli, 0) + COALESCE(s.agustus, 0) + COALESCE(s.september, 0) +
             COALESCE(s.oktober, 0) + COALESCE(s.november, 0) + COALESCE(s.desember, 0)
            ), 0) /
            NULLIF(
                (CASE WHEN s.juli <> 0 THEN 1 ELSE 0 END + 
                 CASE WHEN s.agustus <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.september <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.oktober <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.november <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.desember <> 0 THEN 1 ELSE 0 END), 0)
        )
    END AS unit6,
    -- Yearly Average 6-Month Value (Yearly Average 6-Month Unit * Price)
    (CASE
        WHEN (COALESCE(s.oktober, 0) + COALESCE(s.november, 0) + COALESCE(s.desember, 0)) = 0
        THEN 0
        ELSE (NULLIF(
            (COALESCE(s.juli, 0) + COALESCE(s.agustus, 0) + COALESCE(s.september, 0) +
             COALESCE(s.oktober, 0) + COALESCE(s.november, 0) + COALESCE(s.desember, 0)
            ), 0) /
            NULLIF(
                (CASE WHEN s.juli <> 0 THEN 1 ELSE 0 END + 
                 CASE WHEN s.agustus <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.september <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.oktober <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.november <> 0 THEN 1 ELSE 0 END +
                 CASE WHEN s.desember <> 0 THEN 1 ELSE 0 END), 0) * uc.price::NUMERIC
        )
    END) AS value6,
     (COALESCE(s.oktober, 0) + COALESCE(s.november, 0) + COALESCE(s.desember, 0)) / 3.0 AS unit3,
    -- Yearly Average 3-Month Value (Average 3-Month Unit * Price)
    ((COALESCE(s.oktober, 0) + COALESCE(s.november, 0) + COALESCE(s.desember, 0)) / 3.0) * uc.price::NUMERIC AS value3,
    CASE
        WHEN (COALESCE(s.oktober, 0) + COALESCE(s.november, 0) + COALESCE(s.desember, 0)) = 0
        THEN 0
        ELSE COALESCE(st.desember1, 0) / ((COALESCE(s.oktober, 0) + COALESCE(s.november, 0) + COALESCE(s.desember, 0)) / 3.0) * 30
    END AS doi3bulan
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
JOIN m__cabangs mcb ON s.kode_cabang = mcb.kode_cabang ),
trend_calculation AS (
SELECT *,
    CASE
        WHEN unit12 < 0 AND unit9 < 0 AND unit6 < 0 AND unit3 < 0 THEN 'TREND TAK BERATURAN'
        WHEN unit12 > unit9 AND unit9 < unit6 AND unit6 < unit3 THEN 'TREND NAIK'
        WHEN unit12 < unit9 AND unit9 < unit6 AND unit6 < unit3 THEN 'TREND PROGRESIF NAIK'
        WHEN unit12 > unit9 AND unit9 > unit6 AND unit6 > unit3 THEN 'TREND PROGRESIF TURUN'
        WHEN unit12 < unit9 AND unit9 > unit6 AND unit6 > unit3 THEN 'TREND TURUN'
        ELSE 'TREND TAK BERATURAN'
    END AS status_trend,
    CASE 
        WHEN unit12 != 0 THEN ((unit3 - unit12) / unit12) * 100
        ELSE 0 
    END AS delta
FROM calculated_averages )
SELECT *,
    CASE
        WHEN status_trend = 'TREND TAK BERATURAN' AND delta < 0 AND doi3bulan < 45 THEN 'SCM/SALES'
        WHEN status_trend = 'TREND PROGRESIF TURUN' AND delta < 0 AND doi3bulan < 45 THEN 'SALES'
        WHEN status_trend = 'TREND TURUN' AND delta < 0 AND doi3bulan < 45 THEN 'SALES'
        WHEN status_trend = 'TREND TAK BERATURAN' AND delta < 0 AND doi3bulan > 45 THEN 'MARKETING'
        WHEN status_trend = 'TREND TURUN' AND delta < 0 AND doi3bulan > 45 THEN 'MARKETING'
        WHEN status_trend = 'TREND PROGRESIF TURUN' AND delta < 0 AND doi3bulan > 45 THEN 'MARKETING'
        WHEN status_trend = 'TREND NAIK' AND delta < 0 AND doi3bulan < 45 THEN 'SCM'
        WHEN status_trend = 'TREND NAIK' AND delta < 0 AND doi3bulan > 45 THEN 'MARKETING'
        WHEN status_trend = 'TREND NAIK' AND delta > 0 AND doi3bulan < 45 THEN 'SALES'
        ELSE ''
    END AS pic
FROM trend_calculation;
        ");

        // Insert data ke tabel `trends`
        foreach ($data as $row) {
            DB::table('trends')->insert([
                'dist_code' => $row->dist_code,
                'chnl_code' => $row->chnl_code,
                'region_name' => $row->region_name,
                'area_name' => $row->area_name,
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
                'unit12' => $row->unit12,
                'value12' => $row->value12,
                'unit9' => $row->unit9,
                'value9' => $row->value9,
                'unit6' => $row->unit6,
                'value6' => $row->value6,
                'unit3' => $row->unit3,
                'value3' => $row->value3,
                'beli_januari' => $row->beli_januari,
                'januari1' => $row->januari1,
                'beli_februari' => $row->beli_februari,
                'februari1' => $row->februari1,
                'beli_maret' => $row->beli_maret,
                'maret1' => $row->maret1,
                'beli_april' => $row->beli_april,
                'april1' => $row->april1,
                'beli_mei' => $row->beli_mei,
                'mei1' => $row->mei1,
                'beli_juni' => $row->beli_juni,
                'juni1' => $row->juni1,
                'beli_juli' => $row->beli_juli,
                'juli1' => $row->juli1,
                'beli_agustus' => $row->beli_agustus,
                'agustus1' => $row->agustus1,
                'beli_september' => $row->beli_september,
                'september1' => $row->september1,
                'beli_oktober' => $row->beli_oktober,
                'oktober1' => $row->oktober1,
                'beli_november' => $row->beli_november,
                'november1' => $row->november1,
                'beli_desember' => $row->beli_desember,
                'desember1' => $row->desember1,
                'doi3bulan' => $row->doi3bulan,
                'status_trend' => $row->status_trend,
                'delta' => $row->delta,
                'pic' => $row->pic,
                'average_sales' => $row->average_sales,
                'purchase_suggestion' => $row->purchase_suggestion,
                'purchase_value' => $row->purchase_value,
            ]);
        }

        return "Data berhasil dimasukkan ke tabel trends.";
    }
}
