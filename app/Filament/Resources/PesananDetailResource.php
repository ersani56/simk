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

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with('bahanjadi');

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
            ->query(PesananDetail::with('gajiKaryawans'))
            ->columns([
                Tables\Columns\TextColumn::make('no_faktur')->searchable()->label('No. Faktur'),
                Tables\Columns\TextColumn::make('bahanjadi.nama_bjadi')->label('Nama Produk')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('satuan'),
                Tables\Columns\TextColumn::make('bahanjadi.gambar1')
                    ->label('Gbr 1')
                    ->formatStateUsing(fn($state) => $state ? '<a href="'.asset("storage/$state").'" target="_blank"><img src="'.asset("storage/$state").'" width="50" style="border-radius:50%;cursor:zoom-in;"></a>' : '-')
                    ->html(),
                Tables\Columns\TextColumn::make('bahanjadi.gambar2')
                    ->label('Gbr 2')
                    ->formatStateUsing(fn($state) => $state ? '<a href="'.asset("storage/$state").'" target="_blank"><img src="'.asset("storage/$state").'" width="50" style="border-radius:50%;cursor:zoom-in;"></a>' : '-')
                    ->html(),
                Tables\Columns\TextColumn::make('ukuran'),
                Tables\Columns\TextColumn::make('jumlah')->numeric()->sortable(),
                Tables\Columns\TextColumn::make('status')
                ->badge()
                ->color(fn($state) => match(strtolower(trim($state))) {
                        'antrian' => 'info',
                        'proses' => 'warning', // Gunakan 'warning' (orange built-in Filament)
                        'selesai' => 'success', // Hijau
                        default => 'primary',   // Biru
                })
                ->label('Status')
                ->formatStateUsing(fn ($state) => ucfirst($state)),

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
                Tables\Filters\SelectFilter::make('status')->options([
                    'antrian' => 'antrian',
                    'dipotong' => 'dipotong',
                    'dijahit' => 'dijahit',
                    'disablon' => 'disablon',
                    'selesai' => 'selesai',
                ]),
                // Tambahkan filter untuk menyembunyikan yang selesai (hanya untuk admin)
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
                        ? ['antrian' => 'antrian', 'dipotong' => 'dipotong', 'dijahit' => 'dijahit', 'disablon' => 'disablon', 'selesai' => 'selesai']
                        : ['dipotong' => 'dipotong', 'dijahit' => 'dijahit', 'disablon' => 'disablon'];

                    return [
                        Select::make('status')
                            ->options($availableStatus)
                            ->required()
                            ->reactive(),

                        TextInput::make('jumlah')
                            ->label('Jumlah')
                            ->numeric()
                            ->minValue(1)
                            ->required()
                            ->default(function (callable $get) use ($record, $userId) {
                                $status = $get('status');
                                if (!$status) return 1;

                                $peranMap = ['dipotong' => 'pemotong', 'dijahit' => 'penjahit', 'disablon' => 'penyablon'];
                                $peran = $peranMap[$status] ?? null;

                                if (!$peran) return 1;

                                // Hitung jumlah yang sudah dikerjakan oleh user ini untuk peran ini
                                $sudahUser = GajiKaryawan::where('pesanan_detail_id', $record->id)
                                    ->where('karyawan_id', $userId)
                                    ->where('peran', $peran)
                                    ->sum('jumlah');

                                // Hitung total yang sudah dikerjakan oleh semua user untuk peran ini
                                $totalSudah = GajiKaryawan::where('pesanan_detail_id', $record->id)
                                    ->where('peran', $peran)
                                    ->sum('jumlah');

                                // Sisa yang bisa dikerjakan
                                $sisa = max(0, $record->jumlah - $totalSudah);

                                // Default ke 1 jika ada sisa, atau 0 jika tidak ada sisa
                                return $sisa > 0 ? 1 : 0;
                            })
                            ->hint(function (callable $get) use ($record, $userId) {
                                $status = $get('status');
                                if (!$status) return 'Pilih status terlebih dahulu';

                                $peranMap = ['dipotong' => 'pemotong', 'dijahit' => 'penjahit', 'disablon' => 'penyablon'];
                                $peran = $peranMap[$status] ?? null;

                                if (!$peran) return '-';

                                // Hitung total yang sudah dikerjakan oleh semua user untuk peran ini
                                $totalSudah = GajiKaryawan::where('pesanan_detail_id', $record->id)
                                    ->where('peran', $peran)
                                    ->sum('jumlah');

                                // Hitung yang sudah dikerjakan oleh user ini untuk peran ini
                                $sudahUser = GajiKaryawan::where('pesanan_detail_id', $record->id)
                                    ->where('karyawan_id', $userId)
                                    ->where('peran', $peran)
                                    ->sum('jumlah');

                                $sisa = max(0, $record->jumlah - $totalSudah);

                                return 'Total dikerjakan: ' . $totalSudah . '/' . $record->jumlah .
                                    ' (Anda: ' . $sudahUser . ') | Sisa: ' . $sisa;
                            })
                            ->reactive()
                            ->maxValue(function (callable $get) use ($record, $userId) {
                                $status = $get('status');
                                $peranMap = ['dipotong' => 'pemotong', 'dijahit' => 'penjahit', 'disablon' => 'penyablon'];
                                $peran = $peranMap[$status] ?? null;

                                if (!$peran) return $record->jumlah;

                                // Hitung total yang sudah dikerjakan oleh semua user untuk peran ini
                                $totalSudah = GajiKaryawan::where('pesanan_detail_id', $record->id)
                                    ->where('peran', $peran)
                                    ->sum('jumlah');

                                return max(0, $record->jumlah - $totalSudah);
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

                    // Admin paksa selesai tanpa validasi
                    if ($status === 'selesai' && $user->hasRole('admin')) {
                        $record->update(['status' => 'selesai']);
                        return;
                    }

                    // Validasi jumlah
                    if ($jumlah <= 0) {
                        throw new \Exception('Jumlah harus lebih dari 0');
                    }

                    $validStatus = ['dipotong', 'dijahit', 'disablon'];
                    if (!$user->hasRole('admin') && !in_array($status, $validStatus)) {
                        abort(403, 'Tidak diizinkan mengubah status ini.');
                    }

                    $peranMap = ['dipotong' => 'pemotong', 'dijahit' => 'penjahit', 'disablon' => 'penyablon'];
                    $peran = $peranMap[$status] ?? null;
                    $upahField = ['dipotong' => 'upah_potong', 'dijahit' => 'upah_jahit', 'disablon' => 'upah_sablon'];
                    $upah = $peran ? ($record->{$upahField[$status]} ?? 0) : 0;

                    // Validasi total jumlah tidak melebihi pesanan
                    if ($peran) {
                        $totalSudah = GajiKaryawan::where('pesanan_detail_id', $record->id)
                            ->where('peran', $peran)
                            ->sum('jumlah');

                        if (($totalSudah + $jumlah) > $record->jumlah) {
                            throw new \Exception('Total jumlah untuk peran ini tidak boleh melebihi jumlah pesanan. Sisa yang bisa dikerjakan: ' . ($record->jumlah - $totalSudah));
                        }
                    }
                        // Jika admin memilih 'selesai', tandai override status
                        DB::transaction(function () use ($record, $status, $userId, $peran, $upah, $jumlah, $user) {
                            // Update status pesanan
                            $updateData = [
                                'status' => $status,
                            ];

                            if ($peran) {
                                $updateData[$peran] = $userId;
                            }

                            $record->update($updateData);

                        if ($peran) {
                            // Cari record gaji yang sudah ada untuk user ini pada peran ini
                            $existingGaji = GajiKaryawan::where('pesanan_detail_id', $record->id)
                                ->where('karyawan_id', $userId)
                                ->where('peran', $peran)
                                ->first();

                            if ($existingGaji) {
                                // Jika sudah ada, update jumlahnya dengan menambahkan jumlah baru
                                $existingGaji->update([
                                    'jumlah' => $existingGaji->jumlah + $jumlah,
                                    'total' => ($existingGaji->jumlah + $jumlah) * $upah,
                                    'tanggal_dibayar' => now(),
                                ]);
                            } else {
                                // Jika belum ada, buat record baru
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
                    });
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

                                // Update status berdasarkan peran lain
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

