<?php

namespace App\Filament\Resources\NotaTagihanResource\Pages;

use App\Filament\Resources\NotaTagihanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListNotaTagihans extends ListRecords
{
    protected static string $resource = NotaTagihanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
