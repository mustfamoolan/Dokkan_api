<?php

namespace App\Providers;

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
        \App\Models\PurchaseInvoice::observe(\App\Observers\PurchaseInvoiceObserver::class);
        \App\Models\PurchaseReturn::observe(\App\Observers\PurchaseReturnObserver::class);
        \App\Models\SalesInvoice::observe(\App\Observers\SalesInvoiceObserver::class);
        \App\Models\SalesReturn::observe(\App\Observers\SalesReturnObserver::class);
    }
}
