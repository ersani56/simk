<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SlipGajiController;
use App\Http\Controllers\NotaTagihanPrintController;
use App\Http\Controllers\NotaTagihanController;
use Illuminate\Http\Request;
use App\Models\Pesanan;
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
Route::get('/nota-tagihan/cetak-list', function (Request $request) {
    $pelangganId = $request->query('pelanggan_id');
    $notaTagihan = Pesanan::with(['pelanggan', 'pesananDetails.bahanjadi', 'pembayaran']);

    if ($pelangganId) {
        $notaTagihan->where('kode_plg', $pelangganId);
    }

    $notaTagihan = $notaTagihan->get();

    $pdf = Pdf::loadView('cetak.nota-tagihan-list', compact('notaTagihan'));

    return $pdf->stream('NotaTagihanList.pdf');
})->name('nota-tagihan.cetak-list');

Route::get('/nota-tagihan/cetak', function (Request $request) {
    $noFaktur = $request->query('no_faktur');
    $pesanan = Pesanan::with(['pelanggan', 'pesananDetails.bahanjadi', 'pembayaran'])
        ->where('no_faktur', $noFaktur)
        ->firstOrFail();

    $pdf = Pdf::loadView('exports.nota', compact('pesanan'));

    return $pdf->stream('NotaTagihan-' . $noFaktur . '.pdf');
})->name('nota-tagihan.cetak');
