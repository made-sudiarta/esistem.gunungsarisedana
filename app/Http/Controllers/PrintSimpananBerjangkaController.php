<?php

namespace App\Http\Controllers;
use App\Models\SimpananBerjangka;
use Illuminate\Http\Request;

class PrintSimpananBerjangkaController extends Controller
{
    public function show(SimpananBerjangka $record)
    {
        return view('prints.simpanan-berjangka', [
            'data' => $record
        ]);
    }
//     public function index(Request $request)
// {
//     $query = SimpananBerjangka::query();
//     $today = \Carbon\Carbon::today();

//     // Ambil filter dari Filament
//     $filters = $request->input('filters', []);

//     // Ambil status dari URL (dari card)
//     $status = $request->get('status', 'all');

//     /*
//     |--------------------------------------------------------------------------
//     | 1. FILTER JATUH TEMPO (bulan & tahun sama)
//     |--------------------------------------------------------------------------
//     */
//     if ($status === 'jatuh_tempo') {
//         $query->whereRaw("
//             MONTH(DATE_ADD(tanggal_masuk, INTERVAL jangka_waktu MONTH)) = ?
//             AND YEAR(DATE_ADD(tanggal_masuk, INTERVAL jangka_waktu MONTH)) = ?
//         ", [$today->month, $today->year]);
//     }

//     /*
//     |--------------------------------------------------------------------------
//     | 2. FILTER TENGGAT BUNGA (hari sama)
//     |--------------------------------------------------------------------------
//     */
//     if ($status === 'tenggat_bunga') {
//         $query->whereRaw("
//             DAY(DATE_ADD(tanggal_masuk, INTERVAL jangka_waktu MONTH)) = ?
//         ", [$today->day]);
//     }

//     /*
//     |--------------------------------------------------------------------------
//     | 3. FILTER-FILTER DARI FILAMENT (group, member, trash, dll)
//     |--------------------------------------------------------------------------
//     */
//     if (isset($filters['group_id']['value'])) {
//         $query->where('group_id', $filters['group_id']['value']);
//     }

//     if (isset($filters['member_id']['value'])) {
//         $query->where('member_id', $filters['member_id']['value']);
//     }

//     if (isset($filters['trashed']['value']) && $filters['trashed']['value'] === 'only') {
//         $query->onlyTrashed();
//     } elseif (isset($filters['trashed']['value']) && $filters['trashed']['value'] === 'with') {
//         $query->withTrashed();
//     }

//     // Ambil hasil akhir
//     $records = $query->get();

//     return view('prints.data-simpanan-berjangka', compact('records'));
// }
    // public function index(Request $request)
    // {
    //     $query = SimpananBerjangka::query();
    //     $today = \Carbon\Carbon::today();

    //     $filters = $request->input('filters', []);
    //     $status = $request->get('status', 'all');

    //     // Default title
    //     $title = 'Data Simpanan Berjangka';

    //     /*
    //     |--------------------------------------------------------------------------
    //     | 1. FILTER JATUH TEMPO
    //     |--------------------------------------------------------------------------
    //     */
    //     if ($status === 'jatuh_tempo') {
    //         $query->whereRaw("
    //             MONTH(DATE_ADD(tanggal_masuk, INTERVAL jangka_waktu MONTH)) = ?
    //             AND YEAR(DATE_ADD(tanggal_masuk, INTERVAL jangka_waktu MONTH)) = ?
    //         ", [$today->month, $today->year]);

    //         $title = 'Data Jatuh Tempo Bulan Ini';
    //     }

    //     /*
    //     |--------------------------------------------------------------------------
    //     | 2. FILTER TENGGAT BUNGA
    //     |--------------------------------------------------------------------------
    //     */
    //     if ($status === 'tenggat_bunga') {
    //         $query->whereRaw("
    //             DAY(DATE_ADD(tanggal_masuk, INTERVAL jangka_waktu MONTH)) = ?
    //         ", [$today->day]);

    //         $title = 'Cetak Bunga Hari Ini';
    //     }

    //     /*
    //     |--------------------------------------------------------------------------
    //     | 3. FILTER FILAMENT LAINNYA
    //     |--------------------------------------------------------------------------
    //     */
    //     if (!empty($filters['group_id']['value'])) {
    //         $query->where('group_id', $filters['group_id']['value']);
    //         $title = 'Data Berdasarkan Group';
    //     }

    //     if (!empty($filters['member_id']['value'])) {
    //         $query->where('member_id', $filters['member_id']['value']);
    //         $title = 'Data Berdasarkan Anggota';
    //     }

    //     if (!empty($filters['trashed']['value']) && $filters['trashed']['value'] === 'only') {
    //         $query->onlyTrashed();
    //         $title = 'Data Terhapus';
    //     } elseif (!empty($filters['trashed']['value']) && $filters['trashed']['value'] === 'with') {
    //         $query->withTrashed();
    //         $title = 'Data Termasuk Terhapus';
    //     }

    //     $records = $query->get();

    //     return view('prints.data-simpanan-berjangka', compact('records', 'title'));
    // }
    public function index(Request $request)
    {
        $query = SimpananBerjangka::query()->with(['group', 'member.jenis']); // pastikan relasi di-load
        $today = \Carbon\Carbon::today();

        $filters = $request->input('filters', []);
        $status = $request->get('status', 'all');

        // Default title
        $title = 'Data Simpanan Berjangka';

        /*
        |--------------------------------------------------------------------------
        | 1. FILTER JATUH TEMPO
        |--------------------------------------------------------------------------
        */
        if ($status === 'jatuh_tempo') {
            $query->whereRaw("
                MONTH(DATE_ADD(tanggal_masuk, INTERVAL jangka_waktu MONTH)) = ?
                AND YEAR(DATE_ADD(tanggal_masuk, INTERVAL jangka_waktu MONTH)) = ?
            ", [$today->month, $today->year]);

            $title = 'Data Jatuh Tempo Bulan Ini';
        }

        /*
        |--------------------------------------------------------------------------
        | 2. FILTER TENGGAT BUNGA
        |--------------------------------------------------------------------------
        */
        if ($status === 'tenggat_bunga') {
            $query->whereRaw("
                DAY(DATE_ADD(tanggal_masuk, INTERVAL jangka_waktu MONTH)) = ?
            ", [$today->day]);

            $title = 'Cetak Bunga Hari Ini';
        }

        /*
        |--------------------------------------------------------------------------
        | 3. FILTER FILAMENT LAINNYA
        |--------------------------------------------------------------------------
        */
        if (!empty($filters['group_id']['value'])) {
            // $query->where('group_id', $filters['group_id']['value']);
            $groupId = $filters['group_id']['value'];
            $query->where('group_id', $groupId);

            // ambil nama group
            $groupName = \App\Models\Group::where('id', $groupId)->value('group'); // ganti 'group' sesuai kolom nama group
            
            $title = 'Data Simpanan Berjangka Group '.$groupName ?? '-';
        }

        if (!empty($filters['member_id']['value'])) {
            $memberId = $filters['member_id']['value'];
            $query->where('member_id', $memberId);

            // ambil nama anggota
            $memberName = \App\Models\Member::where('id', $memberId)->value('nama_lengkap');
            $title = 'Data Simpanan Berjangka Anggota ' . ($memberName ?? '-');
        }

        if (!empty($filters['trashed']['value']) && $filters['trashed']['value'] === 'only') {
            $query->onlyTrashed();
            $title = 'Data Terhapus';
        } elseif (!empty($filters['trashed']['value']) && $filters['trashed']['value'] === 'with') {
            $query->withTrashed();
            $title = 'Data Termasuk Terhapus';
        }

        // **Urut berdasarkan nama_lengkap ascending**
        $query->orderBy('nama_lengkap', 'asc');

        $records = $query->get();

        return view('prints.data-simpanan-berjangka', compact('records', 'title'));
    }


    public function printStruk(Request $request)
    {
        // ambil data yang ingin dicetak
        $today = \Carbon\Carbon::today();

        // ambil 5 record berdasarkan tanggal jatuh tempo hari ini
        $records = SimpananBerjangka::whereRaw(
            'DAY(DATE_ADD(tanggal_masuk, INTERVAL jangka_waktu MONTH)) = ?', 
            [$today->day]
        )
        // ->limit(10)
        ->get();

        // jika Anda ingin memakai input ID:
        // $records = SimpananBerjangka::whereIn('id', $request->input('ids', []))->get();

        $struks = $records;
        $title = 'Struk Pengambilan Bunga - '.Date('d M Y');
        $count  = $records->count();

        $bungatotal = $records->sum(function ($item) {
            return $item->nominal * (($item->bunga_persen / 100) / 12);
        });

        return view('prints.struk-pengambilan-bunga', compact('struks','title','count','bungatotal'));
    }

    public function cetakPilihStruk(Request $request)
    {
        // $from = \Carbon\Carbon::parse($request->tanggal_dari);
        // $to   = \Carbon\Carbon::parse($request->tanggal_sampai);
        $from = \Carbon\Carbon::parse($request->tanggal_dari)->startOfDay();
        $to   = \Carbon\Carbon::parse($request->tanggal_sampai)->endOfDay();

        $records = \App\Models\SimpananBerjangka::get()->filter(function ($item) use ($from, $to) {

            $start = \Carbon\Carbon::parse($item->tanggal_masuk);
            $jangka = intval($item->jangka_waktu);

            // Jatuh bunga pertama: 1 bulan setelah masuk
            $jatuh = $start->copy()->addMonth();

            for ($i = 1; $i <= $jangka; $i++) {

                // Sesuaikan tanggal jika tidak ada (contoh 31 → 30)
                $targetDay = $start->day;
                $lastDayOfMonth = $jatuh->copy()->endOfMonth()->day;

                if ($targetDay > $lastDayOfMonth) {
                    $jatuh->day($lastDayOfMonth);
                } else {
                    $jatuh->day($targetDay);
                }

                // Jika jatuh bunga berada dalam range
                if ($jatuh->between($from, $to)) {
                    return true;
                }

                // Tambah bulan untuk iterasi berikutnya
                $jatuh->addMonth();
            }

            return false;
        });

        $bungatotal = $records->sum(function ($item) {
            return $item->nominal * (($item->bunga_persen / 100) / 12);
        });

        return view('prints.struk-pengambilan-bunga', [
            'struks' => $records,
            'title'  => 'STRUK ' . $from->format('d-m-Y') . ' s.d ' . $to->format('d-m-Y'),
            'count'  => $records->count(),
            'bungatotal' => $bungatotal,
            
        ]);
    }
    public function cetakStruk($id)
    {
        $data = SimpananBerjangka::findOrFail($id);

        return view('prints.struk-pengambilan-bunga-single', [
            'data' => $data,
        ]);
    }
    public function cetakStrukBulk(Request $request)
    {
        $ids = explode(',', $request->ids);

        $records = SimpananBerjangka::whereIn('id', $ids)->get();

        // Hitung total bunga
        $bungatotal = $records->sum(function ($item) {
            return $item->nominal * (($item->bunga_persen / 100) / 12);
        });

        return view('prints.struk-pengambilan-bunga', [
            'struks'     => $records,
            'title'      => "STRUK TERPILIH",
            'count'      => $records->count(),
            'bungatotal' => $bungatotal,
        ]);
    }





}