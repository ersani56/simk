<?php

namespace App\Providers;

use Invoice;
use Livewire\Livewire;
use App\Models\Pesanan;
use App\Observers\InvoiceObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Livewire::listen('set-produk', function ($event, $data) {
            session()->flash('selected_product', $data);
        });
    }
}
