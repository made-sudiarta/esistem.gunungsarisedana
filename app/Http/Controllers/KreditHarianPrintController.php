<?php

namespace App\Http\Controllers;

use App\Models\KreditHarian;
use Illuminate\Database\Eloquent\Builder;
use Filament\Facades\Filament;

class KreditHarianPrintController extends Controller
{
    public function index()
    {
        $user = Filament::auth()->user();

        $query = KreditHarian::query();

        // Super Admin → semua
        if (! $user->hasRole('super_admin')) {
            $query->whereHas('group', function (Builder $q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        $data = $query->orderBy('tanggal_pengajuan')->get();

        return view('prints.kredit-harian.index', compact('data'));
    }
}
