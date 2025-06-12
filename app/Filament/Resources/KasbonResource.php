<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Kasbon;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Enums\ActionsPosition;
use App\Filament\Resources\KasbonResource\Pages;

class KasbonResource extends Resource
{
    protected static ?string $model = Kasbon::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->hasRole('admin');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('user_id')
                    ->relationship('user', 'name') // 'user' adalah nama method relasi di model Kasbon, 'name' adalah kolom yang ingin ditampilkan dari model User
                    ->searchable() // Membuat dropdown bisa dicari
                    ->preload()    // Memuat opsi di awal (baik untuk daftar yang tidak terlalu panjang)
                    ->required()
                    ->label('Karyawan'),
                Forms\Components\TextInput::make('jumlah')
                    ->required()
                    ->numeric(),
                Forms\Components\DatePicker::make('tanggal_pengajuan')
                    ->required(),
                Forms\Components\DatePicker::make('tanggal_disetujui'),
                Forms\Components\TextInput::make('status')
                    ->required(),
                Forms\Components\Textarea::make('keterangan')
                    ->columnSpanFull(),
                Select::make('user_id')
                    ->relationship('user', 'name') // 'user' adalah nama method relasi di model Kasbon, 'name' adalah kolom yang ingin ditampilkan dari model User
                    ->searchable() // Membuat dropdown bisa dicari
                    ->preload()    // Memuat opsi di awal (baik untuk daftar yang tidak terlalu panjang)
                    ->required()
                    ->label('Karyawan'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Karyawan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('jumlah')
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('tanggal_pengajuan')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('tanggal_disetujui')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'diajukan' => 'warning',
                        'disetujui' => 'success',
                        'ditolak' => 'danger',
                        'lunas' => 'info',
                        'dipotong' => 'primary',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('user_id')
                    ->relationship('user', 'name')
                    ->label('Karyawan'),
                SelectFilter::make('status')
                    ->options([
                        'diajukan' => 'Diajukan',
                        'disetujui' => 'Disetujui',
                        'ditolak' => 'Ditolak',
                        'lunas' => 'Lunas',
                        'dipotong' => 'Telah Dipotong',
                    ])
                    ->native(false),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Action::make('setujui')
                    ->label('Setujui')
                    ->icon('heroicon-s-check-circle')
                    ->color('success')
                    ->visible(fn (Kasbon $record) => $record->status === 'diajukan')
                    ->action(function (Kasbon $record) {
                        $record->status = 'disetujui';
                        $record->save();
                    })
                    ->requiresConfirmation(),
                Action::make('tolak')
                    ->label('Tolak')
                    ->icon('heroicon-s-x-circle')
                    ->color('danger')
                    ->visible(fn (Kasbon $record) => $record->status === 'diajukan')
                    ->action(function (Kasbon $record) {
                        $record->status = 'ditolak';
                        $record->save();
                    })
                    ->requiresConfirmation(),
            ], position: ActionsPosition::BeforeCells) // Menempatkan aksi di awal
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKasbons::route('/'),
            'create' => Pages\CreateKasbon::route('/create'),
            'edit' => Pages\EditKasbon::route('/{record}/edit'),
        ];
    }
}
