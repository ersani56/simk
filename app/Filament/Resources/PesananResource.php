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
    protected static ?string $navigationGroup = 'Transaksi';

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->hasRole('admin');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->select([
                'pesanans.id',
                'pesanans.no_faktur',
                'pesanans.kode_plg',
                'pesanans.tanggal',
                'pesanans.created_at',
                'pesanans.updated_at'
            ])
            ->selectRaw('
                (SELECT COALESCE(SUM(jumlah_bayar), 0)
                FROM pembayarans
                WHERE pembayarans.no_faktur = pesanans.no_faktur
            ) as total_bayar')
            ->groupBy([
                'pesanans.id',
                'pesanans.no_faktur',
                'pesanans.kode_plg',
                'pesanans.tanggal',
                'pesanans.created_at',
                'pesanans.updated_at'
            ]);
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
                            })
                            ->disabled(fn ($record) => $record?->is_pasangan),

                        Select::make('satuan')
                            ->label('Satuan')
                            ->options([
                                'pcs' => 'Pcs',
                                'stel' => 'Stel',
                                'pasangan' => 'Pasangan',
                            ])
                            ->required()
                            ->live()
                            ->disabled(fn ($record) => $record?->is_pasangan),

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
                            ->columns(2)
                            ->hidden(fn ($record) => $record?->is_pasangan),

                        // Form tambahan untuk setelan/paket
                        Section::make('Detail Setelan/Paket')
                        ->schema([
                            Repeater::make('items_pasangan')
                                ->label('Produk Pasangan')
                                ->schema([
                                    Select::make('kode_bjadi_pasangan')
                                        ->label('Pilih Produk Pasangan')
                                        ->options(Bahanjadi::pluck('nama_bjadi', 'kode_bjadi'))
                                        ->searchable()
                                        ->required()
                                        ->live()
                                        ->afterStateUpdated(function ($state, callable $set) {
                                            $bahanjadi = Bahanjadi::where('kode_bjadi', $state)->first();
                                            if ($bahanjadi) {
                                                $set('upah_potong_pasangan', $bahanjadi->upah_potong);
                                                $set('upah_jahit_pasangan', $bahanjadi->upah_jahit);
                                                $set('upah_sablon_pasangan', $bahanjadi->upah);
                                            }
                                        }),

                                    TextInput::make('upah_potong_pasangan')
                                        ->label('Upah Potong Pasangan')
                                        ->numeric()
                                        ->prefix('Rp.'),

                                    TextInput::make('upah_jahit_pasangan')
                                        ->label('Upah Jahit Pasangan')
                                        ->numeric()
                                        ->prefix('Rp.'),

                                    TextInput::make('upah_sablon_pasangan')
                                        ->label('Upah Sablon Pasangan')
                                        ->numeric()
                                        ->prefix('Rp.'),
                                ])
                                ->columns(1)
                        ])
                        ->visible(fn ($get) => in_array($get('satuan'), ['stel', 'paket']))
                        ->collapsible(),
                        Select::make('ukuran')
                            ->options([
                                'S' => 'S',
                                'M' => 'M',
                                'L' => 'L',
                                'XL' => 'XL',
                                'XXL' => 'XXL',
                                'XXXL' => 'XXXL',
                                'Jumbo' => 'Jumbo',
                                'S Pendek' => 'S Pendek',
                                'M Pendek' => 'M Pendek',
                                'L Pendek' => 'L Pendek',
                                'XL Pendek' => 'XL Pendek',
                                'XXL Pendek' => 'XXL Pendek',
                                'XXXL Pendek' => 'XXXL Pendek',
                                'Jumbo Pendek' => 'Jumbo Pendek',
                                'SPanjang' => 'S Panjang',
                                'M Panjang' => 'M Panjang',
                                'L Panjang' => 'L Panjang',
                                'XL Panjang' => 'XL Panjang',
                                'XXL Panjang' => 'XXL Panjang',
                                'XXXL Panjang' => 'XXXL Panjang',
                                'Jumbo Panjang' => 'Jumbo Panjang',
                                'S Laki-laki' => 'S Laki-laki',
                                'M Laki-laki' => 'M Laki-laki',
                                'L Laki-laki' => 'L Laki-laki',
                                'XL Laki-laki' => 'XL Laki-laki',
                                'XXL Laki-laki' => 'XXL Laki-laki',
                                'XXXL Laki-laki' => 'XXXL Laki-laki',
                                'Jumbo Laki-laki' => 'Jumbo Laki-laki',
                                'S Perempuan' => 'S Perempuan',
                                'M Perempuan' => 'M Perempuan',
                                'L Perempuan' => 'L Perempuan',
                                'XL Perempuan' => 'XL Perempuan',
                                'XXL Perempuan' => 'XXL Perempuan',
                                'XXXL Perempuan' => 'XXXL Perempuan',
                                'Jumbo Perempuan' => 'Jumbo Perempuan',

                            ])
                            ->required(),

                        TextInput::make('jumlah')
                            ->label('Jumlah')
                            ->numeric()
                            ->required(),

                        TextInput::make('status')
                            ->label('Status')
                            ->default('antrian')
                            ->disabled()
                            ->dehydrated(),

                        TextInput::make('ket')
                            ->label('Keterangan'),
                    ])
                    ->createItemButtonLabel('Tambah Item')
                    ->minItems(1)
                    ->columns(1)
                    ->reorderable(false)
                    ->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {
                        // Tambahkan flag is_pasangan = false untuk item utama
                        return array_merge($data, ['is_pasangan' => false]);
                    })
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
                        return $state . ' item'; // Contoh: "5 item"
                    }),
            ])
            ->filters([ ])
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
