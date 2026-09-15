<?php

use App\Http\Controllers\Payment\PaymobCallbackController;
use App\Http\Controllers\Storefront\AccountController;
use App\Http\Controllers\Storefront\AddressController;
use App\Http\Controllers\Storefront\Auth\ForgotPasswordController;
use App\Http\Controllers\Storefront\Auth\LoginController;
use App\Http\Controllers\Storefront\Auth\RegisterController;
use App\Http\Controllers\Storefront\Auth\ResetPasswordController;
use App\Http\Controllers\Storefront\CartController;
use App\Http\Controllers\Storefront\CheckoutController;
use App\Http\Controllers\Storefront\ContactMessageController;
use App\Http\Controllers\Storefront\HomeController;
use App\Http\Controllers\Storefront\NewsletterController;
use App\Http\Controllers\Storefront\OrderController;
use App\Http\Controllers\Storefront\PageController;
use App\Http\Controllers\Storefront\ProductController;
use App\Http\Controllers\Storefront\ReviewController;
use App\Http\Controllers\Storefront\WishlistController;
use Illuminate\Support\Facades\Route;

Route::name('storefront.')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/category/{category:slug}', [ProductController::class, 'category'])->name('category.show');
    Route::get('/product/{slug}', [ProductController::class, 'show'])->name('product.show');

    Route::get('/story', [PageController::class, 'story'])->name('story');
    Route::get('/ritual', [PageController::class, 'ritual'])->name('ritual');
    Route::get('/contact', [PageController::class, 'contact'])->name('contact');
    Route::post('/contact', [ContactMessageController::class, 'store'])->name('contact.store');
    Route::post('/newsletter', [NewsletterController::class, 'store'])->name('newsletter.store');
    Route::get('/newsletter/unsubscribe/{subscriber}', [NewsletterController::class, 'unsubscribe'])
        ->middleware('signed')
        ->name('newsletter.unsubscribe');

    Route::prefix('cart')->name('cart.')->group(function () {
        Route::get('/', [CartController::class, 'index'])->name('index');
        Route::post('/', [CartController::class, 'store'])->name('store');
        Route::put('{item}', [CartController::class, 'update'])->name('update');
        Route::delete('{item}', [CartController::class, 'destroy'])->name('destroy');
    });

    Route::middleware('guest:customer')->group(function () {
        Route::get('register', [RegisterController::class, 'create'])->name('register');
        Route::post('register', [RegisterController::class, 'store'])->name('register.store');
        Route::get('login', [LoginController::class, 'create'])->name('login');
        Route::post('login', [LoginController::class, 'store'])->name('login.store');

        Route::get('forgot-password', [ForgotPasswordController::class, 'create'])->name('password.request');
        Route::post('forgot-password', [ForgotPasswordController::class, 'store'])->name('password.email');
        Route::get('reset-password/{token}', [ResetPasswordController::class, 'create'])->name('password.reset');
        Route::post('reset-password', [ResetPasswordController::class, 'store'])->name('password.update');
    });

    Route::middleware('auth:customer')->group(function () {
        Route::post('logout', [LoginController::class, 'destroy'])->name('logout');

        Route::get('checkout', [CheckoutController::class, 'create'])->name('checkout.create');
        Route::post('checkout', [CheckoutController::class, 'store'])->name('checkout.store');

        Route::get('account', [AccountController::class, 'edit'])->name('account.edit');
        Route::put('account', [AccountController::class, 'update'])->name('account.update');

        Route::prefix('account/addresses')->name('account.addresses.')->group(function () {
            Route::get('/', [AddressController::class, 'index'])->name('index');
            Route::post('/', [AddressController::class, 'store'])->name('store');
            Route::put('{address}', [AddressController::class, 'update'])->name('update');
            Route::delete('{address}', [AddressController::class, 'destroy'])->name('destroy');
        });

        Route::get('account/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('account/orders/{order_number}', [OrderController::class, 'show'])->name('orders.show');
        Route::post('account/orders/{order_number}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');

        Route::get('account/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
        Route::post('wishlist/{product}/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');

        Route::post('products/{product}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    });
});

Route::prefix('payment/paymob')->name('payment.paymob.')->group(function () {
    Route::post('webhook', [PaymobCallbackController::class, 'webhook'])->name('webhook');
    Route::get('return', [PaymobCallbackController::class, 'returnUrl'])->name('return');
});
