<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Pelanggan;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Card;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\PelangganResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PelangganResource\RelationManagers;

class PelangganResource extends Resource
{
    protected static ?string $model = Pelanggan::class;
    protected static ?string $navigationGroup= 'Master';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Card::make()
            ->schema([
                TextInput::make('kode_plg')
                ->label('Kode Pelanggan')
                ->required()
                ->unique(ignorable:fn($record)=>$record),
                TextInput::make('nama_plg')
                ->label('Nama Pelanggan')
                ->required(),
                TextInput::make('alamat')
                ->required(),
                TextInput::make('telepon'),

            ])
            ->columns(1),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kode_plgn')->sortable()->searchable(),
                TextColumn::make('nama_plg')->sortable()->searchable(),
                TextColumn::make('alamat')->sortable(),
                TextColumn::make('telepon')->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\EditAction::make(),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPelanggans::route('/'),
            'create' => Pages\CreatePelanggan::route('/create'),
            'edit' => Pages\EditPelanggan::route('/{record}/edit'),
        ];
    }
}

