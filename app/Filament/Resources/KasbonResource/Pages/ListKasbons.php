<?php

namespace App\Filament\Resources\KasbonResource\Pages;

use App\Filament\Resources\KasbonResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKasbons extends ListRecords
{
    protected static string $resource = KasbonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
