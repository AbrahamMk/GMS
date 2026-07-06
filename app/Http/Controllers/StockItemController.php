<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StockItems\StoreStockItemRequest;
use App\Http\Requests\StockItems\UpdateStockItemRequest;
use App\Http\Resources\StockItems\StockItemResource;
use App\Models\StockItem;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class StockItemController extends Controller
{
    public function index(Request $request): Response
    {
        $search = $request->query('search');
        $isActive = $request->query('is_active');
        $lowStock = $request->query('low_stock');

        $stockItems = StockItem::query()
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('sku', 'like', "%{$search}%");
                });
            })
            ->when($isActive !== null && $isActive !== '', fn ($query, $isActive) => $query->where('is_active', filter_var($isActive, FILTER_VALIDATE_BOOLEAN)))
            ->when($lowStock, fn ($query) => $query->whereColumn('current_stock', '<=', 'reorder_level'))
            ->latest()
            ->paginate(15);

        return Inertia::render('StockItems/Index', [
            'stockItems' => StockItemResource::collection($stockItems)->response()->getData(true),
            'filters' => [
                'search' => $search ?? '',
                'is_active' => $isActive ?? '',
                'low_stock' => $lowStock ?? '',
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('StockItems/Create');
    }

    public function store(StoreStockItemRequest $request)
    {
        StockItem::query()->create($request->validated());

        return redirect()->route('portal.stock-items.index')->with('success', 'Stock item created successfully.');
    }

    public function edit(StockItem $stockItem): Response
    {
        return Inertia::render('StockItems/Edit', [
            'stockItem' => new StockItemResource($stockItem),
        ]);
    }

    public function update(UpdateStockItemRequest $request, StockItem $stockItem)
    {
        $stockItem->fill($request->validated())->save();

        return redirect()->route('portal.stock-items.index')->with('success', 'Stock item updated successfully.');
    }

    public function destroy(StockItem $stockItem)
    {
        $stockItem->delete();

        return redirect()->route('portal.stock-items.index')->with('success', 'Stock item deleted successfully.');
    }
}
