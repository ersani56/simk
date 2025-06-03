<?php

namespace App\Filament\Resources\PesananResource\Pages;

use App\Filament\Resources\PesananResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreatePesanan extends CreateRecord
{
    protected static string $resource = PesananResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
