<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SlipGajiController;
use App\Http\Controllers\NotaTagihanPrintController;
use App\Http\Controllers\NotaTagihanController;
use Illuminate\Http\Request;
use App\Filament\Resources\NotaTagihanResource;

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
    return view('welcome');
});

//Route::get('/slip-gaji/{id}', [SlipGajiController::class, 'show'])->name('slip-gaji.pdf');
Route::get('/slip-gaji/{id}/pdf', [SlipGajiController::class, 'downloadPdf'])->name('slip-gaji.pdf');
Route::get('/slip-gaji/bulan/{bulan}', [SlipGajiController::class, 'cetakBulanan'])
    ->name('slip-gaji.bulan');
Route::get('/nota-tagihan/{noFaktur}/cetak', [NotaTagihanPrintController::class, 'cetakPDF'])
    ->name('nota-tagihan.cetak');
Route::get('/phpinfo', function () {
        phpinfo();
    });
Route::get('/nota-tagihan/cetak-semua/{pelanggan}', [NotaTagihanController::class, 'cetakSemua'])->name('nota-tagihan.cetak-semua');
Route::get('/nota-tagihan/cetak-bulk/{no_fakturs}', [NotaTagihanController::class, 'cetakBulk'])->name('nota-tagihan.cetak-bulk');
// Route::get('/nota-tagihan/cetak', function (Request $request) {
//     $pelangganId = $request->query('tableFilters')['pelanggan']['value'] ?? null;
//     if ($pelangganId) {
//         // Cetak tagihan berdasarkan pelanggan
//         $notaTagihan = NotaTagihanResource::getEloquentQuery()
//             ->where('kode_plg', $pelangganId)
//             ->get();
//     } else {
//         // Cetak seluruh tagihan
//         $notaTagihan = NotaTagihanResource::getEloquentQuery()->get();
//     }
//     // Return PDF atau action lainnya
//     $pdf = \PDF::loadView('nota_tagihan.pdf', compact('notaTagihan'));
//     return $pdf->download('nota_tagihan.pdf');
// })->name('nota-tagihan.cetak');
