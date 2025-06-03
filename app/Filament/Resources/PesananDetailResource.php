<?php

namespace App\Filament\Resources;

use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\GajiKaryawan;
use App\Models\PesananDetail;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\DB;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\PesananDetailResource\Pages;

class PesananDetailResource extends Resource
{
    protected static ?string $model = PesananDetail::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Proses Produksi';
    protected static ?string $modelLabel = 'Proses Produksi';
    protected static ?string $navigationGroup = 'Produksi';
    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with('produk');

        // Jika bukan admin, sembunyikan yang statusnya selesai
        if (!auth()->user()->hasRole('admin')) {
            $query->where('status', '!=', 'selesai');
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
            //->query(PesananDetail::with('gajiKaryawans'))
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('pesanan_id')
                ->searchable()
                ->label('No. Faktur')
                ->sortable(),
                Tables\Columns\TextColumn::make('produk.nama_bjadi')->label('Nama Produk')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('satuan'),
                Tables\Columns\TextColumn::make('produk.gambar1')
                    ->label('Gbr 1')
                    ->formatStateUsing(fn($state) => $state ? '<a href="'.asset("storage/$state").'" target="_blank"><img src="'.asset("storage/$state").'" width="50" style="border-radius:50%;cursor:zoom-in;"></a>' : '-')
                    ->html(),
                Tables\Columns\TextColumn::make('produk.gambar2')
                    ->label('Gbr 2')
                    ->formatStateUsing(fn($state) => $state ? '<a href="'.asset("storage/$state").'" target="_blank"><img src="'.asset("storage/$state").'" width="50" style="border-radius:50%;cursor:zoom-in;"></a>' : '-')
                    ->html(),
                Tables\Columns\TextColumn::make('ukuran'),
                Tables\Columns\TextColumn::make('jumlah')->numeric()->sortable(),
                Tables\Columns\TextColumn::make('status')
                ->badge()
                ->color(fn($state) => match(strtolower(trim($state))) {
                    'antrian' => 'info',
                    'proses' => 'warning',
                    'selesai' => 'success',
                    default => 'primary',
                })
                ->label('Status')
                ->formatStateUsing(fn ($state) => ucfirst($state))
                ->sortable(),

                Tables\Columns\TextColumn::make('hasil_potong')
                ->label('dipotong')
                ->getStateUsing(function ($record) {
                    return $record->gajiKaryawans?->where('peran', 'pemotong')->sum('jumlah') ?? 0;
                }),


                    Tables\Columns\TextColumn::make('hasil_jahit')
                    ->label('dijahit')
                    ->getStateUsing(function ($record) {
                        return $record->gajiKaryawans->where('peran', 'penjahit')->sum('jumlah');
                    })
                    ->alignCenter(),

                    Tables\Columns\TextColumn::make('hasil_sablon')
                    ->label('disablon')
                    ->getStateUsing(function ($record) {
                        return $record->gajiKaryawans->where('peran', 'penyablon')->sum('jumlah');
                    })
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('ket')->label('Keterangan')->wrap(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                ->label('Status Produksi')
                ->options([
                    '' => 'Semua',
                    'antrian' => 'Antrian',
                    'proses' => 'Proses',
                    'selesai' => 'Selesai',
                ])
                ->default('')
                ->query(function (Builder $query, array $data) {
                    if (!empty($data['value'])) {
                        $query->where('status', $data['value']);
                    }
                }),
                Tables\Filters\TernaryFilter::make('hide_completed')
                    ->label('Sembunyikan Selesai')
                    ->placeholder('Tampilkan Semua')
                    ->trueLabel('Sembunyikan Yang Selesai')
                    ->falseLabel('Tampilkan Yang Selesai')
                    ->queries(
                        true: fn (Builder $query) => $query->where('status', '!=', 'selesai'),
                        false: fn (Builder $query) => $query->where('status', 'selesai'),
                    )
                    ->visible(fn() => auth()->user()->hasRole('admin'))
            ])
            ->actions([
        Tables\Actions\Action::make('update_status')
            ->label('')
            ->icon('heroicon-m-arrow-up-circle')
            ->tooltip('Update Status')
            ->form(function (PesananDetail $record) {
                $user = auth()->user();
                $userId = $user->id;
                $isAdmin = $user->hasRole('admin');
                $availableStatus = $isAdmin
                    ? ['antrian' => 'antrian', 'dipotong' => 'dipotong', 'dijahit' => 'dijahit', 'disablon' => 'disablon', 'semua' => 'semua']
                    : ['dipotong' => 'dipotong', 'dijahit' => 'dijahit', 'disablon' => 'disablon'];

                return [
                    Select::make('status')
                        ->options($availableStatus)
                        ->required()
                        ->reactive(),

                    TextInput::make('jumlah')
                        ->label('Jumlah')
                        ->numeric()
                        ->required()
                        ->default(function (callable $get) use ($record, $userId) {
                            $status = $get('status');
                            if (!$status) return 1;

                            if ($status == 'semua') {
                                $sudahUser = GajiKaryawan::where('pesanan_detail_id', $record->id)
                                    ->whereIn('peran', ['pemotong', 'penjahit', 'penyablon'])
                                    ->where('karyawan_id', $userId)
                                    ->sum('jumlah');

                                $totalSudah = GajiKaryawan::where('pesanan_detail_id', $record->id)
                                    ->whereIn('peran', ['pemotong', 'penjahit', 'penyablon'])
                                    ->sum('jumlah');

                                $sisa = max(0, $record->jumlah - $totalSudah);

                                return $sisa > 0 ? 1 : 0;
                            } else {
                                $peranMap = ['dipotong' => 'pemotong', 'dijahit' => 'penjahit', 'disablon' => 'penyablon'];
                                $peran = $peranMap[$status] ?? null;

                                if (!$peran) return 1;

                                $sudahUser = GajiKaryawan::where('pesanan_detail_id', $record->id)
                                    ->where('karyawan_id', $userId)
                                    ->where('peran', $peran)
                                    ->sum('jumlah');

                                $totalSudah = GajiKaryawan::where('pesanan_detail_id', $record->id)
                                    ->where('peran', $peran)
                                    ->sum('jumlah');

                                $sisa = max(0, $record->jumlah - $totalSudah);

                                return $sisa > 0 ? 1 : 0;
                            }
                        })
                        ->hint(function (callable $get) use ($record, $userId) {
                            $status = $get('status');
                            if (!$status) return 'Pilih status terlebih dahulu';

                            if ($status == 'semua') {
                                $totalSudah = GajiKaryawan::where('pesanan_detail_id', $record->id)
                                    ->whereIn('peran', ['pemotong', 'penjahit', 'penyablon'])
                                    ->sum('jumlah');

                                $sudahUser = GajiKaryawan::where('pesanan_detail_id', $record->id)
                                    ->whereIn('peran', ['pemotong', 'penjahit', 'penyablon'])
                                    ->where('karyawan_id', $userId)
                                    ->sum('jumlah');

                                $sisa = max(0, $record->jumlah - $totalSudah);

                                return 'Total dikerjakan: ' . $totalSudah . '/' . $record->jumlah .
                                    ' (Anda: ' . $sudahUser . ') | Sisa: ' . $sisa;
                            } else {
                                $peranMap = ['dipotong' => 'pemotong', 'dijahit' => 'penjahit', 'disablon' => 'penyablon'];
                                $peran = $peranMap[$status] ?? null;

                                if (!$peran) return '-';

                                $totalSudah = GajiKaryawan::where('pesanan_detail_id', $record->id)
                                    ->where('peran', $peran)
                                    ->sum('jumlah');

                                $sudahUser = GajiKaryawan::where('pesanan_detail_id', $record->id)
                                    ->where('karyawan_id', $userId)
                                    ->where('peran', $peran)
                                    ->sum('jumlah');

                                $sisa = max(0, $record->jumlah - $totalSudah);

                                return 'Total dikerjakan: ' . $totalSudah . '/' . $record->jumlah .
                                    ' (Anda: ' . $sudahUser . ') | Sisa: ' . $sisa;
                            }
                        })
                        ->reactive()
                        ->maxValue(function (callable $get) use ($record) {
                            $status = $get('status');
                            if ($status == 'semua') {
                                return $record->jumlah;
                            } else {
                                $peranMap = ['dipotong' => 'pemotong', 'dijahit' => 'penjahit', 'disablon' => 'penyablon'];
                                $peran = $peranMap[$status] ?? null;

                                if (!$peran) return $record->jumlah;

                                $totalSudah = GajiKaryawan::where('pesanan_detail_id', $record->id)
                                    ->where('peran', $peran)
                                    ->sum('jumlah');

                                return max(0, $record->jumlah - $totalSudah);
                            }
                        })
                        ->minValue(function (callable $get) use ($record) {
                            $status = $get('status');
                            if ($status == 'semua') {
                                return 1;
                            } else {
                                return 1;
                            }
                        })
                ];
            })
            ->action(function (PesananDetail $record, array $data) {
                if (!auth()->check()) {
                    abort(403, 'Tidak diizinkan.');
                }

                $status = $data['status'];
                $jumlah = (int) $data['jumlah'];
                $user = auth()->user();
                $userId = $user->id;

                if ($jumlah <= 0) {
                    throw new \Exception('Jumlah harus lebih dari 0');
                }

                $validStatus = ['dipotong', 'dijahit', 'disablon'];
                if (!$user->hasRole('admin') && !in_array($status, $validStatus) && $status != 'semua') {
                    abort(403, 'Tidak diizinkan mengubah status ini.');
                }

                if ($status == 'semua') {
                    $peranMap = [
                        'pemotong' => ['upah' => $record->upah_potong, 'jumlah' => $jumlah],
                        'penjahit' => ['upah' => $record->upah_jahit, 'jumlah' => $jumlah],
                        'penyablon' => ['upah' => $record->upah_sablon, 'jumlah' => $jumlah],
                    ];

                    DB::transaction(function () use ($record, $userId, $peranMap, $jumlah) {
                        foreach ($peranMap as $peran => $detail) {
                            GajiKaryawan::where('pesanan_detail_id', $record->id)
                                ->where('peran', $peran)
                                ->delete();

                            GajiKaryawan::create([
                                'pesanan_detail_id' => $record->id,
                                'karyawan_id' => $userId,
                                'peran' => $peran,
                                'tanggal_dibayar' => now(),
                                'jumlah' => $jumlah,
                                'upah' => $detail['upah'],
                                'total' => $jumlah * $detail['upah'],
                            ]);
                        }

                        $record->update([
                            'pemotong' => $userId,
                            'penjahit' => $userId,
                            'penyablon' => $userId,
                        ]);

                        $totalPekerjaan = $record->jumlah;
                        $pekerjaanSelesaiPemotong = GajiKaryawan::where('pesanan_detail_id', $record->id)
                            ->where('peran', 'pemotong')
                            ->selectRaw('SUM(jumlah) as total')
                            ->first()->total ?? 0;

                        $pekerjaanSelesaiPenjahit = GajiKaryawan::where('pesanan_detail_id', $record->id)
                            ->where('peran', 'penjahit')
                            ->selectRaw('SUM(jumlah) as total')
                            ->first()->total ?? 0;

                        $pekerjaanSelesaiPenyablon = GajiKaryawan::where('pesanan_detail_id', $record->id)
                            ->where('peran', 'penyablon')
                            ->selectRaw('SUM(jumlah) as total')
                            ->first()->total ?? 0;

                        if ($pekerjaanSelesaiPemotong >= $totalPekerjaan &&
                            $pekerjaanSelesaiPenjahit >= $totalPekerjaan &&
                            $pekerjaanSelesaiPenyablon >= $totalPekerjaan) {
                            $record->update([
                                'status' => 'selesai',
                            ]);
                        } elseif ($pekerjaanSelesaiPemotong > 0 || $pekerjaanSelesaiPenjahit > 0 || $pekerjaanSelesaiPenyablon > 0) {
                            $record->update([
                                'status' => 'proses',
                            ]);
                        } else {
                            $record->update([
                                'status' => 'antrian',
                            ]);
                        }
                    });
                } else {
                    $peranMap = ['dipotong' => 'pemotong', 'dijahit' => 'penjahit', 'disablon' => 'penyablon'];
                    $peran = $peranMap[$status] ?? null;
                    $upahField = ['dipotong' => 'upah_potong', 'dijahit' => 'upah_jahit', 'disablon' => 'upah_sablon'];
                    $upah = $peran ? ($record->{$upahField[$status]} ?? 0) : 0;

                    DB::transaction(function () use ($record, $status, $userId, $peran, $upah, $jumlah) {
                        $updateData = [
                            'status' => $status,
                        ];

                        if ($peran) {
                            $updateData[$peran] = $userId;
                        }

                        $record->update($updateData);

                        if ($peran) {
                            $existingGaji = GajiKaryawan::where('pesanan_detail_id', $record->id)
                                ->where('karyawan_id', $userId)
                                ->where('peran', $peran)
                                ->first();

                            if ($existingGaji) {
                                $existingGaji->update([
                                    'jumlah' => $existingGaji->jumlah + $jumlah,
                                    'total' => ($existingGaji->jumlah + $jumlah) * $upah,
                                    'tanggal_dibayar' => now(),
                                ]);
                            } else {
                                GajiKaryawan::create([
                                    'pesanan_detail_id' => $record->id,
                                    'karyawan_id' => $userId,
                                    'peran' => $peran,
                                    'tanggal_dibayar' => now(),
                                    'jumlah' => $jumlah,
                                    'upah' => $upah,
                                    'total' => $jumlah * $upah,
                                ]);
                            }
                        }

                        $totalPekerjaan = $record->jumlah;
                        $pekerjaanSelesaiPemotong = GajiKaryawan::where('pesanan_detail_id', $record->id)
                            ->where('peran', 'pemotong')
                            ->selectRaw('SUM(jumlah) as total')
                            ->first()->total ?? 0;

                        $pekerjaanSelesaiPenjahit = GajiKaryawan::where('pesanan_detail_id', $record->id)
                            ->where('peran', 'penjahit')
                            ->selectRaw('SUM(jumlah) as total')
                            ->first()->total ?? 0;

                        $pekerjaanSelesaiPenyablon = GajiKaryawan::where('pesanan_detail_id', $record->id)
                            ->where('peran', 'penyablon')
                            ->selectRaw('SUM(jumlah) as total')
                            ->first()->total ?? 0;

                        if ($pekerjaanSelesaiPemotong >= $totalPekerjaan &&
                            $pekerjaanSelesaiPenjahit >= $totalPekerjaan &&
                            $pekerjaanSelesaiPenyablon >= $totalPekerjaan) {
                            $record->update([
                                'status' => 'selesai',
                            ]);
                        } elseif ($pekerjaanSelesaiPemotong > 0 || $pekerjaanSelesaiPenjahit > 0 || $pekerjaanSelesaiPenyablon > 0) {
                            $record->update([
                                'status' => 'proses',
                            ]);
                        } else {
                            $record->update([
                                'status' => 'antrian',
                            ]);
                        }
                    });
                }
            }),
            ActionGroup::make([
                Action::make('reset_pemotong')
                    ->label('Reset Pemotong')
                    ->icon('heroicon-m-arrow-path')
                    ->requiresConfirmation()
                    ->visible(fn () => auth()->user()?->hasRole('admin'))
                    ->action(function (PesananDetail $record) {
                        if ($record->pemotong) {
                            GajiKaryawan::where('pesanan_detail_id', $record->id)
                                ->where('peran', 'pemotong')
                                ->where('karyawan_id', $record->pemotong)
                                ->delete();

                            $record->pemotong = null;

                            $record->status = $record->penyablon ? 'disablon' :
                                            ($record->penjahit ? 'dijahit' : 'antrian');
                            $record->save();
                        }
                    }),

                Action::make('reset_penjahit')
                    ->label('Reset Penjahit')
                    ->icon('heroicon-m-arrow-path')
                    ->requiresConfirmation()
                    ->visible(fn () => auth()->user()?->hasRole('admin'))
                    ->action(function (PesananDetail $record) {
                        if ($record->penjahit) {
                            GajiKaryawan::where('pesanan_detail_id', $record->id)
                                ->where('peran', 'penjahit')
                                ->where('karyawan_id', $record->penjahit)
                                ->delete();

                            $record->penjahit = null;

                            $record->status = $record->penyablon ? 'disablon' :
                                            ($record->pemotong ? 'dipotong' : 'antrian');
                            $record->save();
                        }
                    }),

                Action::make('reset_penyablon')
                    ->label('Reset Penyablon')
                    ->icon('heroicon-m-arrow-path')
                    ->requiresConfirmation()
                    ->visible(fn () => auth()->user()?->hasRole('admin'))
                    ->action(function (PesananDetail $record) {
                        if ($record->penyablon) {
                            GajiKaryawan::where('pesanan_detail_id', $record->id)
                                ->where('peran', 'penyablon')
                                ->where('karyawan_id', $record->penyablon)
                                ->delete();

                            $record->penyablon = null;

                            $record->status = $record->penjahit ? 'dijahit' :
                                            ($record->pemotong ? 'dipotong' : 'antrian');
                            $record->save();
                        }
                    }),
            ])
            ->icon('heroicon-m-arrow-path')
            ->tooltip('Reset Status Per Peran')
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPesananDetails::route('/'),
        ];
    }
}

