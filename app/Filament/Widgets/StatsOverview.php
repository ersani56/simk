<?php

namespace App\Filament\Widgets;

use App\Models\PesananDetail;
use Filament\Widgets\StatsOverviewWidget\Card;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StatsOverview extends BaseWidget
{
    protected function getCards(): array
    {
        return [
            Card::make('Dalam antrian', fn () => PesananDetail::where('status', 'antrian')->sum('jumlah'))
                ->color('warning')
                ->description('Pcs'),

            Card::make('Proses', fn () => PesananDetail::where('status', 'proses')->sum('jumlah'))
                ->color('info')
                ->description('Pcs'),

            Card::make('Pesanan Selesai', fn () => PesananDetail::where('status', 'Selesai')->sum('jumlah'))
                ->color('success')
                ->description('Pcs'),
        ];
    }
}
