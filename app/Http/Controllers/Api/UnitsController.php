<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DataStore;
use Illuminate\Http\Request;

class UnitsController extends Controller
{
    public function index()
    {
        $data      = DataStore::load();
        $tenantMap = array_column($data['tenants'], null, 'unit_id');

        $units = array_map(fn($u) => array_merge($u, ['tenant' => $tenantMap[$u['id']] ?? null]), $data['units']);
        usort($units, fn($a, $b) => strcmp($a['unit_number'], $b['unit_number']));

        return response()->json(array_values($units));
    }

    public function show(int $id)
    {
        $data      = DataStore::load();
        $unit      = $this->find($data['units'], $id);
        $tenantMap = array_column($data['tenants'], null, 'unit_id');

        return response()->json(array_merge($unit, ['tenant' => $tenantMap[$unit['id']] ?? null]));
    }

    public function update(Request $request, int $id)
    {
        $validated = $request->validate([
            'status' => 'sometimes|in:available,occupied,reserved',
            'notes'  => 'sometimes|nullable|string',
        ]);

        $data  = DataStore::load();
        $index = $this->indexOf($data['units'], $id);
        $data['units'][$index] = array_merge($data['units'][$index], $validated);
        DataStore::save($data);

        $unit      = $data['units'][$index];
        $tenantMap = array_column($data['tenants'], null, 'unit_id');

        return response()->json(array_merge($unit, ['tenant' => $tenantMap[$unit['id']] ?? null]));
    }

    private function find(array $items, int $id): array
    {
        foreach ($items as $item) {
            if ($item['id'] === $id) return $item;
        }
        abort(404);
    }

    private function indexOf(array $items, int $id): int
    {
        foreach ($items as $i => $item) {
            if ($item['id'] === $id) return $i;
        }
        abort(404);
    }
}
