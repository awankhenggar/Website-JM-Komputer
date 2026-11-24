<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\Admin;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReturnController;
use App\Http\Controllers\ReviewController;
use App\Http\Middleware\EnsureRole;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/tentang', [HomeController::class, 'about'])->name('about');
Route::get('/layanan-servis', [HomeController::class, 'services'])->name('services');
Route::view('/kontak', 'contact')->name('contact');
Route::get('/ulasan', [ReviewController::class, 'index'])->name('reviews.index');

Route::get('/produk', [ProductController::class, 'index'])->name('products.index');
Route::get('/produk/{product:slug}', [ProductController::class, 'show'])->name('products.show');

Route::get('/keranjang', [CartController::class, 'index'])->name('cart.index');
Route::post('/keranjang/{product}', [CartController::class, 'add'])->name('cart.add');
Route::patch('/keranjang/perbarui', [CartController::class, 'updateAll'])->name('cart.update-all');
Route::patch('/keranjang/{product}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/keranjang/{product}', [CartController::class, 'remove'])->name('cart.remove');

Route::middleware('guest')->group(function () {
    Route::get('/masuk', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/masuk', [AuthController::class, 'login']);
    Route::get('/daftar', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/daftar', [AuthController::class, 'register']);

    Route::get('/lupa-password', [PasswordResetController::class, 'showForgot'])->name('password.request');
    Route::post('/lupa-password', [PasswordResetController::class, 'sendLink'])->name('password.email');
    Route::get('/atur-ulang-password/{token}', [PasswordResetController::class, 'showReset'])->name('password.reset');
    Route::post('/atur-ulang-password', [PasswordResetController::class, 'reset'])->name('password.update');
});

Route::post('/keluar', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/email/verifikasi', [EmailVerificationController::class, 'notice'])->name('verification.notice');
    Route::get('/email/verifikasi/{id}/{hash}', [EmailVerificationController::class, 'verify'])->middleware('signed')->name('verification.verify');
    Route::post('/email/verifikasi/kirim-ulang', [EmailVerificationController::class, 'resend'])->middleware('throttle:6,1')->name('verification.send');

    Route::get('/checkout', [CheckoutController::class, 'show'])->name('checkout.show');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

    Route::get('/akun', [AccountController::class, 'index'])->name('account.index');
    Route::get('/akun/pesanan', [AccountController::class, 'orders'])->name('account.orders');
    Route::get('/akun/pesanan/{order}', [AccountController::class, 'orderShow'])->name('account.orders.show');
    Route::post('/akun/pesanan/{order}/bukti', [AccountController::class, 'uploadProof'])->name('account.orders.proof');
    Route::post('/akun/pesanan/{order}/batal', [AccountController::class, 'cancelOrder'])->name('account.orders.cancel');
    Route::get('/akun/servis', [AccountController::class, 'services'])->name('account.services');
    Route::get('/akun/servis/{serviceRequest}', [AccountController::class, 'serviceShow'])->name('account.services.show');
    Route::post('/akun/servis/{serviceRequest}/keputusan', [AccountController::class, 'serviceDecision'])->name('account.services.decision');

    Route::get('/akun/ulasan/{orderItem}', [ReviewController::class, 'create'])->name('account.reviews.create');
    Route::post('/akun/ulasan/{orderItem}', [ReviewController::class, 'store'])->name('account.reviews.store');
    Route::get('/akun/pesanan/{order}/retur', [ReturnController::class, 'create'])->name('account.returns.create');
    Route::post('/akun/pesanan/{order}/retur', [ReturnController::class, 'store'])->name('account.returns.store');

    Route::get('/servis/booking', [BookingController::class, 'create'])->name('booking.create');
    Route::post('/servis/booking', [BookingController::class, 'store'])->name('booking.store');
});

Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    Route::middleware(EnsureRole::class.':admin')->group(function () {
        Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');

        Route::resource('kategori', Admin\CategoryController::class)->except('show')->parameters(['kategori' => 'category']);
        Route::resource('produk', Admin\ProductController::class)->except('show')->parameters(['produk' => 'product']);

        Route::get('pesanan', [Admin\OrderController::class, 'index'])->name('pesanan.index');
        Route::get('pesanan/{order}', [Admin\OrderController::class, 'show'])->name('pesanan.show');
        Route::patch('pesanan/{order}', [Admin\OrderController::class, 'update'])->name('pesanan.update');

        Route::get('retur', [Admin\ReturnController::class, 'index'])->name('retur.index');
        Route::get('retur/{returnRequest}', [Admin\ReturnController::class, 'show'])->name('retur.show');
        Route::patch('retur/{returnRequest}', [Admin\ReturnController::class, 'update'])->name('retur.update');

        Route::get('ulasan', [Admin\ReviewController::class, 'index'])->name('ulasan.index');
        Route::patch('ulasan/{review}/sembunyikan', [Admin\ReviewController::class, 'toggle'])->name('ulasan.toggle');
        Route::patch('ulasan/{review}/pilihan-beranda', [Admin\ReviewController::class, 'toggleFeatured'])->name('ulasan.featured');
        Route::patch('ulasan/{review}/balas', [Admin\ReviewController::class, 'reply'])->name('ulasan.reply');
        Route::delete('ulasan/{review}', [Admin\ReviewController::class, 'destroy'])->name('ulasan.destroy');

        Route::get('pelanggan', [Admin\CustomerController::class, 'index'])->name('pelanggan.index');
        Route::get('pelanggan/{user}', [Admin\CustomerController::class, 'show'])->name('pelanggan.show');

        Route::resource('layanan', Admin\ServiceItemController::class)->except('show')->parameters(['layanan' => 'serviceItem']);
        Route::resource('tim', Admin\TechnicianController::class)->except('show')->parameters(['tim' => 'technician']);

        Route::get('laporan', [Admin\ReportController::class, 'index'])->name('laporan.index');

        Route::get('pengaturan', [Admin\SettingController::class, 'edit'])->name('pengaturan.edit');
        Route::put('pengaturan', [Admin\SettingController::class, 'update'])->name('pengaturan.update');

        Route::resource('pengguna', Admin\StaffController::class)->except('show')->parameters(['pengguna' => 'user']);
    });

    Route::middleware(EnsureRole::class.':admin,teknisi')->group(function () {
        Route::get('servis', [Admin\ServiceController::class, 'index'])->name('servis.index');
        Route::get('servis/{serviceRequest}', [Admin\ServiceController::class, 'show'])->name('servis.show');
        Route::patch('servis/{serviceRequest}', [Admin\ServiceController::class, 'update'])->name('servis.update');
    });
});
