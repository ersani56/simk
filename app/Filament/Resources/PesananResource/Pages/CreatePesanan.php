<?php

namespace App\Filament\Resources\PesananResource\Pages;

use App\Filament\Resources\PesananResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreatePesanan extends CreateRecord
{
    protected static string $resource = PesananResource::class;

    protected function afterCreate(): void
    {
        $details = $this->data['pesananDetails'] ?? [];

        foreach ($details as $detail) {
            if (in_array($detail['satuan'] ?? null, ['stel', 'paket']) && !empty($detail['items_pasangan'])) {
                // Generate unique group ID for this set
                $groupId = Str::uuid();

                // Update main product with group ID
                $this->record->pesananDetails()
                    ->where('kode_bjadi', $detail['kode_bjadi'])
                    ->update(['setelan' => $groupId]);

                // Create pasangan items
                foreach ($detail['items_pasangan'] as $pasangan) {
                    $this->record->pesananDetails()->create([
                        'kode_bjadi' => $pasangan['kode_bjadi_pasangan'],
                        'satuan' => 'pcs',
                        'harga' => 0,
                        'upah_potong' => (int)($pasangan['upah_potong_pasangan'] ?? 0),
                        'upah_jahit' => (int)($pasangan['upah_jahit_pasangan'] ?? 0),
                        'upah_sablon' => (int)($pasangan['upah_sablon_pasangan'] ?? 0),
                        'ukuran' => $detail['ukuran'] ?? '-',
                        'jumlah' => $detail['jumlah'] ?? 1,
                        'status' => $detail['status'] ?? 'antrian',
                        'ket' => 'Pasangan dari ' . $detail['kode_bjadi'],
                        'setelan' => $groupId,
                        'is_pasangan' => true,
                    ]);
                }
            }
        }
    }
}
