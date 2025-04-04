<?php

namespace App\Filament\Resources;

use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Pelanggan;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Card;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use App\Filament\Resources\PelangganResource\Pages;

class PelangganResource extends Resource
{
    protected static ?string $model = Pelanggan::class;
    protected static ?string $navigationGroup= 'Admin';

    protected static ?string $navigationIcon = 'heroicon-o-users';
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->hasRole('admin');
    }
    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Card::make()
            ->schema([
                TextInput::make('kode_plg')
                ->label('Pelanggan')
                ->default(fn () => Pelanggan::generateKodeP()) // Generate kode otomatis
                ->disabled()
                ->dehydrated()
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
                TextColumn::make('kode_plg')->sortable()->searchable(),
                TextColumn::make('nama_plg')->sortable()->searchable(),
                TextColumn::make('alamat')->sortable(),
                TextColumn::make('telepon')->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\DeleteAction::make()
                ->label('')
                ->tooltip('Hapus'),
                Tables\Actions\EditAction::make()
                ->label('')
                ->tooltip('Ubah'),

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

