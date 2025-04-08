<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\GajiKaryawan;
use App\Models\PesananDetail;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\PesananDetailResource\Pages;

class PesananDetailResource extends Resource
{
    protected static ?string $model = PesananDetail::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Proses Produksi';
    protected static ?string $modelLabel = 'Proses Produksi';
    protected static ?string $navigationGroup = 'Produksi';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('bahanjadi');
    }

    // Hapus form karena tidak diperlukan
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Kosongkan karena tidak perlu form
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('no_faktur')
                    ->searchable()
                    ->label('No. Faktur'),
                Tables\Columns\TextColumn::make('bahanjadi.nama_bjadi')
                ->label('Nama Produk')
                ->searchable()
                ->sortable(),
                Tables\Columns\TextColumn::make('bahanjadi.gambar1')
                ->label('Gambar')
                ->formatStateUsing(function ($state) {
                    $url = asset("storage/{$state}");

                    return <<<HTML
                        <a href="{$url}" target="_blank" title="Klik untuk perbesar">
                            <img src="{$url}" width="50" style="cursor: zoom-in; border-radius: 50%;">
                        </a>
                    HTML;
                })
                ->html(),
                Tables\Columns\TextColumn::make('ukuran'),
                Tables\Columns\TextColumn::make('jumlah')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match (strtolower(trim($state))) {
                    'antrian' => 'gray',
                    'dipotong' => 'blue',
                    'dijahit' => 'indigo',
                    'disablon' => 'purple',
                    'selesai' => 'success',
                    // Backup jika ada variasi penulisan
                    'selesai dipotong' => 'blue',
                    'selesai dijahit' => 'indigo',
                    'selesai disablon' => 'purple',
                    default => 'yellow',
                    }),
                Tables\Columns\TextColumn::make('pemotongUser.name')
                    ->label('Pemotong')
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('penjahitUser.name')
                    ->label('Penjahit')
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('penyablonUser.name')
                    ->label('Penyablon')
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('keterangan')
                ->label('Keterangan')
                ->wrap(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'antrian' => 'antrian',
                        'dipotong' => 'dipotong',
                       // ->icon ('heroicon-o-check'),
                        'dijahit' => 'dijahit',
                        'disablon' => 'disablon',
                        'selesai' => 'selesai',
                    ]),
                ])
            ->actions([
                Tables\Actions\Action::make('update_status')
                ->label('Update Status')
                ->form([
                    Forms\Components\Select::make('status')
                        ->options([
                            'antrian' => 'antrian',
                            'dipotong' => 'dipotong',
                            'dijahit' => 'dijahit',
                            'disablon' => 'disablon',
                            'selesai' => 'selesai',
                        ])
                        ->required()
                ])
                ->action(function (PesananDetail $record, array $data) {
                    $status = $data['status'];
                    $userId = auth()->id();

                    $updateData = ['status' => $status];

                    // Deteksi peran dan upah
                    $peran = null;
                    $upah = 0;

                    if ($status === 'dipotong') {
                        $updateData['pemotong'] = $userId;
                        $peran = 'pemotong';
                        $upah = $record->upah_potong;
                    } elseif ($status === 'dijahit') {
                        $updateData['penjahit'] = $userId;
                        $peran = 'penjahit';
                        $upah = $record->upah_jahit;
                    } elseif ($status === 'disablon') {
                        $updateData['penyablon'] = $userId;
                        $peran = 'penyablon';
                        $upah = $record->upah_sablon;
                    }

                    $record->update($updateData);

                    if ($peran) {
                        // Simpan/Update gaji
                        GajiKaryawan::updateOrCreate(
                            [
                                'pesanan_detail_id' => $record->id,
                                'karyawan_id' => $userId,
                                'peran' => $peran,
                            ],
                            [
                                'tanggal_dibayar' => now(),
                                'jumlah' => $record->jumlah,
                                'upah' => $upah,
                                'total' => $record->jumlah * $upah,
                            ]
                        );
                    }
                })
                ->visible(function () {
                    // Sesuaikan dengan kebutuhan, misalnya hanya untuk role tertentu
                    return true;
                }),
                // Hapus EditAction karena tidak diperlukan
            ])
            ->bulkActions([
                // Hapus bulk actions jika tidak diperlukan
            ])
            ->defaultSort('created_at', 'desc');
    }

    // Hapus getRelations jika tidak ada relasi

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPesananDetails::route('/'),
            // Hapus create dan edit karena tidak diperlukan
        ];
    }

    // Nonaktifkan pembuatan record baru
    public static function canCreate(): bool
    {
        return false;
    }
}
