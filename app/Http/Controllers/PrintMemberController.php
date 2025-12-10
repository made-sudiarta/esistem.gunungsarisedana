<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PrintMemberController extends Controller
{
    public function index(Request $request)
    {
        // $members = Member::orderBy('nia', 'ASC')->get(); // Atau tambahkan filter sesuai kebutuhan
        // return view('print.members', compact('members'));

        $filters = $request->input('filters', []);
        $query = Member::query();

        if (!empty($filters['jenis_id'])) {
            $jenisFilter = $filters['jenis_id'];

            // Jika multiple
            if (!empty($jenisFilter['values'])) {
                $query->whereIn('jenis_id', $jenisFilter['values']);
            }
            // Jika single (hanya 'value')
            elseif (!empty($jenisFilter['value'])) {
                $query->where('jenis_id', $jenisFilter['value']);
            }
        }

        $members = $query->with('jenis')->get();

        // Jika nanti kamu mau tambahkan filter lain, bisa lanjut di sini

        return view('prints.members', compact('members'));
    }
    
}
