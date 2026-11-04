<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PrintMemberController;
use App\Http\Controllers\PrintController;
use App\Http\Controllers\CetakMemberController;
use App\Http\Controllers\PrintSimpananBerjangkaController;
use App\Http\Controllers\KreditHarianPrintController;
use App\Http\Controllers\KreditBulananPdfController;
use App\Http\Controllers\SuratTagihanKreditPdfController;
use App\Http\Controllers\SuratPenyerahanJaminanPdfController;
use App\Http\Controllers\KuitansiKreditController;
use App\Http\Controllers\KreditHarianKuitansiController;
use App\Models\KreditHarianTransaksi;
use App\Http\Controllers\SimpananPenyertaKuitansiController;
use App\Http\Controllers\SimpananPokokKuitansiController;
use App\Http\Controllers\SimpananWajibKuitansiController;
use App\Http\Controllers\BilyetSimpananBerjangkaController;

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


Route::get(
    '/admin/kredit-harian/print',
    [KreditHarianPrintController::class, 'index']
)->name('kredit-harian.print.index');
Route::get('/kredit-harian/{record}/akad-pdf', [KreditHarianPrintController::class, 'akad'])
    ->name('kredit-harian.akad-pdf');
Route::get('/kredit-bulanan/{record}/akad-pdf', [KreditBulananPdfController::class, 'akad'])
    ->name('kredit-bulanan.akad-pdf');
Route::get('/surat-tagihan-kredit/{record}/pdf', [SuratTagihanKreditPdfController::class, 'show'])
    ->name('surat-tagihan-kredit.pdf');

Route::middleware('auth')->prefix('surat-penyerahan-jaminan')->name('surat-penyerahan-jaminan.')->group(function () {
    Route::get('/{surat}/pernyataan', [
        SuratPenyerahanJaminanPdfController::class,
        'pernyataan',
    ])->name('pernyataan');

    Route::get('/{surat}/berita-acara', [
        SuratPenyerahanJaminanPdfController::class,
        'beritaAcara',
    ])->name('berita-acara');
});

Route::get(
    '/transaksi-kredit-bulanan/{transaksi}/kuitansi',
    [KuitansiKreditController::class, 'cetak']
)->name('transaksi-kredit-bulanan.kuitansi');


Route::get(
    '/kredit-harian/transaksi/{id}/kuitansi',
    [KreditHarianKuitansiController::class, 'cetak']
)->name('kredit-harian.kuitansi');

Route::get(
    '/simpanan-penyerta/{id}/kuitansi',
    [SimpananPenyertaKuitansiController::class, 'cetak']
)->middleware('auth')->name('simpanan-penyerta.kuitansi');


Route::get(
    '/simpanan-pokok/{id}/kuitansi',
    [SimpananPokokKuitansiController::class, 'cetak']
)
    ->middleware('auth')
    ->name('simpanan-pokok.kuitansi');

    Route::get(
    '/simpanan-wajib/{id}/kuitansi',
    [SimpananWajibKuitansiController::class, 'cetak']
)
    ->middleware('auth')
    ->name('simpanan-wajib.kuitansi');

    Route::get(
    '/simpanan-berjangka/{simpananBerjangka}/cetak-bilyet',
    [BilyetSimpananBerjangkaController::class, 'cetak']
)->name('cetak-bilyet');