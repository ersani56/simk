<?php

namespace App\Filament\Resources;

use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Bahanbaku;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use App\Filament\Resources\BahanbakuResource\Pages;
use NumberFormatter;
function formatRupiah($amount) {
    $formatter = new NumberFormatter('id_ID', NumberFormatter::CURRENCY);
    return $formatter->formatCurrency($amount, 'IDR');
}

class BahanbakuResource extends Resource
{
    protected static ?string $model = Bahanbaku::class;

    protected static ?string $navigationIcon = 'heroicon-o-beaker';

    protected static ?string $navigationGroup= 'Admin';
    protected static ?string $navigationLabel = 'Bahan Baku ';


    public static ?string $label = 'bahan baku';
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
                        TextInput::make('kode_bbaku')
                        ->label('Kode bahan baku')
                        ->required()
                        ->unique(ignorable:fn($record)=>$record),
                        TextInput::make('nama_bbaku')
                        ->label('Nama bahan baku')
                        ->Placeholder('Masukkan nama bahan baku, misal PE hitam')
                        ->required()
                        ->unique(ignorable:fn($record)=>$record),
                        Select::make('satuan')->options([
                            'Kg'=>'Kg',
                            'Mtr'=>'Mtr',
                            'Yrd'=>'Yrd',
                            'Pcs'=>'Pcs',
                        ]),
                        TextInput::make('harga')
                        ->required()
                        ->numeric()
                        ->prefix('Rp'),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kode_bbaku')->sortable()->searchable(),
                TextColumn::make('nama_bbaku')->sortable()->searchable(),
                TextColumn::make('satuan')->sortable(),
                TextColumn::make('harga')->sortable()
                ->formatStateUsing(fn ($state) => formatRupiah($state))
                ->sortable(),
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
            'index' => Pages\ListBahanbakus::route('/'),
            'create' => Pages\CreateBahanbaku::route('/create'),
            'edit' => Pages\EditBahanbaku::route('/{record}/edit'),
        ];
    }
}
