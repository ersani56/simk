<?php

namespace App\Http\Livewire;

use Livewire\Component;

class SetProduk extends Component
{
    public $nama_produk;
    public $kode_produk;

    protected $listeners = ['setProduk'];

    public function setProduk($nama, $kode)
    {
        $this->nama_produk = $nama;
        $this->kode_produk = $kode;
    }

    public function render()
    {
        return view('livewire.set-produk');
    }
}
