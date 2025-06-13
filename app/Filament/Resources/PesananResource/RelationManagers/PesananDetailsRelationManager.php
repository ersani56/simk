<?php

namespace App\Filament\Resources;

use App\Models\Produk;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Resources\RelationManagers\RelationManager;

class PesananDetailsRelationManager extends RelationManager
{
    protected static string $relationship = 'pesananDetails';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kode_bjadi')->label('Kode Barang')->sortable(),
                TextColumn::make('produk.nama_bjadi')
                ->label('Nama Produk'),
                TextColumn::make('satuan'),
                TextColumn::make('setelan')
                ->formatStateUsing(function ($state) {
                    return $state ? Produk::find($state)?->nama_bjadi : '-';
                })
                ->label('Setelan'),
                TextColumn::make('ukuran')->label('Ukuran'),
                TextColumn::make('harga')->label('Harga')->money('IDR'),
                TextColumn::make('jumlah')->label('Jumlah'),
                TextColumn::make('status')->label('Status')->badge(),
            ])
            ->filters([])
            ->headerActions([])
            ->actions([])
            ->bulkActions([]);
    }
}
