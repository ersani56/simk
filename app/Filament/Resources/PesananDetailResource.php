<?php

namespace App\Filament\Resources;

use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\GajiKaryawan;
use App\Models\PesananDetail;
use Pages\EditPesananDetails;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB; // ⬅️ Ini penting!
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
                Tables\Columns\TextColumn::make('satuan'),
                Tables\Columns\TextColumn::make('bahanjadi.gambar1')
                ->label('Gbr 1')
                ->formatStateUsing(function ($state) {
                    $url = asset("storage/{$state}");

                    return <<<HTML
                        <a href="{$url}" target="_blank" title="Klik untuk perbesar">
                            <img src="{$url}" width="50" style="cursor: zoom-in; border-radius: 50%;">
                        </a>
                    HTML;
                })
                ->html(),
                Tables\Columns\TextColumn::make('bahanjadi.gambar2')
                ->label('Gbr 2')
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
                Tables\Columns\TextColumn::make('ket')
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
            // UPDATE STATUS (untuk user & admin)
            Tables\Actions\Action::make('update_status')
            ->label('')
            ->icon('heroicon-m-arrow-up-circle')
            ->tooltip('Update Status')
            ->form(function (PesananDetail $record) {
            $userId = auth()->id();
            $user = auth()->user();
            $isAdmin = $user->hasRole('admin');
            $availableStatus = $isAdmin
                ? ['antrian' => 'antrian', 'dipotong' => 'dipotong', 'dijahit' => 'dijahit', 'disablon' => 'disablon', 'selesai' => 'selesai']
                : ['dipotong' => 'dipotong', 'dijahit' => 'dijahit', 'disablon' => 'disablon'];

            return [
                Select::make('status')
                    ->options($availableStatus)
                    ->required()
                    ->reactive()
                    ->disableOptionWhen(function (string $value) use ($record, $userId) {
                        return GajiKaryawan::where('pesanan_detail_id', $record->id)
                            ->where('karyawan_id', $userId)
                            ->where('peran', $value)
                            ->exists();
                    }),

TextInput::make('jumlah')
    ->label('Jumlah')
    ->numeric()
    ->minValue(1)
    ->required()
    ->reactive()
    ->rule(function (callable $get) use ($record) {
        $status = $get('status');

        if (!$status || !in_array($status, ['dipotong', 'dijahit', 'disablon'])) {
            return null;
        }

        $peranMap = [
            'dipotong' => 'pemotong',
            'dijahit' => 'penjahit',
            'disablon' => 'penyablon',
        ];

        $peran = $peranMap[$status] ?? null;

        if (!$peran) {
            return null;
        }

        $sudah = \App\Models\GajiKaryawan::where('pesanan_detail_id', $record->id)
            ->where('peran', $peran)
            ->sum('jumlah');

        $sisa = max(0, $record->jumlah - $sudah);

        return "max:$sisa";
    })
    ->default(function (callable $get) use ($record) {
        $status = $get('status');

        if (!$status || !in_array($status, ['dipotong', 'dijahit', 'disablon'])) {
            return null;
        }

        $peranMap = [
            'dipotong' => 'pemotong',
            'dijahit' => 'penjahit',
            'disablon' => 'penyablon',
        ];

        $peran = $peranMap[$status] ?? null;

        $sudah = \App\Models\GajiKaryawan::where('pesanan_detail_id', $record->id)
            ->where('peran', $peran)
            ->sum('jumlah');

        return max(0, $record->jumlah - $sudah);
    })
    ->hint(function (callable $get) use ($record) {
        $status = $get('status');

        if (!$status || !in_array($status, ['dipotong', 'dijahit', 'disablon'])) {
            return null;
        }

        $peranMap = [
            'dipotong' => 'pemotong',
            'dijahit' => 'penjahit',
            'disablon' => 'penyablon',
        ];

        $peran = $peranMap[$status] ?? null;

        $sudah = \App\Models\GajiKaryawan::where('pesanan_detail_id', $record->id)
            ->where('peran', $peran)
            ->sum('jumlah');

        $sisa = max(0, $record->jumlah - $sudah);

        return "Sisa yang belum dikerjakan: $sisa";
    }),

];


        })
        ->action(function (PesananDetail $record, array $data) {
            $status = $data['status'];
            $jumlah = (int) $data['jumlah'];
            $userId = auth()->id();

            $updateData = ['status' => $status];

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

            if ($peran) {
                DB::transaction(function () use ($record, $updateData, $userId, $peran, $upah, $jumlah) {
                    $record->update($updateData);

                    GajiKaryawan::updateOrCreate(
                        [
                            'pesanan_detail_id' => $record->id,
                            'karyawan_id' => $userId,
                            'peran' => $peran,
                        ],
                        [
                            'tanggal_dibayar' => now(),
                            'jumlah' => $jumlah,
                            'upah' => $upah,
                            'total' => $jumlah * $upah,
                        ]
                    );
                });
            } else {
                // Jika hanya status diubah tanpa peran
                $record->update($updateData);
            }
        }),

            // RESET STATUS (untuk admin saja)
            Tables\Actions\Action::make('reset_status')
            ->label('')
            ->icon('heroicon-m-arrow-path')
            ->tooltip('Reset Status')
            ->requiresConfirmation()
            ->visible(fn () => auth()->user()->hasRole('admin'))
            ->action(function (PesananDetail $record) {
                // Tentukan peran & user terakhir yang mengerjakan
                $lastGaji = GajiKaryawan::where('pesanan_detail_id', $record->id)
                    ->latest()
                    ->first();

                if ($lastGaji) {
                    $peran = $lastGaji->peran;
                    $userId = $lastGaji->karyawan_id;

                    // Hapus data gaji terakhir
                    $lastGaji->delete();

                    // Reset kolom user_id di pesanan_detail
                    $peranColumn = match ($peran) {
                        'pemotong' => 'pemotong',
                        'penjahit' => 'penjahit',
                        'penyablon' => 'penyablon',
                        default => null,
                    };

                    if ($peranColumn) {
                        $record->$peranColumn = null;
                    }

                    // Hitung ulang status berdasarkan urutan pengerjaan yang masih ada
                    $status = 'antrian';
                    if ($record->penyablon) {
                        $status = 'disablon';
                    } elseif ($record->penjahit) {
                        $status = 'dijahit';
                    } elseif ($record->pemotong) {
                        $status = 'dipotong';
                    }

                    $record->status = $status;
                    $record->save();
                }
            }),
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
