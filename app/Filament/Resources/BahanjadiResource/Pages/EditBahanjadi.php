<?php

namespace App\Filament\Resources\BahanjadiResource\Pages;

use App\Filament\Resources\BahanjadiResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBahanjadi extends EditRecord
{
    protected static string $resource = BahanjadiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
