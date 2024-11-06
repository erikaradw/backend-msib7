<?php

namespace App\Models;

use DateTimeInterface;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\MStore;
use App\Models\MObjType;
use App\Models\MSettingDoc;


class PublicModel extends Model
{

    public function array_respon_200_table_tr(
        $todos,
        $count,
        $arr_pagination,
        $total_bpitem_null = 0,
        $confirm_approve = null,
        $status_save_PenerimaanBarang = 0
    ) {
        return [
            'status_save_PenerimaanBarang' => $status_save_PenerimaanBarang,
            'total_bpitem_null' => $total_bpitem_null,
            'confirm_approve' => $confirm_approve,
            'nomorBaris' => $arr_pagination['nomorBaris'],
            'count' => $count,
            'next' => $arr_pagination['next'],
            'previous' => $arr_pagination['previous'],
            'results' => $todos,
        ];
    }


    public function array_respon_200_table($todos, $count, $arr_pagination)
    {
        return [
            'nomorBaris' => $arr_pagination['nomorBaris'],
            'count' => $count,
            'next' => $arr_pagination['next'],
            'previous' => $arr_pagination['previous'],
            'results' => $todos,
        ];
    }

    public function pagination_without_search($URL, $limit, $offset)
    {
        $limit = (empty($limit) ? 0 : $limit);
        $offset = (empty($offset) ? 0 : $offset);

        $offset_next = $offset + $limit;
        $offset_previous = $offset - $limit;

        $nomorBaris = $offset;

        $next = $URL . "?offset=$offset_next&limit=$limit";

        if ($offset == 0) {
            $previous = null;
        } else {
            $previous = $URL . "?offset=$offset_previous&limit=$limit";
        }

        return ['nomorBaris' => (int) $nomorBaris, 'next' => $next, 'previous' => $previous, 'limit' => $limit, 'offset' => $offset];
    }


    public function pagination_without_search_legal($URL, $limit, $offset, $userid)
    {
        $limit = (empty($limit) ? 0 : $limit);
        $offset = (empty($offset) ? 0 : $offset);

        $offset_next = $offset + $limit;
        $offset_previous = $offset - $limit;

        $nomorBaris = $offset;

        $next = $URL . "?offset=$offset_next&limit=$limit&userid=$userid";

        if ($offset == 0) {
            $previous = null;
        } else {
            $previous = $URL . "?offset=$offset_previous&limit=$limit&userid=$userid";
        }

        return ['nomorBaris' => (int) $nomorBaris, 'next' => $next, 'previous' => $previous, 'limit' => $limit, 'offset' => $offset];
    }


    public function pagination_with_search($URL, $limit, $offset, $search)
    {
        $limit = (empty($limit) ? 0 : $limit);
        $offset = (empty($offset) ? 0 : $offset);

        $offset_next = $offset + $limit;
        $offset_previous = $offset - $limit;

        $nomorBaris = $offset;

        $next = $URL . "?search=$search&offset=$offset_next&limit=$limit";

        if ($offset == 0) {
            $previous = null;
        } else {
            $previous = $URL . "?search=$search&offset=$offset_previous&limit=$limit";
        }


        return ['nomorBaris' => (int) $nomorBaris, 'next' => $next, 'previous' => $previous, 'limit' => $limit, 'offset' => $offset, 'search' => $search];
    }



    public function pagination_without_search_inventory($URL, $limit, $offset, $storeid)
    {
        $limit = (empty($limit) ? 0 : $limit);
        $offset = (empty($offset) ? 0 : $offset);

        $offset_next = $offset + $limit;
        $offset_previous = $offset - $limit;

        $nomorBaris = $offset;

        $next = $URL . "?offset=$offset_next&limit=$limit&storeid=$storeid";

        if ($offset == 0) {
            $previous = null;
        } else {
            $previous = $URL . "?offset=$offset_previous&limit=$limit&storeid=$storeid";
        }

        return ['nomorBaris' => (int) $nomorBaris, 'next' => $next, 'previous' => $previous, 'limit' => $limit, 'offset' => $offset];
    }


    public function pagination_without_search_docList($URL, $limit, $offset, $flagCbo_main, $flagCbo_sub, $flagCbo_detail)
    {
        $limit = (empty($limit) ? 0 : $limit);
        $offset = (empty($offset) ? 0 : $offset);

        $offset_next = $offset + $limit;
        $offset_previous = $offset - $limit;

        $nomorBaris = $offset;

        $next = $URL . "?offset=$offset_next&limit=$limit&flagCbo_main=$flagCbo_main&flagCbo_sub=$flagCbo_sub&flagCbo_detail=$flagCbo_detail";

        if ($offset == 0) {
            $previous = null;
        } else {
            $previous = $URL . "?offset=$offset_previous&limit=$limit&flagCbo_main=$flagCbo_main&flagCbo_sub=$flagCbo_sub&flagCbo_detail=$flagCbo_detail";
        }

        return ['nomorBaris' => (int) $nomorBaris, 'next' => $next, 'previous' => $previous, 'limit' => $limit, 'offset' => $offset];
    }


    public function pagination_with_search_docList($URL, $limit, $offset, $search, $flagCbo_main, $flagCbo_sub, $flagCbo_detail)
    {
        $limit = (empty($limit) ? 0 : $limit);
        $offset = (empty($offset) ? 0 : $offset);

        $offset_next = $offset + $limit;
        $offset_previous = $offset - $limit;

        $nomorBaris = $offset;

        $next = $URL . "?search=$search&offset=$offset_next&limit=$limit&flagCbo_main=$flagCbo_main&flagCbo_sub=$flagCbo_sub&flagCbo_detail=$flagCbo_detail";

        if ($offset == 0) {
            $previous = null;
        } else {
            $previous = $URL . "?search=$search&offset=$offset_previous&limit=$limit&flagCbo_main=$flagCbo_main&flagCbo_sub=$flagCbo_sub&flagCbo_detail=$flagCbo_detail";
        }


        return ['nomorBaris' => (int) $nomorBaris, 'next' => $next, 'previous' => $previous, 'limit' => $limit, 'offset' => $offset, 'search' => $search];
    }


    public function tgl_indo($tanggal)
    {
        $bulan = array(
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        );
        $pecahkan_tgl = explode(' ', $tanggal);
        $pecahkan = explode('-', $pecahkan_tgl[0]);

        // variabel pecahkan 0 = tanggal
        // variabel pecahkan 1 = bulan
        // variabel pecahkan 2 = tahun

        return $pecahkan[2] . ' ' . $bulan[(int) $pecahkan[1]] . ' ' . $pecahkan[0] . ' ' . $pecahkan_tgl[1];
    }
}
