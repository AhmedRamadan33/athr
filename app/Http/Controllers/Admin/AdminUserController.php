<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAdminRequest;
use App\Http\Requests\Admin\UpdateAdminRequest;
use App\Models\Admin;
use App\Models\Role;
use App\Services\Admin\AdminUserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    public function __construct(protected AdminUserService $adminUsers) {}

    public function index(Request $request): View
    {
        return view('admin.admins.index', [
            'admins' => $this->adminUsers->paginate($request->only(['search', 'status', 'role'])),
            'roles' => Role::pluck('name'),
        ]);
    }

    public function create(): View
    {
        return view('admin.admins.form', [
            'admin' => new Admin,
            'roles' => Role::orderBy('name')->get(),
        ]);
    }

    public function store(StoreAdminRequest $request): RedirectResponse
    {
        $this->adminUsers->create($request->validated());

        return redirect()->route('admin.admins.index')->with('success', 'تم إنشاء حساب الأدمن بنجاح.');
    }

    public function edit(Admin $admin): View
    {
        return view('admin.admins.form', [
            'admin' => $admin->load('roles'),
            'roles' => Role::orderBy('name')->get(),
        ]);
    }

    public function update(UpdateAdminRequest $request, Admin $admin): RedirectResponse
    {
        $this->adminUsers->update($admin, $request->validated());

        return redirect()->route('admin.admins.index')->with('success', 'تم تحديث بيانات الأدمن بنجاح.');
    }

    public function destroy(Admin $admin): RedirectResponse
    {
        $this->adminUsers->delete($admin);

        return redirect()->route('admin.admins.index')->with('success', 'تم حذف الأدمن بنجاح.');
    }

    public function toggleActive(Admin $admin): RedirectResponse
    {
        $this->adminUsers->toggleActive($admin);

        return redirect()->route('admin.admins.index')->with('success', 'تم تحديث حالة الأدمن بنجاح.');
    }
}
