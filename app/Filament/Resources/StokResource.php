<?php

namespace App\Filament\Resources;

use App\Models\Stok;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Bahanbaku;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use App\Filament\Resources\StokResource\Pages;

class StokResource extends Resource
{
    protected static ?string $model = Stok::class;
    protected static ?string $navigationGroup= 'Transaksi';
    protected static ?string $navigationLabel = 'Pembelian';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->hasRole('admin');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                ->schema([
                    Select::make('kode_bbaku')
                    ->options(Bahanbaku::query()->pluck('nama_bbaku','kode_bbaku'))
                    ->searchable()
                    ->reactive() // Aktifkan reaktivitas Livewire
                    ->afterStateUpdated(function ($state, callable $set) {
                        // Ambil kode_bbaku berdasarkan id yang dipilih
                        $bahanBaku = Bahanbaku::find($state);
                        if ($bahanBaku) {
                            $set('kode_bbaku', $bahanBaku->kode_bbaku);
                        }
                    })
                    ->required()
                    ->label('Nama Bahan Baku')
                    ->unique(ignorable:fn($record)=>$record),
                    TextInput::make('jml_stok')
                    ->required()
                    ->numeric(),
                    Select::make('lokasi')->options([
                        'Rumah'=>'Rumah',
                        'Ruko'=>'Ruko',
                        'Sri Agung'=>'Sri Agung',
                        'Sohari'=>'Sohari',
                        'Bude Imah'=>'Bude Imah',
                        'Mb Hani'=>'Mb Hani',
                    ]),
                ])
                ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->query(
            Stok::query()
            ->selectRaw('CONCAT(kode_bbaku, "-", lokasi) as id, kode_bbaku, lokasi, SUM(jml_stok) as stok')
            ->groupBy('kode_bbaku', 'lokasi')
        )

        ->columns([
            TextColumn::make('kode_bbaku')->sortable()->searchable(),
            TextColumn::make('bahanBaku.nama_bbaku') // Ambil nama bahan baku dari relasi
            ->label('Nama Bahan Baku')
            ->sortable()
            ->searchable(),
            TextColumn::make('stok')->sortable()
            ->summarize(Tables\Columns\Summarizers\Sum::make()),
            TextColumn::make('lokasi')->sortable()->sortable(),
            ])
            ->defaultSort('kode_bbaku')
            ->filters([

            ])
            ->actions([
                Tables\Actions\DeleteAction::make()
                ->label('')
                ->tooltip('Hapus'),
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
            'index' => Pages\ListStoks::route('/'),
            'create' => Pages\CreateStok::route('/create'),
            'edit' => Pages\EditStok::route('/{record}/edit'),
        ];
    }

}
