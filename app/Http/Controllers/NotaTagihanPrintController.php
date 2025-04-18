<?php
namespace App\Http\Controllers;

use App\Models\Pesanan;
use Barryvdh\DomPDF\Facade\Pdf;

class NotaTagihanPrintController extends Controller
{
    public function cetak($id)
    {
        $pesanan = Pesanan::with(['pelanggan', 'pesananDetails', 'pembayaran'])->findOrFail($id);

        $details = $pesanan->pesananDetails;
        $total = $details->sum(fn ($item) => $item->jumlah * $item->harga);
        $totalBayar = $pesanan->totalPembayaran();
        $sisa = $total - $totalBayar;

        return view('cetak.nota-tagihan', compact('pesanan', 'details', 'total', 'totalBayar', 'sisa'));
    }

    public function cetakPDF($noFaktur)
    {
        $pesanan = Pesanan::with(['pelanggan', 'pesananDetails.bahanjadi', 'pembayaran'])
            ->where('no_faktur', $noFaktur)
            ->firstOrFail();

        $total = $pesanan->pesananDetails->sum(fn ($item) => $item->jumlah * $item->harga);
        $totalBayar = $pesanan->totalPembayaran();
        $sisa = $total - $totalBayar;

        $pdf = Pdf::loadView('exports.nota', compact('pesanan', 'total', 'totalBayar', 'sisa'));

        return $pdf->stream('NotaTagihan-' . $noFaktur . '.pdf');
    }
}
