<?php

namespace App\Filament\Resources\KasbonResource\Pages;

use App\Filament\Resources\KasbonResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKasbon extends EditRecord
{
    protected static string $resource = KasbonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
