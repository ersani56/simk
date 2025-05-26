<?php

namespace App\Filament\Resources\PesananResource\Pages;

use App\Filament\Resources\PesananResource;
use App\Models\PesananDetail;
use Filament\Resources\Pages\EditRecord;

class EditPesanan extends EditRecord
{
    protected static string $resource = PesananResource::class;

    protected function afterSave(): void
    {
        $this->syncProdukPasangan();
    }

    protected function syncProdukPasangan(): void
    {
        $pesanan = $this->record;

        foreach ($pesanan->pesananDetails as $item) {
            if (isset($item->items_pasangan) && is_array($item->items_pasangan)) {
                foreach ($item->items_pasangan as $pasangan) {
                    $sudahAda = PesananDetail::where('no_faktur', $pesanan->no_faktur)
                        ->where('is_pasangan', true)
                        ->where('setelan', $item->kode_bjadi)
                        ->where('kode_bjadi', $pasangan['kode_bjadi_pasangan'])
                        ->exists();

                    if (! $sudahAda) {
                        PesananDetail::create([
                            'no_faktur' => $pesanan->no_faktur,
                            'kode_bjadi' => $pasangan['kode_bjadi_pasangan'],
                            'ukuran' => $item->ukuran,
                            'jumlah' => $item->jumlah,
                            'harga' => 0,
                            'upah_potong' => $pasangan['upah_potong_pasangan'] ?? 0,
                            'upah_jahit' => $pasangan['upah_jahit_pasangan'] ?? 0,
                            'upah_sablon' => $pasangan['upah_sablon_pasangan'] ?? 0,
                            'status' => 'antrian',
                            'is_pasangan' => true,
                            'setelan' => $item->kode_bjadi,
                        ]);
                    }
                }
            }
        }
    }
}
