<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ImpactStat;
use Illuminate\Http\Request;

class ImpactStatController extends Controller
{
    public function index(Request $request)
    {
        $query = ImpactStat::query();

        if ($request->boolean('active_only', true)) {
            $query->where('is_active', true);
        }

        $query->orderBy('order_index')->orderBy('id');

        return $query->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'label' => ['required', 'string', 'max:255'],
            'value' => ['required', 'string', 'max:255'],
            'unit' => ['nullable', 'string', 'max:50'],
            'order_index' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $stat = ImpactStat::create($data);

        return response()->json($stat, 201);
    }

    public function show(ImpactStat $impact_stat)
    {
        return $impact_stat;
    }

    public function update(Request $request, ImpactStat $impact_stat)
    {
        $data = $request->validate([
            'label' => ['sometimes', 'required', 'string', 'max:255'],
            'value' => ['nullable', 'string', 'max:255'],
            'unit' => ['nullable', 'string', 'max:50'],
            'order_index' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $impact_stat->update($data);

        return $impact_stat;
    }

    public function destroy(ImpactStat $impact_stat)
    {
        $impact_stat->delete();

        return response()->noContent();
    }
}
