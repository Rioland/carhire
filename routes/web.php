<?php

use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ResourceController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PublicController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public site
|--------------------------------------------------------------------------
*/

Route::get('/', [PublicController::class, 'home'])->name('home');
Route::get('/blog', [PublicController::class, 'blog'])->name('blog');
Route::get('/articles/{slug}', [PublicController::class, 'article'])->name('article');
Route::get('/location-directory', [PublicController::class, 'directory'])->name('directory');
Route::get('/locations/{slug}', [PublicController::class, 'locationService'])->name('location.service');
Route::get('/services/{slug}', [PublicController::class, 'service'])->name('service');
Route::get('/car-rental-{slug}', [PublicController::class, 'city'])->name('city');
Route::get('/contact', [PublicController::class, 'contact'])->name('contact');
Route::get('/sitemap.xml', [PublicController::class, 'sitemap'])->name('sitemap');

Route::post('/book', [BookingController::class, 'store'])->name('booking.store');
Route::get('/booking/{reference}', [BookingController::class, 'thanks'])->name('booking.thanks');

/*
|--------------------------------------------------------------------------
| Admin dashboard
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('login', [LoginController::class, 'show'])->name('login');
    Route::post('login', [LoginController::class, 'login'])->middleware('throttle:10,1');
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');

    Route::middleware('admin')->group(function () {
        Route::get('/', DashboardController::class)->name('dashboard');

        Route::get('bookings', [AdminBookingController::class, 'index'])->name('bookings.index');
        Route::get('bookings/{booking}', [AdminBookingController::class, 'show'])->name('bookings.show');
        Route::put('bookings/{booking}', [AdminBookingController::class, 'update'])->name('bookings.update');
        Route::delete('bookings/{booking}', [AdminBookingController::class, 'destroy'])->name('bookings.destroy');

        Route::get('settings', [SettingController::class, 'edit'])->name('settings.edit');
        Route::put('settings', [SettingController::class, 'update'])->name('settings.update');

        Route::get('{resource}', [ResourceController::class, 'index'])->name('resource.index');
        Route::get('{resource}/new', [ResourceController::class, 'create'])->name('resource.create');
        Route::post('{resource}', [ResourceController::class, 'store'])->name('resource.store');
        Route::get('{resource}/{id}/edit', [ResourceController::class, 'edit'])->name('resource.edit');
        Route::put('{resource}/{id}', [ResourceController::class, 'update'])->name('resource.update');
        Route::delete('{resource}/{id}', [ResourceController::class, 'destroy'])->name('resource.destroy');
    });
});

/*
|--------------------------------------------------------------------------
| Editable pages (Privacy, Terms, anything added in the dashboard)
| Kept last so it never shadows a real route.
|--------------------------------------------------------------------------
*/

Route::get('/{slug}', [PublicController::class, 'page'])->name('page');
