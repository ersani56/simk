<?php

namespace App\Filament\Resources;

use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Bahanjadi;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Card;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;
use App\Filament\Resources\BahanjadiResource\Pages;

class BahanjadiResource extends Resource
{
    protected static ?string $model = Bahanjadi::class;
    protected static ?string $navigationGroup= 'Admin';
    protected static ?string $navigationLabel = 'Produk';
    protected static ?string $navigation= 'Produk';
    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
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
                    TextInput::make('kode_bjadi')
                    ->label('Kode Produk')
                    ->disabled()
                    ->required()
                    ->dehydrated()
                    ->unique(ignorable:fn($record)=>$record),
                    TextInput::make('nama_bjadi')
                    ->label('Nama Produk')
                    ->Placeholder('Masukkan nama barang, Kaos KK Abu muda+merah tangan strip 2 merah')
                    ->required()
                    ->unique(ignorable:fn($record)=>$record),
                    Select::make('kategori')->options([
                        'Kaos'=>'Kaos',
                        'Trening'=>'Trening',
                        'Batik'=>'Batik',
                        'Celana'=>'Celana',
                        'Lainnya'=>'Lainnya',
                    ])
                    ->label('Kategori')
                    ->reactive() // Memicu perubahan saat dipilih
                    ->afterStateUpdated(fn ($state, callable $set) => $set('kode_bjadi', Bahanjadi::generateKodeP($state)))
                    ->required(),
                    Select::make('satuan')->options([
                        'Pcs'=>'Pcs',
                        'Stel'=>'Stel',
                    ])->label('Satuan'),
                    TextInput::make('upah')
                    ->required()
                    ->numeric()
                    ->prefix('Rp'),
                    FileUpload::make('gambar1')
                    ->disk('public_html_storage') // <- pakai disk yang baru
                    ->directory('') // kosongkan supaya simpan langsung di storage/products
                    ->image()
                    ->imageEditor()
                    ->maxSize(1024),
                    FileUpload::make('gambar2')
                    ->disk('public') // Simpan ke storage/public
                    ->directory('products') // Simpan dalam folder storage/app/public/products
                    ->image() // Hanya menerima gambar
                    ->imageEditor() // Fitur crop & edit (opsional)
                    ->maxSize(2048), // Batas 2MB
                ])
                ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kode_bjadi')->sortable()->searchable(),
                TextColumn::make('nama_bjadi')->sortable()->searchable(),
                TextColumn::make('kategori')->sortable(),
                TextColumn::make('satuan')->sortable(),
                TextColumn::make('upah')->sortable()
                ->formatStateUsing(fn ($state) => formatRupiah($state))
                ->sortable(),
                ImageColumn::make('gambar1')
                ->disk('public') // Ambil dari storage/public
                ->circular()
                ->size(50),
                ImageColumn::make('gambar2')
                ->disk('public') // Ambil dari storage/public
                ->circular()
                ->size(50),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\DeleteAction::make()
                ->label('')
                ->tooltip('Hapus'),
                Tables\Actions\EditAction::make()
                ->label('')
                ->tooltip('Ubah'),
                Action::make('addToCart')
                ->label('')
                ->icon('heroicon-o-shopping-cart')
                ->action(fn (Bahanjadi $record) => self::addToCart($record))
                ->color('success')
                ->tooltip('Tambah ke keranjang'),

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
            'index' => Pages\ListBahanjadis::route('/'),
            'create' => Pages\CreateBahanjadi::route('/create'),
            'edit' => Pages\EditBahanjadi::route('/{record}/edit'),
        ];
    }
}
