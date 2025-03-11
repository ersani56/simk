<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Stok;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\StokResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\StokResource\RelationManagers;

class StokResource extends Resource
{
    protected static ?string $model = Stok::class;
    protected static ?string $navigationGroup= 'Input ';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                ->schema([
                    TextInput::make('kode_stok')
                    ->label('Kode Stok')
                    ->required()
                    ->unique(ignorable:fn($record)=>$record),
                    TextInput::make('kode_bbaku')
                    ->required(),
                    TextInput::make('nama_bbaku')
                    ->required(),
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
        ->columns([
            TextColumn::make('kode_stok')->sortable()->searchable(),
            TextColumn::make('kode_bbaku')->sortable()->searchable(),
            TextColumn::make('nama_bbaku')->sortable()->searchable()->label('nama'),
            TextColumn::make('jml_stok')->sortable(),
            TextColumn::make('lokasi')->sortable()->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
