<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PesananDetailResource\Pages;
use App\Models\PesananDetail;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

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
                Tables\Columns\TextColumn::make('kode_bjadi')
                    ->searchable()
                    ->label('Kode Barang'),
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
                Tables\Columns\TextColumn::make('pemotong')
                    ->label('Pemotong')
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('penjahit')
                    ->label('Penjahit')
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('penyablon')
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
                            'disablon' => 'disablon', // Pastikan tidak ada typo
                            'selesai' => 'selesai',
                        ])
                        ->required()
                        ->default(function (PesananDetail $record) {
                            return $record->status;
                        }),
                ])
                ->action(function (PesananDetail $record, array $data) {
                    $status = $data['status'];
                    $userName = auth()->user()->name;

                    $updateData = ['status' => $status];

                    if ($status === 'dipotong') {
                        $updateData['pemotong'] = $userName;
                    } elseif ($status === 'dijahit') {
                        $updateData['penjahit'] = $userName;
                    } elseif ($status === 'disablon') {
                        $updateData['penyablon'] = $userName;
                    }

                    $record->update($updateData);
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
