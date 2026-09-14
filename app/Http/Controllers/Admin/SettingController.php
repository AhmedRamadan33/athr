<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSettingRequest;
use App\Services\Admin\SettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SettingController extends Controller
{
    public function __construct(protected SettingService $settingService) {}

    public function edit(?string $group = null): View
    {
        $group ??= 'general';

        if (! array_key_exists($group, SettingService::GROUPS)) {
            throw new NotFoundHttpException;
        }

        return view('admin.settings.edit', [
            'group' => $group,
            'groups' => SettingService::GROUPS,
            'values' => $this->settingService->getGroup($group),
        ]);
    }

    public function update(UpdateSettingRequest $request, string $group): RedirectResponse
    {
        if (! array_key_exists($group, SettingService::GROUPS)) {
            throw new NotFoundHttpException;
        }

        $this->settingService->updateGroup($group, $request->validated()['values']);

        return redirect()->route('admin.settings.edit', $group)->with('success', 'تم حفظ الإعدادات بنجاح.');
    }
}
