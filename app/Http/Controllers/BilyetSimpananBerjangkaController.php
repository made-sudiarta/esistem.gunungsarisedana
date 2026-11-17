<?php

namespace App\Http\Controllers;

use App\Models\SimpananBerjangka;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class BilyetSimpananBerjangkaController extends Controller
{
    public function cetak(SimpananBerjangka $simpananBerjangka)
    {
        // Ambil data simpanan beserta relasi anggota
        $simpanan = SimpananBerjangka::with(['member', 'group'])
            ->findOrFail($simpananBerjangka->id);

        // Tanggal penempatan
        $tanggalMasuk = Carbon::parse(
            $simpanan->tanggal_masuk
        );

        // Tanggal jatuh tempo
        $tanggalJatuhTempo = $tanggalMasuk->copy()
            ->addMonths((int) $simpanan->jangka_waktu);

        // Terbilang nominal
        $terbilangNominal = trim(
            $this->terbilang((int) $simpanan->nominal)
        );

        $terbilangNominal = ucfirst(
            $terbilangNominal
        ) . ' rupiah';

        // Render Blade PDF
        $pdf = Pdf::loadView(
            'pdf.bilyet-simpanan-berjangka',
            compact(
                'simpanan',
                'tanggalMasuk',
                'tanggalJatuhTempo',
                'terbilangNominal'
            )
        );

        /*
        |--------------------------------------------------------------------------
        | UKURAN KERTAS CUSTOM
        |--------------------------------------------------------------------------
        |
        | Lebar  : 110 mm (11 cm)
        | Tinggi : 212 mm (21,2 cm)
        |
        */

        $width = 212 * 72 / 25.4;
        $height = 110 * 72 / 25.4;

        $pdf->setPaper([0, 0, $width, $height]);
           

        // Tampilkan PDF di browser
        return $pdf->stream(
            'Bilyet-' . $simpanan->kode_bilyet . '.pdf'
        );
    }

    private function terbilang(int $angka): string
    {
        $angka = abs($angka);

        $kata = [
            '',
            'satu',
            'dua',
            'tiga',
            'empat',
            'lima',
            'enam',
            'tujuh',
            'delapan',
            'sembilan',
            'sepuluh',
            'sebelas',
        ];

        if ($angka < 12) {
            return $kata[$angka];
        }

        if ($angka < 20) {
            return $this->terbilang($angka - 10) . ' belas';
        }

        if ($angka < 100) {
            return $this->terbilang(intval($angka / 10))
                . ' puluh '
                . $this->terbilang($angka % 10);
        }

        if ($angka < 200) {
            return 'seratus ' . $this->terbilang($angka - 100);
        }

        if ($angka < 1000) {
            return $this->terbilang(intval($angka / 100))
                . ' ratus '
                . $this->terbilang($angka % 100);
        }

        if ($angka < 2000) {
            return 'seribu ' . $this->terbilang($angka - 1000);
        }

        if ($angka < 1000000) {
            return $this->terbilang(intval($angka / 1000))
                . ' ribu '
                . $this->terbilang($angka % 1000);
        }

        if ($angka < 1000000000) {
            return $this->terbilang(intval($angka / 1000000))
                . ' juta '
                . $this->terbilang($angka % 1000000);
        }

        if ($angka < 1000000000000) {
            return $this->terbilang(intval($angka / 1000000000))
                . ' miliar '
                . $this->terbilang($angka % 1000000000);
        }

        return 'nominal terlalu besar';
    }
}
