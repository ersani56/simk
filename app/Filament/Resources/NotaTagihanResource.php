<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Pesanan;
use Filament\Forms\Form;
use App\Models\Pelanggan;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\NotaTagihanResource\Pages;
use Illuminate\Support\Facades\DB; // Pastikan ini ada

class NotaTagihanResource extends Resource
{
    protected static ?string $model = Pesanan::class;
    protected static ?string $navigationIcon = 'heroicon-o-receipt-percent';
    protected static ?string $navigationLabel = 'Nota Tagihan';
    protected static ?string $pluralModelLabel = 'Nota Tagihan';
    protected static ?string $navigationGroup = 'Laporan';

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->hasRole('admin');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['pesananDetails', 'pembayarans', 'pelanggan']) // Eager loading ini bagus
            ->select('pesanans.*') // Memilih semua kolom dari pesanans
            // PERBAIKAN UNTUK total_bayar
            ->selectRaw('(SELECT COALESCE(SUM(jumlah_bayar), 0) FROM pembayarans WHERE pembayarans.pesanan_id = pesanans.id) as total_bayar')
            // PERBAIKAN UNTUK sisa_tagihan
            // Pastikan 'pesanans.total_tagihan' adalah kolom yang valid di tabel 'pesanans'
            ->selectRaw('(COALESCE(pesanans.total_tagihan, 0) - (SELECT COALESCE(SUM(jumlah_bayar), 0) FROM pembayarans WHERE pembayarans.pesanan_id = pesanans.id)) as sisa_tagihan');
            // Tambahkan COALESCE juga untuk pesanans.total_tagihan untuk menghindari error jika NULL
    }

    public static function form(Form $form): Form
    {
        // Form ini sepertinya untuk view, bukan create/edit resource ini secara langsung
        // Jika resource ini hanya untuk menampilkan, form ini mungkin tidak terlalu krusial
        // kecuali untuk halaman view.
        return $form->schema([
            TextInput::make('pesanan_id')->disabled(),
            TextInput::make('tanggal')->disabled(), // Tambahkan ->date() jika ini tanggal
            TextInput::make('pelanggan.nama_plg') // Akses melalui relasi
                ->label('Nama Pelanggan')
                ->disabled(),
            // Jika total_tagihan adalah kolom di tabel pesanans:
            TextInput::make('total_tagihan')->disabled(),
            // Jika total_bayar dan sisa_tagihan dari selectRaw ingin ditampilkan di form View:
            TextInput::make('total_bayar')->disabled(),
            TextInput::make('sisa_tagihan')->disabled(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->recordUrl(null)
        ->striped()
        ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('no_faktur')
                    ->label('No Faktur')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tanggal')
                    ->date()
                    ->label('Tanggal')
                    ->sortable(), // Tanggal biasanya bisa di-sort
                Tables\Columns\TextColumn::make('pelanggan.nama_plg') // Akses melalui relasi
                    ->label('Pelanggan')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_tagihan') // Ini adalah kolom asli dari tabel pesanans
                    ->money('IDR')
                    ->sortable() // Bisa di-sort jika kolom asli
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()
                            ->money('IDR')
                            ->label('Total Tagihan')
                    ]),
                Tables\Columns\TextColumn::make('total_bayar') // Ini adalah alias dari selectRaw
                    ->money('IDR')
                    ->sortable() // Bisa di-sort karena sudah dihitung di query utama
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()
                            ->money('IDR')
                            ->label('Total Dibayar')
                    ]),
                Tables\Columns\TextColumn::make('sisa_tagihan') // Ini adalah alias dari selectRaw
                    ->money('IDR')
                    ->sortable() // Bisa di-sort karena sudah dihitung di query utama
                    ->color(fn ($state) => $state > 0 ? 'warning' : 'success') // Contoh pewarnaan
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()
                            ->money('IDR')
                            ->label('Sisa Tagihan')
                    ]),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('kode_plg')
                    ->relationship('pelanggan', 'nama_plg') // Lebih baik gunakan relationship di sini
                    ->label('Filter by Pelanggan')
                    ->searchable()
                    ->preload(),
            ])
            ->headerActions([
                Action::make('Cetak Hasil Filter')
                    ->label('Cetak Hasil Filter')
                    ->icon('heroicon-o-printer')
                    ->color('primary')
                    ->url(fn ($livewire) =>
                        $livewire->getFilteredTableQuery()->count()
                            ? route('nota-tagihan.cetak-filtered', ['ids' => $livewire->getFilteredTableQuery()->pluck('id')->toArray()])
                            : null
                    )
                    ->openUrlInNewTab()
                    ->after(function ($livewire) {
                        if ($livewire->getFilteredTableQuery()->count() === 0) {
                            \Filament\Notifications\Notification::make()
                                ->title('Tidak Ada Data')
                                ->body('Tidak ada data yang cocok dengan filter untuk dicetak.')
                                ->warning()
                                ->send();
                        }
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Action::make('cetak_pdf')
                    ->label('Cetak PDF')
                    ->url(fn ($record) => route('nota-tagihan.cetak', ['no_faktur' => $record->no_faktur]))
                    ->openUrlInNewTab()
                    ->icon('heroicon-o-printer'),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNotaTagihans::route('/'),
            'view' => Pages\ViewNotaTagihan::route('/{record}'), // Pastikan record adalah ID atau slug Pesanan
        ];
    }
}
