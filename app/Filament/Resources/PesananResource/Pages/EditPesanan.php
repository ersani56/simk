<?php

namespace App\Filament\Resources\PesananResource\Pages;

use App\Filament\Resources\PesananResource;
use Filament\Resources\Pages\EditRecord;
use App\Models\Bahanjadi;

class EditPesanan extends EditRecord
{
    protected static string $resource = PesananResource::class;

    protected function afterSave(): void
    {
        $details = $this->data['pesananDetails'] ?? [];

        // // Hapus pasangan lama (yang setelan TIDAK NULL)
        // $this->record->pesananDetails()
        //     ->whereNotNull('setelan')
        //     ->delete();

        // Loop data dari repeater untuk cek apakah ada produk dengan satuan 'stel'
        foreach ($details as $detail) {
            if (($detail['satuan'] ?? null) === 'stel' && !empty($detail['setelan'])) {
                $this->record->pesananDetails()->create([
                    'kode_bjadi' => $detail['setelan'],
                    'satuan' => 'pcs',
                    'harga' => 0,
                    'upah_potong' => (int)($detail['upah_potong_pasangan'] ?? 0),
                    'upah_jahit' => (int)($detail['upah_jahit_pasangan'] ?? 0),
                    'upah_sablon' => (int)($detail['upah_sablon_pasangan'] ?? 0),
                    'ukuran' => $detail['ukuran'] ?? '-',
                    'jumlah' => $detail['jumlah'] ?? 1,
                    'status' => $detail['status'] ?? 'antrian',
                    'ket' => 'Pasangan dari ' . $detail['kode_bjadi'],
                    'setelan' => $detail['kode_bjadi'],
                ]);
            }
        }
    }
}
