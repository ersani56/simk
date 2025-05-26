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
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->hasRole('admin');
    }

    protected static ?string $model = Pembayaran::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Transaksi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('kode_plg')
                    ->label('Pelanggan')
                    ->options(\App\Models\Pelanggan::pluck('nama_plg', 'kode_plg'))
                    ->searchable()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $set('no_faktur', null);
                        $set('total_bayar', 0);
                    })
                    ->required(),
                Select::make('no_faktur')
                    ->label('No Faktur')
                    ->options(function (callable $get) {
                        $pelanggan_id = $get('kode_plg');
                        if (!$pelanggan_id) {
                            return [];
                        }
                        return Pesanan::where('kode_plg', $pelanggan_id)->pluck('no_faktur', 'no_faktur');
                    })
                    ->searchable()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $pesanan = Pesanan::where('no_faktur', $state)->first();
                        if ($pesanan) {
                            $totalBayar = Pembayaran::where('no_faktur', $state)->sum('jumlah_bayar');
                            $set('total_bayar', $totalBayar);
                        } else {
                            $set('total_bayar', 0);
                        }
                    })
                    ->required(),
                TextInput::make('total_bayar')
                    ->label('Total Bayar')
                    ->disabled()
                    ->dehydrated(false),
                TextInput::make('jumlah_bayar')
                    ->label('Jumlah Bayar')
                    ->numeric()
                    ->prefix('Rp')
                    ->required(),
                DatePicker::make('tanggal_bayar')
                    ->label('Tanggal Bayar')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('no_faktur')->label('No Faktur'),
                TextColumn::make('pesanan.pelanggan.nama_plg')->label('Pelanggan')->searchable(),
                TextColumn::make('jumlah_bayar')->money('IDR')->label('Jumlah Bayar'),
                TextColumn::make('tanggal_bayar')->label('Tanggal Bayar')->date(),
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
            'index' => Pages\ListPembayarans::route('/'),
            'create' => Pages\CreatePembayaran::route('/create'),
            'edit' => Pages\EditPembayaran::route('/{record}/edit'),
        ];
    }
}
