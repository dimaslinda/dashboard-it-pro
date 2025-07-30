<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;

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
        // Define morph map for polymorphic relationships
        Relation::morphMap([
            'website' => \App\Models\Website::class,
            'invoice' => \App\Models\Invoice::class,
            'provider_contract' => \App\Models\ProviderContract::class,
        ]);
    }
}
