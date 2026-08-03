<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\GymClasses\StoreGymClassRequest;
use App\Http\Requests\GymClasses\UpdateGymClassRequest;
use App\Http\Resources\GymClasses\GymClassResource;
use App\Models\GymClass;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class GymClassController extends Controller
{
    public function index(Request $request): Response
    {
        $search = $request->query('search');

        $classes = GymClass::query()
            ->with('trainer')
            ->when($search, fn ($query, $search) => $query->where('name', 'like', "%{$search}%"))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('GymClasses/Index', [
            'classes' => GymClassResource::collection($classes)->response()->getData(true),
            'filters' => ['search' => $search ?? ''],
        ]);
    }

    public function store(StoreGymClassRequest $request)
    {
        $class = GymClass::query()->create($request->validated());

        return redirect()->route('portal.gym-classes.index')->with('success', 'Gym class created successfully.');
    }

    public function show(GymClass $gymClass): Response
    {
        return Inertia::render('GymClasses/Show', [
            'gymClass' => (new GymClassResource($gymClass->load('trainer')))->resolve(),
        ]);
    }

    public function update(UpdateGymClassRequest $request, GymClass $gymClass)
    {
        $gymClass->fill($request->validated())->save();

        return redirect()->route('portal.gym-classes.index')->with('success', 'Gym class updated successfully.');
    }

    public function destroy(GymClass $gymClass)
    {
        $gymClass->delete();

        return redirect()->route('portal.gym-classes.index')->with('success', 'Gym class deleted successfully.');
    }
}
