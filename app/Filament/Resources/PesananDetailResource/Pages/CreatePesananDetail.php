<?php
namespace App\Filament\Resources\PesananDetailResource\Pages;

use App\Models\PesananDetail;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\PesananDetailResource;

class CreatePesananDetail extends CreateRecord
{
    protected static string $resource = PesananDetailResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

