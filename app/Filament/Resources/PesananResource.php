<?php

namespace App\Filament\Resources;

use filament;
use Filament\Tables;
use App\Models\Pesanan;
use Filament\Forms\Form;
use App\Models\Bahanjadi;
use App\Models\Pelanggan;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use App\Filament\Resources\PesananResource\Pages;

use function Laravel\Prompts\select;

class PesananResource extends Resource
{
    protected static ?string $model = Pesanan::class;
    protected static ?string $navigationGroup= 'Transaksi';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('no_faktur')
                ->required()
                ->unique(),
                Select::make('kode_plg')
                ->options(Pelanggan::query()->pluck('nama_plg','kode_plg'))
                ->searchable()
                ->reactive() // Aktifkan reaktivitas Livewire
                ->afterStateUpdated(function ($state, callable $set) {
                        // Ambil kode_bbaku berdasarkan id yang dipilih
                $bahanBaku = Pelanggan::find($state);
                if ($bahanBaku) {
                        $set('kode_plg', $bahanBaku->kode_bbaku);
                    }
                })
                ->required()
                ->label('Nama Pelanggan'),
                Select::make('kode_bjadi')
                ->options(Bahanjadi::query()->pluck('nama_bjadi','kode_bjadi'))
                ->searchable()
                ->reactive() // Aktifkan reaktivitas Livewire
                ->afterStateUpdated(function ($state, callable $set) {
                $set('gambar1', Bahanjadi::find($state)?->image);
                $bahanBaku = Bahanjadi::find($state);
                if ($bahanBaku) {
                        $set('kode_bjadi', $bahanBaku->kode_bbaku);

                    }
                })
                ->required()
                ->label('Nama Produk'),
                TextInput::make('jumlah')
                ->required()
                ->numeric(),
                Select::make('ukuran')->options([
                    'S'=>'S',
                    'M'=>'M',
                    'L'=>'L',
                    'XL'=>'XL',
                    'XXL'=>'XXL',
                    'XXXL'=>'XXXL',
                    'Jumbo'=>'Jumbo',
                ])->label('Ukuran'),
                TextInput::make('harga')
                ->required()
                ->numeric(),
                TextInput::make('catatan'),
                Select::make('status')->options([
                    'antrian'=>'antrian',
                    'dipotong'=>'dipotong',
                    'dijahit'=>'dijahit',
                    'dipotong'=>'dipotong',
                    'disablon'=>'disablon',
                    'dipacking'=>'dipacking',
                    'selesai'=>'selesai',
                ]),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('no_faktur')->sortable()->searchable(),
                TextColumn::make('kode_bjadi')->sortable()->searchable(),
                TextColumn::make('jumlah')->sortable(),
                TextColumn::make('ukuran')->sortable(),
                TextColumn::make('harga')->sortable()
                ->formatStateUsing(fn ($state) => formatRupiah($state))
                ->sortable(),
                TextColumn::make('status')->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                ->label('')
                ->tooltip('Edit'),
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
            'index' => Pages\ListPesanans::route('/'),
            'create' => Pages\CreatePesanan::route('/create'),
            'edit' => Pages\EditPesanan::route('/{record}/edit'),
        ];
    }
}
