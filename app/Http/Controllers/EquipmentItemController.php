<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\EquipmentItems\StoreEquipmentItemRequest;
use App\Http\Requests\EquipmentItems\UpdateEquipmentItemRequest;
use App\Http\Resources\EquipmentItems\EquipmentItemResource;
use App\Models\EquipmentCategory;
use App\Models\EquipmentItem;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class EquipmentItemController extends Controller
{
    public function index(Request $request): Response
    {
        $search = $request->query('search');
        $status = $request->query('status');
        $categoryId = $request->query('category_id');

        $equipment = EquipmentItem::query()
            ->with('category')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('asset_tag', 'like', "%{$search}%")
                      ->orWhere('model', 'like', "%{$search}%");
                });
            })
            ->when($status, fn ($query, $status) => $query->where('status', $status))
            ->when($categoryId, fn ($query, $categoryId) => $query->where('equipment_category_id', $categoryId))
            ->latest()
            ->paginate(15);

        $categories = EquipmentCategory::query()->orderBy('name')->get(['id', 'name']);

        return Inertia::render('EquipmentItems/Index', [
            'equipment' => EquipmentItemResource::collection($equipment)->response()->getData(true),
            'categories' => $categories,
            'filters' => [
                'search' => $search ?? '',
                'status' => $status ?? '',
                'category_id' => $categoryId ?? '',
            ],
        ]);
    }

    public function create(): Response
    {
        $categories = EquipmentCategory::query()->orderBy('name')->get(['id', 'name']);

        return Inertia::render('EquipmentItems/Create', [
            'categories' => $categories,
        ]);
    }

    public function store(StoreEquipmentItemRequest $request)
    {
        EquipmentItem::query()->create($request->validated());

        return redirect()->route('portal.equipment-items.index')->with('success', 'Equipment item created successfully.');
    }

    public function edit(EquipmentItem $equipmentItem): Response
    {
        $categories = EquipmentCategory::query()->orderBy('name')->get(['id', 'name']);

        return Inertia::render('EquipmentItems/Edit', [
            'equipmentItem' => new EquipmentItemResource($equipmentItem->load('category')),
            'categories' => $categories,
        ]);
    }

    public function update(UpdateEquipmentItemRequest $request, EquipmentItem $equipmentItem)
    {
        $equipmentItem->fill($request->validated())->save();

        return redirect()->route('portal.equipment-items.index')->with('success', 'Equipment item updated successfully.');
    }

    public function destroy(EquipmentItem $equipmentItem)
    {
        $equipmentItem->delete();

        return redirect()->route('portal.equipment-items.index')->with('success', 'Equipment item deleted successfully.');
    }
}
