<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PrintMemberController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->input('filters', []);
        $query = Member::query();

        if (!empty($filters['jenis_id'])) {
            $jenisFilter = $filters['jenis_id'];

            if (!empty($jenisFilter['values'])) {
                $query->whereIn('jenis_id', $jenisFilter['values']);
            }
            elseif (!empty($jenisFilter['value'])) {
                $query->where('jenis_id', $jenisFilter['value']);
            }
        }

        // $members = $query->with('jenis')->get();

        $members = $query
                    ->with('jenis')
                    ->orderBy('nia', 'ASC')
                    ->get();
        return view('prints.members', compact('members'));
    }
    
}
