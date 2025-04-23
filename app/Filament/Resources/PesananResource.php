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
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->selectRaw('pesanans.*,
                (SELECT COALESCE(SUM(jumlah_bayar), 0)
                FROM pembayarans
                WHERE pembayarans.no_faktur COLLATE utf8mb4_unicode_ci = pesanans.no_faktur COLLATE utf8mb4_unicode_ci
                ) as total_bayar')
            ->groupBy('pesanans.id');
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
                    ->options(Pelanggan::pluck('nama_plg', 'kode_plg'))
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
                            ->options(Bahanjadi::orderBy('nama_bjadi')->pluck('nama_bjadi', 'kode_bjadi'))
                            ->searchable()
                            ->live()
                            ->required()
                            ->afterStateUpdated(function ($state, callable $set) {
                                $bahanjadi = Bahanjadi::where('kode_bjadi', $state)->first();
                                if ($bahanjadi) {
                                    $set('harga', $bahanjadi->harga);
                                    $set('upah_potong', $bahanjadi->upah_potong);
                                    $set('upah_jahit', $bahanjadi->upah_jahit);
                                    $set('upah_sablon', $bahanjadi->upah);
                                }
                            }),

                        Select::make('satuan')
                            ->label('Satuan')
                            ->options([
                                'pcs' => 'PCS',
                                'stel' => 'Stel',
                            ])
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                $set('setelan', null);
                            }),

                        // Form untuk produk utama
                        Section::make()
                            ->schema([
                                TextInput::make('harga')
                                    ->label('Harga')
                                    ->numeric()
                                    ->required()
                                    ->prefix('Rp.'),

                                TextInput::make('upah_potong')
                                    ->label('Upah Potong')
                                    ->numeric()
                                    ->required()
                                    ->prefix('Rp.'),

                                TextInput::make('upah_jahit')
                                    ->label('Upah Jahit')
                                    ->numeric()
                                    ->required()
                                    ->prefix('Rp.'),

                                TextInput::make('upah_sablon')
                                    ->label('Upah Sablon')
                                    ->numeric()
                                    ->required()
                                    ->prefix('Rp.'),
                            ])
                            ->columns(2),

                        // Form tambahan untuk setelan (hanya muncul jika satuan = stel)
                        Section::make('Detail Setelan')
                            ->schema([
                                Select::make('setelan')
                                    ->label('Pilih Produk Pasangan')
                                    ->options(Bahanjadi::pluck('nama_bjadi', 'kode_bjadi'))
                                    ->searchable()
                                    ->visible(fn ($get) => $get('satuan') === 'stel')
                                    ->required(fn ($get) => $get('satuan') === 'stel')
                                    ->live()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        $bahanjadi = Bahanjadi::where('kode_bjadi', $state)->first();
                                        if ($bahanjadi) {
                                            $set('harga_pasangan', 0); // ← harga pasangan selalu nol
                                            $set('upah_potong_pasangan', $bahanjadi->upah_potong);
                                            $set('upah_jahit_pasangan', $bahanjadi->upah_jahit);
                                            $set('upah_sablon_pasangan', $bahanjadi->upah);
                                        }
                                    }),
                                    TextInput::make('harga_pasangan')
                                            ->default(0)
                                            ->visible(fn ($get) => $get('satuan') === 'stel' && $get('setelan'))
                                            ->disabled(),

                                    TextInput::make('upah_potong_pasangan')
                                            ->label('Upah potong pasangan')
                                            ->numeric()
                                            ->statePath('upah_potong_pasangan')
                                            ->dehydrated()
                                            ->prefix('Rp.')
                                            ->visible(fn ($get) => $get('satuan') === 'stel' && $get('setelan')),
                                            TextInput::make('upah_jahit_pasangan')
                                            ->label('Upah jahit pasangan')
                                            ->numeric()
                                            ->prefix('Rp.')
                                            ->visible(fn ($get) => $get('satuan') === 'stel' && $get('setelan')),
                                    TextInput::make('upah_sablon_pasangan')
                                            ->label('Upah sablon pasangan')
                                            ->numeric()
                                            ->statePath('upah_potong_pasangan')
                                            ->dehydrated()
                                            ->prefix('Rp.')
                                            ->visible(fn ($get) => $get('satuan') === 'stel' && $get('setelan')),
                            ])
                            ->visible(fn ($get) => $get('satuan') === 'stel')
                            ->collapsible(),
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
                        ->default('antrian')
                        ->options([
                            'antrian' => 'antrian',
                            'dipotong' => 'dipotong',
                            'dijahit' => 'dijahit',
                            'disablon' => 'disablon',
                            'selesai' => 'selesai',
                        ])
                        ->required(),
                    TextInput::make('ket'),
                ])
                ->statePath('pesananDetails')
                ->createItemButtonLabel('Tambah Item')
                ->minItems(1)
                ->columns(1)
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('no_faktur')
                ->label('No Faktur')
                ->searchable(),
                TextColumn::make('pelanggan.nama_plg')
                ->label('Nama Pelanggan')
                ->searchable(),
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
                return $query->with('pelanggan')->withCount('pesananDetails');
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
