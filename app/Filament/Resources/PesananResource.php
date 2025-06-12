<?php
namespace App\Filament\Resources;

use Filament\Tables;
use App\Models\Produk;
use App\Models\Pesanan;
use Filament\Forms\Form;
use App\Models\Pelanggan;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Actions\Action;
use App\Filament\Resources\PesananResource\Pages;

class PesananResource extends Resource
{
    protected static ?string $model = Pesanan::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Transaksi';
    public ?string $produkTerakhirDipilih = null;

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->hasRole('admin');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->latest('updated_at')
            ->select([
                'pesanans.id',
                'pesanans.no_faktur',
                'pesanans.kode_plg',
                'pesanans.tanggal',
                'pesanans.catatan',
                'pesanans.created_at',
                'pesanans.updated_at'
            ])
            ->selectRaw('
                (SELECT COALESCE(SUM(jumlah_bayar), 0)
                FROM pembayarans
                WHERE pembayarans.pesanan_id = pesanans.id
            ) as total_bayar')
            ->groupBy([
                'pesanans.id',
                'pesanans.no_faktur',
                'pesanans.kode_plg',
                'pesanans.tanggal',
                'pesanans.catatan',
                'pesanans.created_at',
                'pesanans.updated_at'
            ]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make([
                            'default' => 1, // Ponsel
                            'md' => 2,      // Tablet (dan ponsel landscape yang lebih lebar)
                            'lg' => 4,
                        ])
                //Section::make()
                ->schema([
                TextInput::make('no_faktur')
                    ->disabled()
                    ->label('No Faktur')
                    ->required()
                    ->default(fn() => Pesanan::generateInvoiceNumber())
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
                Textarea::make('catatan')
                    ->label('Catatan')
                    ->default('-')
                    ->rows(1),
                ]),
                Hidden::make('produk_terakhir')
                ->reactive(),
            //->columns(4),

                // **Detail Pesanan**
            Repeater::make('pesananDetails')
                ->relationship('pesananDetails')
                ->reactive()
                ->schema([
                   // Section::make(),
                        Grid::make([
                            'default' => 1, // Ponsel
                            'md' => 3,      // Tablet (dan ponsel landscape yang lebih lebar)
                            'lg' => 6,
                        ])
                        ->schema([
                                Hidden::make('produk_terakhir'), // hanya untuk menyimpan sementara pilihan terakhir
                                TextInput::make('no_faktur')
                                    ->hidden()
                                    ->dehydrated(),
                                Select::make('kode_bjadi')
                                    ->label('Nama Produk')
                                    ->columnSpan([
                                        'default' => 1, // Ponsel
                                        'md' => 3,      // Tablet (dan ponsel landscape yang lebih lebar)
                                        'lg' => 3,
                                    ])
                                    //->options(Produk::orderBy('nama_bjadi')->pluck('nama_bjadi', 'kode_bjadi'))
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                            $produkTerakhir = $get('../../produk_terakhir') ?? [];

                                            // Tambah produk yang baru dipilih
                                            array_unshift($produkTerakhir, $state);

                                            // Hapus duplikat dan batasi hanya 2 terakhir
                                            $produkTerakhir = array_unique($produkTerakhir);
                                            $produkTerakhir = array_slice($produkTerakhir, 0, 2);

                                            $set('../../produk_terakhir', $produkTerakhir);
                                        })
                                        ->options(function (callable $get) {
                                            $produkList = \App\Models\Produk::orderBy('nama_bjadi')->get();
                                            $produkTerakhir = $get('../../produk_terakhir') ?? [];

                                            // Ambil dua produk terakhir dari koleksi
                                            $produkTerakhirList = collect($produkTerakhir)
                                                ->map(fn ($kode) => $produkList->firstWhere('kode_bjadi', $kode))
                                                ->filter();

                                            // Sisanya yang belum dipakai
                                            $produkSisa = $produkList->reject(fn ($p) => in_array($p->kode_bjadi, $produkTerakhir));

                                            return $produkTerakhirList
                                                ->merge($produkSisa)
                                                ->pluck('nama_bjadi', 'kode_bjadi');
                                        })
                                    ->searchable()
                                    ->live()
                                    ->required()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        $bahanjadi = Produk::where('kode_bjadi', $state)->first();
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
                                Select::make('ukuran')
                                ->options([
                                    'S' => 'S',
                                    'M' => 'M',
                                    'L' => 'L',
                                    'XL' => 'XL',
                                    'XXL' => 'XXL',
                                    'XXXL' => 'XXXL',
                                    'Jumbo' => 'Jumbo',
                                    'S Pdk' => 'S Pdk',
                                    'M Pdk' => 'M Pdk',
                                    'L Pdk' => 'L Pdk',
                                    'XL Pdk' => 'XL Pdk',
                                    'XXL Pdk' => 'XXL Pdk',
                                    'XXXL Pdk' => 'XXXL Pdk',
                                    'Jumbo Pdk' => 'Jumbo Pdk',
                                    'S Pjg' => 'S Pjg',
                                    'M Pjg' => 'M Pjg',
                                    'L Panjang' => 'L Pjg',
                                    'XL Pjg' => 'XL Pjg',
                                    'XXL Pjg' => 'XXL Pjg',
                                    'XXXL Pjg' => 'XXXL Pjg',
                                    'Jumbo Pjg' => 'Jumbo Pjg',
                                    'S Lk' => 'S Lk',
                                    'M Lk' => 'M Lk',
                                    'L Lk' => 'L Lk',
                                    'XL Lk' => 'XL Lk',
                                    'XXL Lk' => 'XXL Lk',
                                    'XXXL Lk' => 'XXXL Lk',
                                    'Jumbo Lk' => 'Jumbo Lk',
                                    'S Pr' => 'S Pr',
                                    'M Pr' => 'M Pr',
                                    'L Pr' => 'L Pr',
                                    'XL Pr' => 'XL Pr',
                                    'XXL Pr' => 'XXL Pr',
                                    'XXXL Pr' => 'XXXL Pr',
                                    'Jumbo Per' => 'Jumbo Pr',
                                ])
                                ->required(),
                                TextInput::make('jumlah')
                                    ->label('Jumlah')
                                    ->numeric()
                                    ->required(),

                                TextInput::make('status')
                                    ->disabled()
                                    ->label('Status')
                                    ->default('antrian')
                                    ->dehydrated(true),
                                TextInput::make('ket')
                                    ->label('Keterangan'),
                                TextInput::make('is_pasangan')
                                    ->hidden()
                                    ->dehydrated(true)
                                    ->default('false'),
                                Select::make('satuan')
                                    ->label('Satuan')
                                    ->options([
                                        'pcs' => 'Pcs',
                                        'stel' => 'Stel',
                                        'pasangan' => 'Pasangan',
                                    ])
                                    ->disabled(fn ($record) => $record?->is_pasangan)
                                    ->required()
                                    ->live()
                                    ->reactive()
                                        ->afterStateUpdated(function ($state, $old, callable $get, callable $set, \Filament\Forms\Components\Component $component) {
                                            // $state adalah nilai baru dari 'satuan' untuk item saat ini.
                                            // $component->getRecord() akan memberikan data array dari item repeater saat ini.
                                            // $component->getKey() akan memberikan key unik dari item repeater saat ini.
                                            $groupId = Str::random(8);
                                            $allDetails = $get('../../pesananDetails') ?? [];
                                            $currentItemKey = $component->getKey(); // Key dari item yang sedang diedit

                                            if ($state === 'stel') {
                                                // 1. Update item saat ini (yang menjadi 'stel')
                                                // Pastikan 'is_pasangan' di set ke false untuk item 'stel' utama
                                                // dan 'satuan' nya memang 'stel'.
                                                // Filament seharusnya sudah menangani perubahan 'satuan' menjadi 'stel'
                                                // untuk item saat ini karena Select terikat padanya.
                                                // Kita hanya perlu memastikan 'is_pasangan' diatur dengan benar.
                                                $set('is_pasangan', false); // Ini akan mengatur 'is_pasangan' pada item saat ini

                                                // Ambil data dari item 'stel' saat ini untuk item 'pasangan'
                                                $ukuran = $get('ukuran'); // Ambil dari item saat ini
                                                $jumlah = $get('jumlah'); // Ambil dari item saat ini
                                                $kode_bjadi_induk = $get('kode_bjadi'); // Jika ada, untuk referensi

                                                // Dapatkan lagi $allDetails SETELAH $set('is_pasangan', false)
                                                // untuk memastikan perubahan pada item saat ini tercermin jika $get
                                                // sebelumnya mengambil snapshot lama.
                                                // Namun, lebih aman memodifikasi $allDetails secara manual jika perlu.
                                                $allDetails = $get('../../pesananDetails') ?? []; // Re-fetch atau modifikasi yang sudah ada

                                                // Pastikan item saat ini (yang 'stel') sudah terupdate di $allDetails
                                                if (isset($allDetails[$currentItemKey])) {
                                                    $allDetails[$currentItemKey]['satuan'] = 'stel'; // Pastikan
                                                    $allDetails[$currentItemKey]['is_pasangan'] = false; // Pastikan
                                                    // Anda bisa juga update field lain di $allDetails[$currentItemKey] jika perlu
                                                    // $allDetails[$currentItemKey]['ket'] = $get('ket') . ' (Induk Stel)';
                                                }


                                                // 2. Buat dan tambahkan item 'pasangan' baru
                                                $pasanganItemKey = 'item-' . Str::uuid(); // Buat key unik baru untuk item pasangan
                                                $allDetails[$pasanganItemKey] = [
                                                    'kode_bjadi' => null, // Atau $kode_bjadi_induk . '_pasangan'
                                                    'satuan' => 'pasangan', // Set 'satuan' untuk item pasangan
                                                    'ukuran' => $ukuran,
                                                    'jumlah' => $jumlah,
                                                    'harga' => null, // Salin harga jika sama, atau null
                                                    'upah_potong' => null, // Salin jika sama, atau null
                                                    'upah_jahit' => null,   // Salin jika sama, atau null
                                                    'upah_sablon' => null, // Salin jika sama, atau null
                                                    'status' => 'antrian',
                                                    'ket' => null,
                                                    'is_pasangan' => true,
                                                    'setelan' => $groupId, // Tandai ini sebagai item pasangan
                                                    // Pastikan semua field yang ada di skema repeater didefinisikan di sini,
                                                    // bahkan jika null, untuk konsistensi.
                                                ];

                                                // Set kembali keseluruhan array details
                                                $set('../../pesananDetails', $allDetails);

                                            } elseif ($old === 'stel' && $state !== 'stel') {
                                                // Jika sebelumnya 'stel' dan sekarang bukan 'stel' lagi,
                                                // kita perlu menghapus item 'pasangan' yang terkait.
                                                // Ini memerlukan cara untuk mengidentifikasi pasangan dari item ini.
                                                // Misalnya, jika Anda menyimpan $pasanganItemKey pada item induk
                                                // atau jika item pasangan memiliki referensi ke $currentItemKey.

                                                $updatedDetails = [];
                                                foreach ($allDetails as $key => $detail) {
                                                    // Asumsi: item pasangan dibuat dengan 'ket' yang merujuk ke 'kode_bjadi' induk
                                                    // atau Anda memiliki cara lain untuk mengidentifikasi.
                                                    // Ini adalah bagian yang paling tricky untuk penghapusan otomatis.
                                                    // Contoh sederhana: jika pasangan selalu item berikutnya dengan is_pasangan=true
                                                    // Ini tidak robust. Cara terbaik adalah menyimpan ID pasangan di item stel, atau sebaliknya.

                                                    // Logika untuk menentukan apakah item ini adalah pasangan dari item yang diubah
                                                    // $isPairOfCurrentItem = ... ; (misalnya, berdasarkan $detail['ket'] atau field 'parent_key')

                                                    // Untuk contoh ini, kita asumsikan tidak ada logika penghapusan otomatis yang kompleks
                                                    // dan hanya memastikan item saat ini diupdate.
                                                    if ($key === $currentItemKey) {
                                                        $detail['is_pasangan'] = null; // Atau false, atau sesuai logika Anda
                                                        // $detail['satuan'] = $state; // Sudah dihandle Filament
                                                    }
                                                    $updatedDetails[$key] = $detail;
                                                }
                                                // Logika untuk menghapus item pasangan perlu lebih spesifik
                                                // Misalnya, jika Anda menambahkan 'parent_key' ke item pasangan:
                                                // $detailsWithoutPair = array_filter($allDetails, function($itemData, $itemKey) use ($currentItemKey) {
                                                //     return !($itemData['is_pasangan'] === true && $itemData['parent_key_temp'] === $currentItemKey);
                                                // }, ARRAY_FILTER_USE_BOTH);
                                                // $set('../../pesananDetails', $detailsWithoutPair);
                                                // Untuk sekarang, kita hanya fokus pada penambahan. Penghapusan bisa jadi fitur lanjutan.
                                            }
                                        })

                        ])
                    ])
                        ->createItemButtonLabel('Tambah Item')
                        ->minItems(1)
                        ->reorderable(false)
                        ->cloneable(false)
                        ->columnSpanFull()
                        //->itemLabel(fn (array $state): ?string => $state['kode_bjadi'] ?? null)
                        ->deletable(true)
                        ->deleteAction(
                                fn (Action $action) => $action->requiresConfirmation()
                            )
                        // ->columns(2),
                ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('no_faktur')
                    ->label('No Faktur')
                    ->searchable(),
                TextColumn::make('pelanggan.nama_plg')
                    ->label('Nama Pelanggan')
                    ->searchable(),
                TextColumn::make('tanggal')->label('Tanggal')->date(),
                TextColumn::make('catatan')->label('Catatan') ->limit(100),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPesanans::route('/'),
            'create' => Pages\CreatePesanan::route('/create'),
            'edit' => Pages\EditPesanan::route('/{record}/edit'),
        ];
    }
}
