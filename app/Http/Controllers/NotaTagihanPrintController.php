<?php
namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Pesanan;
use App\Models\Pelanggan;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;


class NotaTagihanPrintController extends Controller
{
    public function cetak($id)
    {
        $pesanan = Pesanan::with(['pelanggan', 'pesananDetails', 'pembayarans'])->findOrFail($id);

        $details = $pesanan->pesananDetails;
        $total = $details->sum(fn ($item) => $item->jumlah * $item->harga);

        // Total bayar pelanggan pada tahun berjalan
        $tahun = Carbon::parse($pesanan->tanggal)->year;
        $totalBayar = Pembayaran::whereHas('pesanan', function ($query) use ($pesanan) {
            $query->where('kode_plg', $pesanan->pelanggan_id);
        })->whereYear('tanggal_bayar', $tahun)->sum('jumlah_bayar');

        // Total tagihan pelanggan pada tahun berjalan
        $totalTagihan = Pesanan::where('kode_plg', $pesanan->pelanggan_id)
            ->whereYear('tanggal', $tahun)
            ->sum('total');

        // Sisa tagihan
        $sisa = $totalTagihan - $totalBayar;

        return view('cetak.nota-tagihan', compact('pesanan', 'details', 'total', 'totalBayar', 'sisa'));
    }

    public function cetakPDF($noFaktur)
    {
        $pesanan = Pesanan::with(['pelanggan', 'pesananDetails.produk', 'pembayarans'])
            ->where('no_faktur', $noFaktur)
            ->firstOrFail();

        $total = $pesanan->pesananDetails->sum(fn ($item) => $item->jumlah * $item->harga);

        // Total bayar pelanggan pada tahun berjalan
        $tahun = Carbon::parse($pesanan->tanggal)->year;
        $totalBayar = Pembayaran::whereHas('pesanan', function ($query) use ($pesanan) {
            $query->where('kode_plg', $pesanan->pelanggan_id);
        })->whereYear('tanggal_bayar', $tahun)->sum('jumlah_bayar');

        // Total tagihan pelanggan pada tahun berjalan
        $totalTagihan = Pesanan::where('kode_plg', $pesanan->pelanggan_id)
            ->whereYear('tanggal', $tahun)
            ->with('pesananDetails')
            ->get()
            ->sum(function ($item) {
                return $item->pesananDetails->sum(fn ($detail) => $detail->jumlah * $detail->harga);
            });

        // Sisa tagihan
        $sisa = $totalTagihan - $totalBayar;

        $pdf = Pdf::loadView('exports.nota', compact('pesanan', 'total', 'totalBayar', 'sisa'));

        return $pdf->stream('NotaTagihan-' . $noFaktur . '.pdf');
    }

    public function cetakSemua(Request $request)
    {
        $query = Pesanan::with(['pelanggan', 'pembayarans']);

        if ($request->has('tableFilters.kode_plg.value')) {
            $kodePlg = $request->input('tableFilters.kode_plg.value');
            $query->where('kode_plg', $kodePlg);
        }

        $pesanans = $query->get();

        $totalTagihan = $pesanans->sum('total_tagihan');
        $totalBayar = $pesanans->sum('total_bayar');
        $sisaTagihan = $totalTagihan - $totalBayar;

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('cetak.nota-tagihan-list', compact(
            'pesanans', 'totalTagihan', 'totalBayar', 'sisaTagihan'
        ));

        return $pdf->stream('daftar-tagihan.pdf');
    }

    public function cetakFiltered(Request $request)
    {
        // 1. Validasi input, pastikan 'ids' ada dan merupakan array
        $validated = $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'integer', // Pastikan semua isinya adalah angka
        ]);

        $pesananIds = $validated['ids'];

        // 2. Ambil data dari database HANYA untuk ID yang dikirim
        // Sama seperti di resource, kita perlu menghitung total bayar.
        // Cara paling mudah adalah dengan mengulang query dari resource.
        $pesanans = Pesanan::whereIn('id', $pesananIds)
            ->with('pelanggan')
            ->select('pesanans.*')
            ->selectRaw('(SELECT COALESCE(SUM(jumlah_bayar), 0) FROM pembayarans WHERE pembayarans.pesanan_id = pesanans.id) as total_bayar')
            ->get(); // Gunakan get() karena kita sudah punya semua ID

        // 3. Hitung ringkasan total dari koleksi yang sudah didapat
        $totalTagihan = $pesanans->sum('total_tagihan');
        $totalBayar = $pesanans->sum('total_bayar');
        $sisaTagihan = $totalTagihan - $totalBayar;

            // Muat view ke dalam PDF menggunakan compact()
            // Pastikan semua nama variabel di dalam compact() ada.
        $pdf = Pdf::loadView('cetak.nota-tagihan-list', compact(
                'pesanans',
                'totalTagihan',
                'totalBayar',
                'sisaTagihan'
            ));

            $fileName = 'daftar-tagihan-' . date('Y-m-d') . '.pdf';
            return $pdf->stream($fileName);
    }
}
