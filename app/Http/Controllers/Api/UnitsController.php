<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use Illuminate\Http\Request;

class UnitsController extends Controller
{
    public function index()
    {
        $units = Unit::with('tenant:id,first_name,last_name,email,phone,lease_start,lease_end,unit_id')
            ->orderBy('row_label')
            ->orderBy('unit_number')
            ->get();

        return response()->json($units);
    }

    public function show(Unit $unit)
    {
        return response()->json($unit->load('tenant'));
    }

    public function update(Request $request, Unit $unit)
    {
        $validated = $request->validate([
            'status' => 'sometimes|in:available,occupied,reserved',
            'notes'  => 'sometimes|nullable|string',
        ]);

        $unit->update($validated);

        return response()->json($unit->fresh('tenant'));
    }
}
