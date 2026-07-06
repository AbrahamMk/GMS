<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\EquipmentCategories\StoreEquipmentCategoryRequest;
use App\Http\Requests\EquipmentCategories\UpdateEquipmentCategoryRequest;
use App\Models\EquipmentCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class EquipmentCategoryController extends Controller
{
    public function index(Request $request): Response
    {
        $categories = EquipmentCategory::query()->orderBy('name')->get(['id', 'branch_id', 'name']);

        return Inertia::render('EquipmentCategories/Index', [
            'categories' => $categories,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('EquipmentCategories/Create');
    }

    public function store(StoreEquipmentCategoryRequest $request)
    {
        EquipmentCategory::query()->create($request->validated());

        return redirect()->route('portal.equipment-categories.index')->with('success', 'Equipment category created successfully.');
    }

    public function edit(EquipmentCategory $equipmentCategory): Response
    {
        return Inertia::render('EquipmentCategories/Edit', [
            'category' => $equipmentCategory,
        ]);
    }

    public function update(UpdateEquipmentCategoryRequest $request, EquipmentCategory $equipmentCategory)
    {
        $equipmentCategory->fill($request->validated())->save();

        return redirect()->route('portal.equipment-categories.index')->with('success', 'Equipment category updated successfully.');
    }

    public function destroy(EquipmentCategory $equipmentCategory)
    {
        $equipmentCategory->delete();

        return redirect()->route('portal.equipment-categories.index')->with('success', 'Equipment category deleted successfully.');
    }
}
