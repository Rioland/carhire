<?php

namespace App\Providers;

use App\Models\City;
use App\Models\Page;
use App\Models\ServiceType;
use App\Models\Setting;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ContentServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Paginator::defaultView('vendor.pagination.simple');
        Paginator::defaultSimpleView('vendor.pagination.simple');

        // Guard the whole thing so `php artisan migrate` still works on a fresh database.
        if (! Schema::hasTable('settings')) {
            return;
        }

        View::composer('*', function ($view) {
            $view->with([
                'settings' => Setting::allValues(),
                'navCities' => City::where('is_active', true)->orderBy('sort_order')->limit(6)->get(),
                'navServices' => ServiceType::where('is_active', true)->where('has_landing_page', true)->orderBy('sort_order')->limit(6)->get(),
                'footerPages' => Page::where('is_active', true)->where('show_in_footer', true)->orderBy('title')->get(),
            ]);
        });
    }
}
