<?php

namespace App\Filament\Resources;

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
use App\Filament\Resources\PesananResource\Pages;
use App\Filament\Resources\PesananResource\RelationManagers\PesananDetailsRelationManager;

class PesananResource extends Resource
{
    protected static ?string $model = Pesanan::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup= 'Transaksi';

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->hasRole('admin');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('no_faktur')
                ->label('No Faktur')
                ->required()
                ->default(fn() => Pesanan::generateInvoiceNumber())
                ->disabled()
                ->unique(table: 'pesanans', column: 'no_faktur', ignoreRecord: true)
                ->maxLength(12)
                ->dehydrated(),
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
                    ->label('Nama Produk')
                    ->options(Bahanjadi::pluck('nama_bjadi', 'kode_bjadi'))
                    ->searchable()
                    ->live()
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $bahanjadi = Bahanjadi::where('kode_bjadi', $state)->first();
                        if ($bahanjadi) {
                            $set('harga', $bahanjadi->harga);
                            $set('upah_potong', $bahanjadi->upah_potong);
                            $set('upah_jahit', $bahanjadi->upah_jahit);
                            $set('upah_sablon', $bahanjadi->upah);
                        }
                    }),
                TextInput::make('harga')
                ->label('Harga')
                ->numeric()
                ->required()
                ->prefix('Rp.'),
                TextInput::make('upah_potong')
                ->label('Upah potong')
                ->numeric()
                ->required()
                ->prefix('Rp.'),
                TextInput::make('upah_jahit')
                ->label('Upah jahit')
                ->numeric()
                ->required()
                ->prefix('Rp.'),
                TextInput::make('upah_sablon')
                ->label('Upah sablon')
                ->numeric()
                ->required()
                ->prefix('Rp.'),
                    Select::make('ukuran')->options([
                        'S'=>'S',
                        'M'=>'M',
                        'L'=>'L',
                        'XL'=>'XL',
                        'XXL'=>'XXL',
                        'XXXL'=>'XXXL',
                        'Jumbo'=>'Jumbo',
                        'S Pendek'=>'S Pendek',
                        'S Panjang'=>'S Panjang',
                        'S Laki-laki'=>'S Laki-laki',
                        'S Perempuan'=>'S Perempuan',
                        'M Pendek'=>'M Pendek',
                        'M Panjang'=>'M Panjang',
                        'M Laki-laki'=>'M Laki-laki',
                        'M Perempuan'=>'M Peremuan',
                        'L Pendek'=>'L Pendek',
                        'L Panjang'=>'L Panjang',
                        'L Laki-laki'=>'L Laki-laki',
                        'L Perempuan'=>'L Perempuan',
                        'XL Pendek'=>'XL Pendek',
                        'XL Panjang'=>'XL Panjang',
                        'XL Laki-laki'=>'XL Laki-laki',
                        'XL Perempuan'=>'XL Perempuan',
                        'XXL Pendek'=>'XXL Pendek',
                        'XXL Panjang'=>'XXL Panjang',
                        'XXL Laki-laki'=>'XXL Laki-laki',
                        'XXL Perempuan'=>'XXL Perempuan',
                        'XXXL Pendek'=>'XXXL Pendek',
                        'XXXL Panjang'=>'XXXL Panjang',
                        'XXXL Laki-laki'=>'XXXL Laki-laki',
                        'XXXL Perempuan'=>'XXXL Perempuan',
                        'Jumbo Pendek'=>'Jumbo Pendek',
                        'Jumbo Panjang'=>'Jumbo Panjang',
                        'Jumbo Laki-laki'=>'Jumbo Laki-laki',
                        'Jumbo Perempuan'=>'Jumbo Perempuan',
                    ]),
                    TextInput::make('jumlah')
                        ->label('Jumlah')
                        ->numeric()
                        ->required(),
                    Select::make('status')
                        ->label('Status')
                        ->options([
                            'antrian' => 'antrian',
                            'proses potong' => 'proses potong',
                            'selesai potong' => 'selesai potong',
                            'proses jahit' => 'proses jahit',
                            'selesai jahit' => 'selesai jahit',
                            'proses sablon' => 'proses sablon',
                            'selesai sablon' => 'proses sablon',
                            'proses packing' => 'proses packing',
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
