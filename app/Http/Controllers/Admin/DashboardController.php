<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\Role;
use Illuminate\View\View;
use Spatie\Activitylog\Models\Activity;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard.index', [
            'adminsCount' => Admin::count(),
            'rolesCount' => Role::count(),
            'productsCount' => Product::count(),
            'customersCount' => Customer::count(),
            'ordersCount' => Order::count(),
            'pendingOrdersCount' => Order::where('status', 'pending')->count(),
            'pendingReviewsCount' => Review::where('is_approved', false)->count(),
            'salesTotal' => Order::whereIn('status', ['processing', 'shipped', 'delivered'])->sum('total'),
            'activitiesTodayCount' => Activity::whereDate('created_at', today())->count(),
            'recentActivities' => Activity::with('causer')->latest()->limit(8)->get(),
            'recentOrders' => Order::with('customer')->latest()->limit(5)->get(),
        ]);
    }
}
