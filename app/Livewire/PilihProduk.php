<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Bahanjadi;

class PilihProduk extends Component
{
    public $products;

    public function mount()
    {
        $this->products = Bahanjadi::all();
    }

    public function selectProduct($kode, $nama)
    {
        $this->dispatch('productSelected', kode: $kode, nama: $nama);
    }

    public function render()
    {
        return view('livewire.pilih-produk', [
            'products' => $this->products,
        ]);
    }

}
