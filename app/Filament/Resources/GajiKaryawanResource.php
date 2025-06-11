<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\GajiKaryawan;
use Illuminate\Support\Carbon;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\GajiKaryawanResource\Pages;


class GajiKaryawanResource extends Resource
{
    protected static ?string $model = GajiKaryawan::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup= 'Laporan';

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with(['karyawan', 'pesananDetail']);

        if (!auth()->user()->hasRole('admin')) {
            $query->where('karyawan_id', auth()->id());
        }

        return $query;

    }


    public static function form(Form $form): Form
    {
        return $form->schema([]);

    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(null)
            ->columns([
                Tables\Columns\TextColumn::make('karyawan.name')
                ->label('Nama Karyawan')
                ->searchable()
                ->sortable(),
                Tables\Columns\TextColumn::make('peran')->label('Peran'),
                Tables\Columns\TextColumn::make('pesananDetail.produk.nama_bjadi')
                ->label('Nama Barang')
                ->sortable()
                ->searchable(),
                Tables\Columns\TextColumn::make('pesananDetail.ukuran')
                ->label('Size')
                ->sortable()
                ->searchable(),
                Tables\Columns\TextColumn::make('jumlah')->label('Jumlah'),
                Tables\Columns\TextColumn::make('upah')->label('Upah'),
                Tables\Columns\TextColumn::make('total')->label('Total'),
                Tables\Columns\TextColumn::make('created_at')->label('Tanggal')->date(),
            ])
            ->contentFooter(function ($livewire) {
                $selectedBulan = $livewire->tableFilters['bulan']['bulan'] ?? now()->toDateString();
                $tanggal = \Carbon\Carbon::parse($selectedBulan);

                $totalGaji = \App\Models\GajiKaryawan::query()
                    ->when(!auth()->user()->hasRole('admin'), fn ($q) => $q->where('karyawan_id', auth()->id()))
                    ->whereMonth('tanggal_dibayar', $tanggal->month)
                    ->whereYear('tanggal_dibayar', $tanggal->year)
                    ->sum('total');

                $cetakUrl = route('slip-gaji.bulan', $tanggal->format('Y-m'));

                return view('filament.components.gaji-summary', compact('totalGaji', 'cetakUrl'));
            })

            ->filters([
                Tables\Filters\Filter::make('bulan')
                ->form([
                    Forms\Components\DatePicker::make('bulan')
                        ->label('Pilih Bulan')
                        ->displayFormat('F Y')
                        ->statePath('bulan'), // ⬅️ ini penting!
                ])
                ->query(function (Builder $query, array $data) {
                    if ($data['bulan']) {
                        $tanggal = \Carbon\Carbon::parse($data['bulan']);
                        $query->whereMonth('tanggal_dibayar', $tanggal->month)
                            ->whereYear('tanggal_dibayar', $tanggal->year);
                    }
                })

            ->query(function (Builder $query, array $data) {
                if ($data['bulan']) {
                    $query->whereMonth('tanggal_dibayar', Carbon::parse($data['bulan'])->month)
                        ->whereYear('tanggal_dibayar', Carbon::parse($data['bulan'])->year);
            }
        })
        ->default(now()),
            ])
            ->actions([
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
            'index' => Pages\ListGajiKaryawans::route('/'),
            //'create' => Pages\CreateGajiKaryawan::route('/create'),
            'edit' => Pages\EditGajiKaryawan::route('/{record}/edit'),
        ];
    }
}
