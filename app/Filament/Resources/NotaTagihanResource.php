<?php

namespace App\Filament\Resources;

use Illuminate\Support\Facades\DB; // Tambahkan ini
use Filament\Tables\Actions\Action;
use Filament\Forms;
use Filament\Tables;
use App\Models\Pesanan;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\NotaTagihanResource\Pages;

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
            ->with(['pesananDetails', 'pembayaran', 'pelanggan'])
            ->select([
                'pesanans.*',
                DB::raw('(SELECT COALESCE(SUM(jumlah_bayar), 0) FROM pembayarans WHERE pembayarans.no_faktur = pesanans.no_faktur) as total_bayar'),
                DB::raw('(pesanans.total_tagihan - (SELECT COALESCE(SUM(jumlah_bayar), 0) FROM pembayarans WHERE pembayarans.no_faktur = pesanans.no_faktur)) as sisa_tagihan')
            ]);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('no_faktur')->disabled(),
            Forms\Components\TextInput::make('tanggal')->disabled(),
            Forms\Components\TextInput::make('nama_pelanggan')
                ->label('Nama Pelanggan')
                ->placeholder(fn ($record) => $record?->pelanggan?->nama_plg)
                ->disabled(),
            Forms\Components\Hidden::make('total_tagihan'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('no_faktur')
                    ->label('No Faktur')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tanggal')
                    ->date()
                    ->label('Tanggal'),
                Tables\Columns\TextColumn::make('pelanggan.nama_plg')
                    ->label('Pelanggan')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_tagihan')
                    ->money('IDR')
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()
                            ->money('IDR')
                            ->label('Total Tagihan')
                    ]),
                Tables\Columns\TextColumn::make('total_bayar')
                    ->money('IDR')
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()
                            ->money('IDR')
                            ->label('Total Dibayar')
                    ]),
                Tables\Columns\TextColumn::make('sisa_tagihan')
                    ->money('IDR')
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()
                            ->money('IDR')
                            ->label('Sisa Tagihan')
                    ]),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('pelanggan')
                    ->relationship('pelanggan', 'nama_plg')
                    ->label('Filter by Pelanggan')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Action::make('cetak_pdf')
                    ->label('Cetak PDF')
                    ->url(fn ($record) => route('nota-tagihan.cetak', $record->no_faktur))
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
            'view' => Pages\ViewNotaTagihan::route('/{record}'),
        ];
    }
}
