<?php

namespace App\Filament\Resources\PesananDetailResource\Pages;

use App\Filament\Resources\PesananDetailResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPesananDetails extends ListRecords
{
    protected static string $resource = PesananDetailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
