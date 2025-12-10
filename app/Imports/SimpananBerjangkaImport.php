<?php

namespace App\Imports;

use App\Models\SimpananBerjangka;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class SimpananBerjangkaImport implements ToCollection
{
    
    public function collection(Collection $rows)
    {
        $rows->skip(1)->each(function ($row) {

            $tanggalMasuk = $this->parseExcelDateSafe($row[4]);

            SimpananBerjangka::create([
                'kode_bilyet' => $row[0],
                'group_id' => $row[1],
                'member_id' => $row[2],
                'nama_lengkap' => $row[3],
                'tanggal_masuk' => $tanggalMasuk,
                'jangka_waktu' => $row[5],
                'bunga_persen' => $row[6],
                'nominal' => $row[7],
            ]);
        });
    }

    protected function parseExcelDateSafe($value)
    {
        // JIKA numeric → pasti convert, tanpa gagal
        if (is_numeric($value)) {
            $dt = ExcelDate::excelToDateTimeObject((float)$value);
            return Carbon::instance($dt)->format('Y-m-d');
        }

        // Jika DateTime object
        if ($value instanceof \DateTime) {
            return $value->format('Y-m-d');
        }

        // Jika string → parse fleksibel
        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Throwable $e) {
            // fallback terakhir
            return now()->format('Y-m-d'); 
        }
    }

}
