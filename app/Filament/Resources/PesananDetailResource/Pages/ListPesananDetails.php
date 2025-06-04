<?php

namespace App\Filament\Resources\PesananDetailResource\Pages;

use App\Filament\Resources\PesananDetailResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListPesananDetails extends ListRecords
{
    protected static string $resource = PesananDetailResource::class;

    public function getTabs(): array
    {
        return [
            'Semua' => Tab::make('Semua'),
            'Antrian' => Tab::make('Antrian')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'antrian')),
            'Proses' => Tab::make('Proses')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'proses')),
            'Trening' => Tab::make('Trening')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('produk', fn ($q) => $q->where('kategori', 'trening'))),

            'Kaos' => Tab::make('Kaos')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('produk', fn ($q) => $q->where('kategori', 'kaos'))),

            'Batik' => Tab::make('Batik')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('produk', fn ($q) => $q->where('kategori', 'batik'))),

            'Celana' => Tab::make('Celana')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('produk', fn ($q) => $q->where('kategori', 'celana'))),

            'Rok' => Tab::make('Rok')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('produk', fn ($q) => $q->where('kategori', 'rok'))),

            'Lainnya' => Tab::make('Lainnya')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('produk', fn ($q) => $q->where('kategori', 'lainnya'))),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
