<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Pesanan;
use Filament\Forms\Form;
use App\Models\Bahanjadi;
use App\Models\Pelanggan;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\PesananResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PesananResource\RelationManagers;
use App\Filament\Resources\PesananResource\RelationManagers\PesananDetailsRelationManager;

class PesananResource extends Resource
{
    protected static ?string $model = Pesanan::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup= 'Transaksi';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('no_faktur')
                ->label('No Faktur')
                ->required()
                ->unique(Pesanan::class)
                ->maxLength(20),
                Select::make('kode_plg')
                ->label('Nama Pelanggan')
                ->options(Pelanggan::pluck('nama_plg', 'kode_plg')) // Ambil kode_barang sebagai opsi
                ->searchable()
                ->required(),
            DatePicker::make('tanggal')
                ->label('Tanggal')
                ->required()
                ->default(today()),
            // **Detail Pesanan**
            Repeater::make('pesananDetails')
                ->relationship('pesananDetails')
                ->schema([
                    Select::make('kode_bjadi')
                    ->label('Kode Barang')
                    ->options(Bahanjadi::pluck('nama_bjadi', 'kode_bjadi')) // Ambil kode_barang sebagai opsi
                    ->searchable()
                    ->required(),
                    TextInput::make('ukuran')
                        ->label('Ukuran')
                        ->required()
                        ->maxLength(10),
                    TextInput::make('harga')
                        ->label('Harga')
                        ->numeric()
                        ->required(),
                    TextInput::make('jumlah')
                        ->label('Jumlah')
                        ->numeric()
                        ->required(),
                    Select::make('status')
                        ->label('Status')
                        ->options([
                            'antrian' => 'antrian',
                            'dipotong' => 'dipotong',
                            'dijahit' => 'dijahit',
                            'disablon' => 'disablon',
                            'selesai' => 'selesai',
                        ])
                        ->required(),
                ])
                ->minItems(1)
                ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('no_faktur')
                ->label('No Faktur')
                ->searchable(),
                TextColumn::make('kode_plg')->label('Nama Pelanggan')->searchable(),
                TextColumn::make('tanggal')->label('Tanggal')->date(),
                TextColumn::make('pesanan_details_count')->label('Jumlah Item')
                ->sortable()
                ->alignCenter()
                ->formatStateUsing(function ($state) {
                    return $state . ' item';// Contoh: "5 item"
                }),
            ])
            ->filters([
                //
            ])
            ->defaultSort('no_faktur', 'desc')
            ->modifyQueryUsing(function ($query) {
                return $query->withCount('pesananDetails'); // Eager loading untuk menghitung jumlah item
            })
                ->actions([
                Tables\Actions\ViewAction::make()->label('')->tooltip('detail'),
                Tables\Actions\DeleteAction::make()->label('')->tooltip('hapus'),
                Tables\Actions\EditAction::make()->label('')->tooltip('ubah'),
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
            PesananDetailsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPesanans::route('/'),
            'create' => Pages\CreatePesanan::route('/create'),
            'edit' => Pages\EditPesanan::route('/{record}/edit'),
        ];
    }
}
