<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Pesanan;
use Filament\Forms\Form;
use App\Models\Pembayaran;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PembayaranResource\Pages;
use App\Filament\Resources\PembayaranResource\RelationManagers;

class PembayaranResource extends Resource
{
    protected static ?string $model = Pembayaran::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Transaksi';

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->hasRole('admin');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('kode_plg')
                    ->label('Pelanggan')
                    ->options(\App\Models\Pelanggan::pluck('nama_plg', 'kode_plg'))
                    ->searchable()
                    ->live() // atau ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $set('pesanan_id', null);
                        $set('total_bayar_sebelumnya', 0);
                        $set('total_tagihan_pesanan', 0); // Reset juga ini jika ada
                    })
                    ->required()
                    ->dehydrated(false),

                Select::make('pesanan_id')
                    ->label('No Faktur Pesanan')
                    ->options(function (callable $get) {
                        $kodePelanggan = $get('kode_plg');
                        if (!$kodePelanggan) {
                            return []; // Kembalikan array kosong jika tidak ada pelanggan terpilih
                        }
                        return Pesanan::where('kode_plg', $kodePelanggan)
                            // ->whereNotNull('no_faktur') // Pastikan no_faktur ada
                            // ->where('no_faktur', '!=', '')
                            ->pluck('no_faktur', 'id');
                    })
                    ->searchable() // Tetap bisa dicari jika daftarnya panjang
                    ->preload()    // <--- TAMBAHKAN INI
                    ->live()       // <--- TAMBAHKAN INI (atau ->reactive())
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            $totalBayarSebelumnya = Pembayaran::where('pesanan_id', $state)->sum('jumlah_bayar');
                            $set('total_bayar_sebelumnya', $totalBayarSebelumnya);

                            $pesanan = Pesanan::find($state);
                            if ($pesanan) {
                                $set('total_tagihan_pesanan', $pesanan->total_harga ?? 0);
                            }
                        } else {
                            $set('total_bayar_sebelumnya', 0);
                            $set('total_tagihan_pesanan', 0);
                        }
                    })
                    ->required(),

                TextInput::make('total_tagihan_pesanan')
                    ->label('Total Tagihan Pesanan')
                    ->numeric()
                    ->prefix('Rp')
                    ->disabled()
                    ->dehydrated(false),
                TextInput::make('total_bayar_sebelumnya')
                    ->label('Total Sudah Dibayar')
                    ->numeric()
                    ->prefix('Rp')
                    ->disabled()
                    ->dehydrated(false),
                TextInput::make('jumlah_bayar')
                    ->label('Jumlah Bayar Saat Ini')
                    ->numeric()
                    ->prefix('Rp')
                    ->required()
                    ->minValue(1),
                DatePicker::make('tanggal_bayar')
                    ->label('Tanggal Bayar')
                    ->default(now())
                    ->required(),
            ]);
    }

    // ... (table, getRelations, getPages) ...
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('pesanan.no_faktur')->label('No Faktur')->searchable()->sortable(),
                TextColumn::make('pesanan.pelanggan.nama_plg')->label('Pelanggan')->searchable()->sortable(),
                TextColumn::make('jumlah_bayar')->money('IDR')->label('Jumlah Bayar')->sortable(),
                TextColumn::make('tanggal_bayar')->label('Tanggal Bayar')->date()->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                ->label('')
                ->tooltip('Ubah'),
                Tables\Actions\DeleteAction::make()
                ->label('')
                ->tooltip('Hapus'),
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
            'index' => Pages\ListPembayarans::route('/'),
            'create' => Pages\CreatePembayaran::route('/create'),
            'edit' => Pages\EditPembayaran::route('/{record}/edit'),
        ];
    }
}
