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

    protected function afterCreate(): void
{
    $data = $this->record;

    if ($data->satuan === 'stel' && $data->setelan) {
        PesananDetail::create([
            'no_faktur'   => $data->no_faktur,
            'kode_bjadi'  => $data->setelan, // kode produk pasangan
            'ukuran'      => $data->ukuran,
            'jumlah'      => $data->jumlah,
            'harga'       => 0,
            'satuan'      => $data->satuan,
            'setelan'     => $data->kode_bjadi, // produk utama
            'is_pasangan' => true,
        ]);
    }
}
}

