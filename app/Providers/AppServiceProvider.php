<?php

namespace App\Providers;

use Livewire\Livewire;
use Filament\Facades\Filament;
use Filament\Navigation\UserMenuItem;
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
