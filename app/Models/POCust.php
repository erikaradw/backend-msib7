<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class POCust extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $table = 'p_o_custs'; // Nama tabel sesuai dengan migration
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
    public function get_data_($search, $arr_pagination, $request)
    {
        $search = strtolower($search);

        // Mulai query
        $query = POCust::whereRaw("
            (lower(dist_code) like '%$search%'
            OR lower(tgl_order) like '%$search%'
            OR lower(mtg_code) like '%$search%'
            OR lower(qty_sc_reg) like '%$search%'
            OR lower(qty_po) like '%$search%'
            OR lower(branch_code) like '%$search%') 
            AND deleted_by IS NULL
        ");

        // Tambahkan filter jika parameter tersedia
        if (!empty($request->tahun) && is_numeric($request->tahun)) {
            $query->whereRaw("EXTRACT(YEAR FROM TO_DATE(tgl_order, 'MM/DD/YYYY')) = ?", [$request->tahun]);
        }

        if (!empty($request->bulan) && is_numeric($request->bulan)) {
            $query->whereRaw("EXTRACT(MONTH FROM TO_DATE(tgl_order, 'MM/DD/YYYY')) = ?", [$request->bulan]);
        }

        if (!empty($request->dist_code)) {
            $query->where('dist_code', 'like', '%' . $request->dist_code . '%');
        }

        // Eksekusi query dengan paginasi
        $data = $query->select('id', 'dist_code', 'tgl_order', 'mtg_code', 'qty_sc_reg', 'qty_po', 'branch_code')
            ->offset($arr_pagination['offset']) // Menghormati nilai offset dari pagination
            ->limit($arr_pagination['limit'])
            ->orderBy('id', 'ASC')
            ->get();

        return $data;
    }

    public function count_data_($search, $request)
    {
        $search = strtolower($search);

        // Mulai query
        $query = POCust::whereRaw("
        (lower(dist_code) like '%$search%'
        OR lower(tgl_order) like '%$search%'
        OR lower(mtg_code) like '%$search%'
        OR lower(qty_sc_reg) like '%$search%'
        OR lower(qty_po) like '%$search%'
        OR lower(branch_code) like '%$search%') 
        AND deleted_by IS NULL
    ");

        // Tambahkan filter jika parameter tersedia
        if (!empty($request->tahun) && is_numeric($request->tahun)) {
            $query->whereRaw("EXTRACT(YEAR FROM TO_DATE(tgl_order, 'MM/DD/YYYY')) = ?", [$request->tahun]);
        }

        if (!empty($request->bulan) && is_numeric($request->bulan)) {
            $query->whereRaw("EXTRACT(MONTH FROM TO_DATE(tgl_order, 'MM/DD/YYYY')) = ?", [$request->bulan]);
        }

        if (!empty($request->dist_code)) {
            $query->where('dist_code', 'like', '%' . $request->dist_code . '%');
        }

        // Hitung jumlah data
        $data = $query->count();

        return $data;
    }

    // public function get_data_($search, $arr_pagination, $request)
    // {
    //     // Jika ada pencarian, reset offset pagination ke 0
    //     if (!empty($search)) {
    //         $arr_pagination['offset'] = 0;
    //     }

    //     $search = strtolower($search);

    //     // Mulai query
    //     $query = POCust::whereRaw("
    //         (lower(dist_code) like '%$search%'
    //         OR lower(tgl_order) like '%$search%'
    //         OR lower(mtg_code) like '%$search%'
    //         OR lower(qty_sc_reg) like '%$search%'
    //         OR lower(qty_po) like '%$search%'
    //         OR lower(branch_code) like '%$search%') 
    //         AND deleted_by IS NULL
    //     ");

    //     // Tambahkan filter jika parameter tersedia
    //     if (!empty($request->tahun) && is_numeric($request->tahun)) {
    //         $query->whereRaw("EXTRACT(YEAR FROM TO_DATE(tgl_order, 'MM/DD/YYYY')) = ?", [$request->tahun]);
    //     }

    //     if (!empty($request->bulan) && is_numeric($request->bulan)) {
    //         $query->whereRaw("EXTRACT(MONTH FROM TO_DATE(tgl_order, 'MM/DD/YYYY')) = ?", [$request->bulan]);
    //     }

    //     if (!empty($request->dist_code)) {
    //         $query->where('dist_code', 'like', '%' . $request->dist_code . '%');
    //     }

    //     // Eksekusi query dengan paginasi
    //     $data = $query->select('id', 'dist_code', 'tgl_order', 'mtg_code', 'qty_sc_reg', 'qty_po', 'branch_code')
    //         ->offset($arr_pagination['offset'])
    //         ->limit($arr_pagination['limit'])
    //         ->orderBy('id', 'ASC')
    //         ->get();

    //     return $data;
    // }

    // public function count_data_($search, $request)
    // {
    //     $search = strtolower($search);

    //     // Mulai query
    //     $query = POCust::whereRaw("
    //     (lower(dist_code) like '%$search%'
    //     OR lower(tgl_order) like '%$search%'
    //     OR lower(mtg_code) like '%$search%'
    //     OR lower(qty_sc_reg) like '%$search%'
    //     OR lower(qty_po) like '%$search%'
    //     OR lower(branch_code) like '%$search%') 
    //     AND deleted_by IS NULL
    // ");

    //     // Tambahkan filter jika parameter tersedia
    //     if (!empty($request->tahun) && is_numeric($request->tahun)) {
    //         $query->whereRaw("EXTRACT(YEAR FROM TO_DATE(tgl_order, 'MM/DD/YYYY')) = ?", [$request->tahun]);
    //     }

    //     if (!empty($request->bulan) && is_numeric($request->bulan)) {
    //         $query->whereRaw("EXTRACT(MONTH FROM TO_DATE(tgl_order, 'MM/DD/YYYY')) = ?", [$request->bulan]);
    //     }

    //     if (!empty($request->dist_code)) {
    //         $query->where('dist_code', 'like', '%' . $request->dist_code . '%');
    //     }

    //     // Hitung jumlah data
    //     $data = $query->count();

    //     return $data;
    // }
}
