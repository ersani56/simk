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

            Card::make('Selesai dipotong', fn () => PesananDetail::where('status', 'dipotong')->sum('jumlah'))
                ->color('info')
                ->description('Pcs'),

            Card::make('Selesai dijahit', fn () => PesananDetail::where('status', 'dijahit')->sum('jumlah'))
                ->color('info')
                ->description('Pcs'),

            Card::make('Selesai disablon', fn () => PesananDetail::where('status', 'disablon')->sum('jumlah'))
                ->color('info')
                ->description('Pcs'),

            Card::make('Pesanan Selesai', fn () => PesananDetail::where('status', 'Selesai')->sum('jumlah'))
                ->color('success')
                ->description('Pcs'),
        ];
    }
}
