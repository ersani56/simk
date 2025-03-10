<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Bahanbaku;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Card;
use function Laravel\Prompts\select;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\BahanbakuResource\Pages;
use NumberFormatter;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\BahanbakuResource\RelationManagers;

function formatRupiah($amount) {
    $formatter = new NumberFormatter('id_ID', NumberFormatter::CURRENCY);
    return $formatter->formatCurrency($amount, 'IDR');
}

class BahanbakuResource extends Resource
{
    protected static ?string $model = Bahanbaku::class;

    protected static ?string $navigationIcon = 'heroicon-o-beaker';

    protected static ?string $navigationGroup= 'Input ';
    protected static ?string $navigationLabel = 'Bahan baku ';


    public static ?string $label = 'bahan baku';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                    ->schema([
                        TextInput::make('nama')
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
                        Select::make('tempat')->options([
                            'Sri Agung'=>'Sri Agung',
                            'Sohari'=>'Sohari',
                            'Rumah'=>'Rumah',
                            'Ruko'=>'Ruko',
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
            ->columns([
                TextColumn::make('nama')->sortable()->searchable(),
                TextColumn::make('satuan')->sortable(),
                TextColumn::make('harga')->sortable()
                ->formatStateUsing(fn ($state) => formatRupiah($state))
                ->sortable(),
                TextColumn::make('stok')->sortable(),
                TextColumn::make('tempat')->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
