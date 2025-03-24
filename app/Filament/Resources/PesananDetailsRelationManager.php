<?php

namespace App\Filament\Resources\PesananResource\RelationManagers;

use Filament\Tables\Table;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;

class PesananDetailsRelationManager extends RelationManager
{
    protected static string $relationship = 'pesananDetails';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kode_bjadi')->label('Kode Barang')->sortable(),
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
