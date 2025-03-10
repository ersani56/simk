<?php

namespace App\Filament\Resources\BahanbakuResource\Pages;

use App\Filament\Resources\BahanbakuResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBahanbakus extends ListRecords
{
    protected static string $resource = BahanbakuResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
