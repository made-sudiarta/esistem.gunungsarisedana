<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PrintMemberController;
use App\Http\Controllers\PrintController;
use App\Http\Controllers\CetakMemberController;
use App\Http\Controllers\PrintSimpananBerjangkaController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    // return view('welcome');
    return redirect('/admin');
});
// Route::get('/dashboard', function (){
//     return redirect('/admin');
// });
Route::get('/print-members', [PrintMemberController::class, 'index'])->name('prints.members');

Route::get('/members/print/{member}', [PrintController::class, 'print'])->name('print.members');
Route::get('/print/member/{record}', [CetakMemberController::class, 'show'])->name('print.member');
Route::get('/print/members', [PrintController::class, 'printAll'])->name('print.members.all');

Route::get('/print/simpanan-berjangka/{record}', [PrintSimpananBerjangkaController::class, 'show'])
    ->name('print.simpanan-berjangka.show');

Route::get('/print-simpanan-berjangka', [App\Http\Controllers\PrintSimpananBerjangkaController::class, 'index'])
    ->name('print.simpanan-berjangka.index');
Route::get('/simpanan-berjangka/print-struk', 
    [\App\Http\Controllers\PrintSimpananBerjangkaController::class, 'printStruk']
)->name('simpanan-berjangka.print-struk');

Route::get('/cetak-pilih-struk', [PrintSimpananBerjangkaController::class, 'cetakPilihStruk'])
    ->name('cetak-pilih-struk');


Route::get('/prints/struk/{id}', [PrintSimpananBerjangkaController::class, 'cetakStruk'])
    ->name('cetak-struk');

Route::get('/prints/struk-bulk', [PrintSimpananBerjangkaController::class, 'cetakStrukBulk'])
    ->name('cetak-struk-bulk');


