<?php 
namespace App\Http\Controllers;

use App\Models\Member; // ganti sesuai model kamu
use Illuminate\Http\Request;

class CetakMemberController extends Controller
{
    public function show(Member $record)
    {
        return view('print.member-card', compact('record'));
    }
}
