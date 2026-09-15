<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdatePageContentRequest;
use App\Services\Admin\PageContentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PageContentController extends Controller
{
    public function __construct(protected PageContentService $pageContents) {}

    public function edit(Request $request): View
    {
        $pages = collect(PageContentService::PAGES)->mapWithKeys(fn (array $config, string $key) => [
            $key => [
                'label' => $config['label'],
                'fields' => $config['fields'],
                'values' => $this->pageContents->getPage($key),
            ],
        ]);

        return view('admin.page-contents.edit', [
            'pages' => $pages,
            'activeTab' => $request->query('tab', array_key_first(PageContentService::PAGES)),
        ]);
    }

    public function update(UpdatePageContentRequest $request, string $pageKey): RedirectResponse
    {
        if (! array_key_exists($pageKey, PageContentService::PAGES)) {
            throw new NotFoundHttpException;
        }

        $this->pageContents->updatePage(
            $pageKey,
            $request->validated()['values'] ?? [],
            $request->file('values', [])
        );

        return redirect()->route('admin.page-contents.edit', ['tab' => $pageKey])
            ->with('success', 'تم حفظ محتوى الصفحة بنجاح.');
    }
}
