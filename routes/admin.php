<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AttributeController;
use App\Http\Controllers\Admin\Auth\ForgotPasswordController;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\Auth\ResetPasswordController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\NewsletterSubscriberController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PageContentController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductVariantController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\ShippingZoneController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest:admin')->group(function () {
        Route::get('login', [LoginController::class, 'create'])->name('login');
        Route::post('login', [LoginController::class, 'store'])->name('login.store');

        Route::get('forgot-password', [ForgotPasswordController::class, 'create'])->name('password.request');
        Route::post('forgot-password', [ForgotPasswordController::class, 'store'])->name('password.email');
        Route::get('reset-password/{token}', [ResetPasswordController::class, 'create'])->name('password.reset');
        Route::post('reset-password', [ResetPasswordController::class, 'store'])->name('password.update');
    });

    Route::middleware('auth:admin')->group(function () {
        Route::post('logout', [LoginController::class, 'destroy'])->name('logout');

        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::middleware('permission:admins.manage,admin')->group(function () {
            Route::resource('admins', AdminUserController::class)->except(['show']);
            Route::patch('admins/{admin}/toggle-active', [AdminUserController::class, 'toggleActive'])->name('admins.toggle-active');
        });

        Route::middleware('permission:roles.manage,admin')->group(function () {
            Route::resource('roles', RoleController::class)->except(['show']);
            Route::get('permissions', [PermissionController::class, 'index'])->name('permissions.index');
        });

        Route::middleware('permission:settings.manage,admin')->group(function () {
            Route::get('settings/{group?}', [SettingController::class, 'edit'])->name('settings.edit');
            Route::put('settings/{group}', [SettingController::class, 'update'])->name('settings.update');
        });

        Route::middleware('permission:pages.manage,admin')->group(function () {
            Route::get('pages-content', [PageContentController::class, 'edit'])->name('page-contents.edit');
            Route::put('pages-content/{pageKey}', [PageContentController::class, 'update'])->name('page-contents.update');

            Route::get('contact-messages', [ContactMessageController::class, 'index'])->name('contact-messages.index');
            Route::post('contact-messages/{contactMessage}/read', [ContactMessageController::class, 'markAsRead'])->name('contact-messages.read');
            Route::delete('contact-messages/{contactMessage}', [ContactMessageController::class, 'destroy'])->name('contact-messages.destroy');

            Route::get('newsletter-subscribers', [NewsletterSubscriberController::class, 'index'])->name('newsletter-subscribers.index');
            Route::get('newsletter-subscribers/export', [NewsletterSubscriberController::class, 'export'])->name('newsletter-subscribers.export');
            Route::get('newsletter-subscribers/compose', [NewsletterSubscriberController::class, 'compose'])->name('newsletter-subscribers.compose');
            Route::post('newsletter-subscribers/send', [NewsletterSubscriberController::class, 'send'])->name('newsletter-subscribers.send');
            Route::delete('newsletter-subscribers/{subscriber}', [NewsletterSubscriberController::class, 'destroy'])->name('newsletter-subscribers.destroy');
        });

        Route::middleware('permission:activity-log.view,admin')->group(function () {
            Route::get('activity-log', [ActivityLogController::class, 'index'])->name('activity-log.index');
        });

        Route::middleware('permission:catalog.manage,admin')->group(function () {
            Route::resource('attributes', AttributeController::class)->except(['show']);
            Route::post('attributes/{attribute}/values', [AttributeController::class, 'storeValue'])->name('attributes.values.store');
            Route::delete('attributes/{attribute}/values/{value}', [AttributeController::class, 'destroyValue'])->name('attributes.values.destroy');

            Route::resource('categories', CategoryController::class)->except(['show']);
            Route::resource('brands', BrandController::class)->except(['show']);

            Route::resource('products', ProductController::class)->except(['show']);
            Route::delete('products/{product}/images/{image}', [ProductController::class, 'destroyImage'])->name('products.images.destroy');
            Route::post('products/{product}/variants', [ProductVariantController::class, 'store'])->name('products.variants.store');
            Route::put('products/{product}/variants/{variant}', [ProductVariantController::class, 'update'])->name('products.variants.update');
            Route::delete('products/{product}/variants/{variant}', [ProductVariantController::class, 'destroy'])->name('products.variants.destroy');
        });

        Route::middleware('permission:orders.manage,admin')->group(function () {
            Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
            Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
            Route::put('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
        });

        Route::middleware('permission:customers.manage,admin')->group(function () {
            Route::get('customers', [CustomerController::class, 'index'])->name('customers.index');
            Route::get('customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');
            Route::patch('customers/{customer}/toggle-active', [CustomerController::class, 'toggleActive'])->name('customers.toggle-active');
        });

        Route::middleware('permission:shipping.manage,admin')->group(function () {
            Route::resource('shipping-zones', ShippingZoneController::class)->except(['show']);
        });

        Route::middleware('permission:coupons.manage,admin')->group(function () {
            Route::resource('coupons', CouponController::class)->except(['show']);
        });

        Route::middleware('permission:reviews.manage,admin')->group(function () {
            Route::get('reviews', [ReviewController::class, 'index'])->name('reviews.index');
            Route::post('reviews/{review}/approve', [ReviewController::class, 'approve'])->name('reviews.approve');
            Route::post('reviews/{review}/reject', [ReviewController::class, 'reject'])->name('reviews.reject');
            Route::delete('reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
        });

        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/', [NotificationController::class, 'index'])->name('index');
            Route::post('{notification}/read', [NotificationController::class, 'markAsRead'])->name('read');
            Route::post('read-all', [NotificationController::class, 'markAllAsRead'])->name('read-all');
        });
    });
});
