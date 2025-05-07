<?php
namespace App\Filament\Resources\PesananDetailResource\Pages;

use App\Filament\Resources\PesananDetailResource;
use Filament\Resources\Pages\EditRecord;

class EditPesananDetail extends EditRecord
{
    protected static string $resource = PesananDetailResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
