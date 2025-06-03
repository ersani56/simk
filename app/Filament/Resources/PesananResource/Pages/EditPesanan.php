<?php

namespace App\Filament\Resources\PesananResource\Pages;

use App\Filament\Resources\PesananResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Str;

class EditPesanan extends EditRecord
{
    protected static string $resource = PesananResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
