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
        $admin = auth('admin')->user();

        $canOrders = $admin->can('orders.manage');
        $canActivity = $admin->can('activity-log.view');

        return view('admin.dashboard.index', [
            'adminsCount' => $admin->can('admins.manage') ? Admin::count() : null,
            'rolesCount' => $admin->can('roles.manage') ? Role::count() : null,
            'productsCount' => $admin->can('catalog.manage') ? Product::count() : null,
            'customersCount' => $admin->can('customers.manage') ? Customer::count() : null,
            'ordersCount' => $canOrders ? Order::count() : null,
            'pendingOrdersCount' => $canOrders ? Order::where('status', 'pending')->count() : null,
            'pendingReviewsCount' => $admin->can('reviews.manage') ? Review::where('is_approved', false)->count() : null,
            'salesTotal' => $canOrders ? Order::whereIn('status', ['processing', 'shipped', 'delivered'])->sum('total') : null,
            'activitiesTodayCount' => $canActivity ? Activity::whereDate('created_at', today())->count() : null,
            'recentActivities' => $canActivity ? Activity::with('causer')->latest()->limit(8)->get() : collect(),
            'recentOrders' => $canOrders ? Order::with('customer')->latest()->limit(5)->get() : collect(),
        ]);
    }
}
