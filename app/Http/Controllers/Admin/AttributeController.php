<?php

namespace App\Http\Controllers\Admin;

use App\Models\Attribute;
use App\Services\Admin\AttributeService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Attribute\UpdateAttributeRequest;

class AttributeController extends Controller
{
    public function __construct(
        private AttributeService $attributeService
    ) {}

    public function index()
    {
        $attributes = $this->attributeService->getAttributesList();

        return view('admin.ecommerce.attribute.index', compact('attributes'));
    }

    public function update(UpdateAttributeRequest $request, Attribute $attribute)
    {
        try {
            $this->attributeService->update($attribute, $request->validated());

            return redirect()
                ->route('admin.ecommerce.attribute.index')
                ->with('success', 'Attribute updated successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.ecommerce.attribute.index')
                ->with('error', 'Failed to update attribute: ' . $e->getMessage());
        }
    }

    public function toggleStatus(Attribute $attribute)
    {
        try {
            $updated = $this->attributeService->toggleStatus($attribute);
            $status  = $updated->status === 'active' ? 'enabled' : 'disabled';

            return redirect()
                ->route('admin.ecommerce.attribute.index')
                ->with('success', "Attribute {$status} successfully.");
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.ecommerce.attribute.index')
                ->with('error', $e->getMessage());
        }
    }
}