<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\Unit;
use Illuminate\Http\Request;

class TenantsController extends Controller
{
    public function index(Request $request)
    {
        $query = Tenant::with('unit:id,unit_number,size_label,monthly_rate')
            ->orderBy('last_name');

        if ($request->filled('search')) {
            $pattern = '%' . $request->search . '%';
            $query->where(function ($q) use ($pattern) {
                $q->where('first_name', 'like', $pattern)
                  ->orWhere('last_name', 'like', $pattern)
                  ->orWhere('email', 'like', $pattern);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return response()->json($query->get());
    }

    public function show(Tenant $tenant)
    {
        return response()->json($tenant->load(['unit', 'payments']));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name'        => 'required|string|max:100',
            'last_name'         => 'required|string|max:100',
            'email'             => 'required|email|unique:tenants',
            'phone'             => 'required|string|max:30',
            'unit_id'           => 'nullable|exists:units,id',
            'lease_start'       => 'nullable|date',
            'lease_end'         => 'nullable|date|after:lease_start',
            'emergency_contact' => 'nullable|string|max:200',
            'notes'             => 'nullable|string',
        ]);

        $tenant = Tenant::create(array_merge($validated, ['status' => 'active']));

        if ($tenant->unit_id) {
            Unit::find($tenant->unit_id)->update(['status' => 'occupied']);
        }

        return response()->json($tenant->load('unit'), 201);
    }

    public function update(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'status'            => 'sometimes|in:active,inactive',
            'phone'             => 'sometimes|string|max:30',
            'lease_end'         => 'sometimes|nullable|date',
            'emergency_contact' => 'sometimes|nullable|string|max:200',
            'notes'             => 'sometimes|nullable|string',
        ]);

        $tenant->update($validated);

        return response()->json($tenant->fresh('unit'));
    }
}
