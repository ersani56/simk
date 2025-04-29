<?php

namespace App\Filament\Resources\PesananResource\Pages;

use App\Filament\Resources\PesananResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class EditPesanan extends EditRecord
{
    protected static string $resource = PesananResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        // Simpan pesanan utama terlebih dahulu
        $record = parent::handleRecordUpdate($record, $data);

        // Pastikan ada data pesananDetails
        if (empty($data['pesananDetails'])) {
            return $record;
        }

        // Proses penyimpanan produk pasangan
        $this->processPasanganItems($record, $data['pesananDetails']);

        return $record;
    }

    protected function processPasanganItems(Model $record, array $details): void
    {
        // Hapus produk pasangan lama yang terkait
        $record->pesananDetails()->where('is_pasangan', true)->delete();

        foreach ($details as $detail) {
            // Skip jika bukan setelan/paket atau tidak ada items_pasangan
            if (!in_array($detail['satuan'] ?? null, ['stel', 'paket']) ||
                empty($detail['items_pasangan'])) {
                continue;
            }

            $groupId = $detail['setelan'] ?? Str::uuid();

            // Update produk utama dengan group ID
            $record->pesananDetails()
                ->where('kode_bjadi', $detail['kode_bjadi'])
                ->update(['setelan' => $groupId]);

            // Simpan produk pasangan
            foreach ($detail['items_pasangan'] as $pasangan) {
                $record->pesananDetails()->create([
                    'kode_bjadi' => $pasangan['kode_bjadi_pasangan'],
                    'satuan' => 'pcs',
                    'harga' => 0,
                    'upah_potong' => $pasangan['upah_potong_pasangan'],
                    'upah_jahit' => $pasangan['upah_jahit_pasangan'],
                    'upah_sablon' => $pasangan['upah_sablon_pasangan'],
                    'ukuran' => $detail['ukuran'],
                    'jumlah' => $detail['jumlah'],
                    'status' => $detail['status'],
                    'ket' => 'Pasangan dari ' . $detail['kode_bjadi'],
                    'setelan' => $groupId,
                    'is_pasangan' => true,
                ]);
            }
        }
    }

    protected function fillFormWithDataAndCallHooks(Model $record, array $extraData = []): void
    {
        $record->load(['pesananDetails' => function ($query) {
            $query->orderBy('is_pasangan', 'asc');
        }]);

        $processedDetails = [];
        $groupedDetails = $record->pesananDetails->groupBy('setelan');

        foreach ($groupedDetails as $groupId => $items) {
            $mainItem = $items->firstWhere('is_pasangan', false);

            if (!$mainItem) continue;

            $detailData = $mainItem->toArray();

            if (in_array($mainItem->satuan, ['stel', 'paket'])) {
                $detailData['items_pasangan'] = $items->where('is_pasangan', true)
                    ->map(function ($item) {
                        return [
                            'kode_bjadi_pasangan' => $item->kode_bjadi,
                            'upah_potong_pasangan' => $item->upah_potong,
                            'upah_jahit_pasangan' => $item->upah_jahit,
                            'upah_sablon_pasangan' => $item->upah_sablon,
                        ];
                    })->values()->toArray();
            }

            $processedDetails[] = $detailData;
        }

        $data = array_merge($record->toArray(), [
            'pesananDetails' => $processedDetails
        ]);

        parent::fillFormWithDataAndCallHooks($record, $data);
    }
}
