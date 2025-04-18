<?php

namespace App\Filament\Resources\NotaTagihanResource\Pages;

use App\Filament\Resources\NotaTagihanResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions\Action;


class ViewNotaTagihan extends ViewRecord
{
    protected static string $resource = NotaTagihanResource::class;
    protected function getHeaderActions(): array
{
    return [
        Action::make('Cetak Nota')
        ->url(fn ($record) => route('nota-tagihan.cetak', ['noFaktur' => $record->no_faktur]))
            ->openUrlInNewTab()
            ->icon('heroicon-o-printer'),
    ];
}

    public function getFooter(): ?\Illuminate\Contracts\View\View
    {
        // Load relasi agar semua data tersedia
        $record = $this->record->load(['pelanggan', 'detail', 'pembayaran']);

        $details = $record->detail;
        $pelanggan = $record->pelanggan;
        $total = $details->sum(fn ($item) => $item->jumlah * $item->harga);

        $totalBayar = $record->totalPembayaran();
        $sisa = $total - $totalBayar;

        return view('filament.resources.nota-tagihan.detail', compact(
            'details',
            'pelanggan',
            'total',
            'totalBayar',
            'sisa'
        ));
    }
}
