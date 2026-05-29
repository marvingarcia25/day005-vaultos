<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DataStore;
use Illuminate\Http\Request;

class TenantsController extends Controller
{
    public function index(Request $request)
    {
        $data    = DataStore::load();
        $unitMap = array_column($data['units'], null, 'id');
        $list    = $data['tenants'];

        if ($request->filled('status')) {
            $s    = strtolower($request->status);
            $list = array_filter($list, fn($t) => $t['status'] === $s);
        }

        if ($request->filled('search')) {
            $q    = strtolower($request->search);
            $list = array_filter($list, fn($t) =>
                str_contains(strtolower($t['first_name'] . ' ' . $t['last_name']), $q) ||
                str_contains(strtolower($t['email']), $q)
            );
        }

        usort($list, fn($a, $b) => strcmp($a['last_name'], $b['last_name']));

        $list = array_map(fn($t) => array_merge($t, [
            'unit' => isset($t['unit_id']) ? ($unitMap[$t['unit_id']] ?? null) : null,
        ]), $list);

        return response()->json(array_values($list));
    }

    public function show(int $id)
    {
        $data    = DataStore::load();
        $unitMap = array_column($data['units'], null, 'id');
        $tenant  = $this->find($data['tenants'], $id);

        $payments = array_values(array_filter($data['payments'], fn($p) => $p['tenant_id'] === $id));
        usort($payments, fn($a, $b) => strcmp($b['payment_date'], $a['payment_date']));

        return response()->json(array_merge($tenant, [
            'unit'     => isset($tenant['unit_id']) ? ($unitMap[$tenant['unit_id']] ?? null) : null,
            'payments' => $payments,
        ]));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name'        => 'required|string|max:100',
            'last_name'         => 'required|string|max:100',
            'email'             => 'required|email',
            'phone'             => 'required|string|max:30',
            'unit_id'           => 'nullable|integer',
            'lease_start'       => 'nullable|date',
            'lease_end'         => 'nullable|date',
            'emergency_contact' => 'nullable|string|max:200',
            'notes'             => 'nullable|string',
        ]);

        $data = DataStore::load();

        foreach ($data['tenants'] as $t) {
            if (strtolower($t['email']) === strtolower($validated['email'])) {
                return response()->json(['errors' => ['email' => ['The email has already been taken.']]], 422);
            }
        }

        $id = $data['next_ids']['tenant']++;
        $tenant = array_merge([
            'id'                => $id,
            'unit_id'           => null,
            'lease_start'       => null,
            'lease_end'         => null,
            'emergency_contact' => null,
            'notes'             => null,
            'status'            => 'active',
        ], $validated);

        $data['tenants'][] = $tenant;

        if ($tenant['unit_id']) {
            foreach ($data['units'] as &$unit) {
                if ($unit['id'] === (int) $tenant['unit_id']) {
                    $unit['status'] = 'occupied';
                    break;
                }
            }
        }

        DataStore::save($data);

        $unitMap = array_column($data['units'], null, 'id');
        return response()->json(array_merge($tenant, [
            'unit' => isset($tenant['unit_id']) ? ($unitMap[$tenant['unit_id']] ?? null) : null,
        ]), 201);
    }

    public function update(Request $request, int $id)
    {
        $validated = $request->validate([
            'status'            => 'sometimes|in:active,inactive',
            'phone'             => 'sometimes|string|max:30',
            'lease_end'         => 'sometimes|nullable|date',
            'emergency_contact' => 'sometimes|nullable|string|max:200',
            'notes'             => 'sometimes|nullable|string',
        ]);

        $data  = DataStore::load();
        $index = $this->indexOf($data['tenants'], $id);
        $data['tenants'][$index] = array_merge($data['tenants'][$index], $validated);
        DataStore::save($data);

        $unitMap = array_column($data['units'], null, 'id');
        $tenant  = $data['tenants'][$index];
        return response()->json(array_merge($tenant, [
            'unit' => isset($tenant['unit_id']) ? ($unitMap[$tenant['unit_id']] ?? null) : null,
        ]));
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
