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
            Card::make('Pesanan - Antrian', fn () => PesananDetail::where('status', 'antrian')->count())
                ->color('warning'),

            Card::make('Pesanan Diproses Potong', fn () => PesananDetail::where('status', 'Proses Potong')->count())
                ->color('info'),

            Card::make('Pesanan Proses Jahit', fn () => PesananDetail::where('status', 'Proses Jahit')->count())
                ->color('primary'),

            Card::make('Pesanan Proses Packing', fn () => PesananDetail::where('status', 'Proses Packing')->count())
                ->color('primary'),

            Card::make('Pesanan Selesai', fn () => PesananDetail::where('status', 'Selesai')->count())
                ->color('success'),
        ];
    }
}
