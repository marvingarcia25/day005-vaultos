<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;

class PaymentsController extends Controller
{
    public function index(Tenant $tenant)
    {
        return response()->json($tenant->payments);
    }

    public function store(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'amount'       => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'period_month' => 'required|integer|min:1|max:12',
            'period_year'  => 'required|integer|min:2000',
            'method'       => 'required|in:cash,card,bank_transfer',
            'notes'        => 'nullable|string',
        ]);

        $payment = $tenant->payments()->create($validated);

        return response()->json($payment, 201);
    }
}
