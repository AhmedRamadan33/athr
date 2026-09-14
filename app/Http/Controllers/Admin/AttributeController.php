<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAttributeRequest;
use App\Http\Requests\Admin\StoreAttributeValueRequest;
use App\Http\Requests\Admin\UpdateAttributeRequest;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Services\Admin\AttributeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AttributeController extends Controller
{
    public function __construct(protected AttributeService $attributeService) {}

    public function index(Request $request): View
    {
        return view('admin.attributes.index', [
            'attributes' => $this->attributeService->paginate($request->only(['search'])),
        ]);
    }

    public function create(): View
    {
        return view('admin.attributes.form', ['attribute' => new Attribute]);
    }

    public function store(StoreAttributeRequest $request): RedirectResponse
    {
        $attribute = $this->attributeService->create($request->validated());

        return redirect()->route('admin.attributes.edit', $attribute)->with('success', 'تم إنشاء السمة بنجاح.');
    }

    public function edit(Attribute $attribute): View
    {
        return view('admin.attributes.form', [
            'attribute' => $attribute->load('values'),
        ]);
    }

    public function update(UpdateAttributeRequest $request, Attribute $attribute): RedirectResponse
    {
        $this->attributeService->update($attribute, $request->validated());

        return redirect()->route('admin.attributes.edit', $attribute)->with('success', 'تم تحديث السمة بنجاح.');
    }

    public function destroy(Attribute $attribute): RedirectResponse
    {
        $this->attributeService->delete($attribute);

        return redirect()->route('admin.attributes.index')->with('success', 'تم حذف السمة بنجاح.');
    }

    public function storeValue(StoreAttributeValueRequest $request, Attribute $attribute): RedirectResponse
    {
        $this->attributeService->addValue($attribute, $request->validated()['value']);

        return redirect()->route('admin.attributes.edit', $attribute)->with('success', 'تمت إضافة القيمة بنجاح.');
    }

    public function destroyValue(Attribute $attribute, AttributeValue $value): RedirectResponse
    {
        $this->attributeService->removeValue($value);

        return redirect()->route('admin.attributes.edit', $attribute)->with('success', 'تم حذف القيمة بنجاح.');
    }
}
