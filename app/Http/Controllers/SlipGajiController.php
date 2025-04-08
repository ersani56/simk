<?php

namespace App\Http\Controllers;

use App\Models\GajiKaryawan;
use Illuminate\Support\Carbon;
use PDF;

class SlipGajiController extends Controller
{
    public function cetakBulanan($bulan)
    {
        $tanggal = Carbon::parse($bulan . '-01');
        $query = GajiKaryawan::with('karyawan')
            ->whereMonth('tanggal_dibayar', $tanggal->month)
            ->whereYear('tanggal_dibayar', $tanggal->year);

        // Jika bukan admin, hanya tampilkan data karyawan yang login
        if (!auth()->user()->hasRole('admin')) {
            $query->where('karyawan_id', auth()->id());
        }

        $gajiBulanan = $query->get();
        $user = auth()->user();

        return Pdf::loadView('filament.components.gaji-bulanan', [
            'gajiBulanan' => $gajiBulanan,
            'tanggal' => $tanggal,
            'user' => $user,
        ])->stream('Slip-Gaji-' . $tanggal->format('F-Y') . '.pdf');
    }
}
