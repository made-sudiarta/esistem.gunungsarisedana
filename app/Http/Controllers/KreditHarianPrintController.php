<?php

namespace App\Http\Controllers;

use App\Models\KreditHarian;
use Illuminate\Database\Eloquent\Builder;
use Filament\Facades\Filament;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class KreditHarianPrintController extends Controller
{
    public function index()
    {
        $user = Filament::auth()->user();
        $today = Carbon::today();

        $query = KreditHarian::query()
            // ⛔ EXCLUDE YANG LUNAS
            ->where('sisa_pokok', '>', 0);

        // Role-based group
        if (! $user->hasRole('super_admin')) {
            $query->whereHas('group', function (Builder $q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        $data = $query
            ->orderBy('tanggal_pengajuan')
            ->get()
            // HITUNG STATUS DINAMIS
            ->map(function ($row) use ($today) {
                $jatuhTempo = Carbon::parse($row->tanggal_pengajuan)
                    ->addDays($row->jangka_waktu);

                $row->status = $jatuhTempo->lt($today)
                    ? 'jatuh tempo'
                    : 'aktif';

                return $row;
            });

        return view('prints.kredit-harian.index', compact('data'));
    }
    public function akad(KreditHarian $record)
    {
        $record->load([
            'member',
            'group.employees.members',
        ]);

        $pdf = Pdf::loadView('pdf.kredit-harian-akad', [
            'record' => $record,
        ])->setPaper('a4', 'portrait');

        return $pdf->stream('akad-kredit-' . $record->id . '.pdf');
    }
}
