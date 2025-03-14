<?php

namespace App\Filament\Resources\BahanjadiResource\Pages;

use App\Filament\Resources\BahanjadiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBahanjadis extends ListRecords
{
    protected static string $resource = BahanjadiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
