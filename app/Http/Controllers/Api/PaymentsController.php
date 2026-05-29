<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DataStore;
use Illuminate\Http\Request;

class PaymentsController extends Controller
{
    public function index(int $id)
    {
        $data     = DataStore::load();
        $payments = array_values(array_filter($data['payments'], fn($p) => $p['tenant_id'] === $id));
        usort($payments, fn($a, $b) => strcmp($b['payment_date'], $a['payment_date']));
        return response()->json($payments);
    }

    public function store(Request $request, int $id)
    {
        $validated = $request->validate([
            'amount'       => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'period_month' => 'required|integer|min:1|max:12',
            'period_year'  => 'required|integer|min:2000',
            'method'       => 'required|in:cash,card,bank_transfer',
            'notes'        => 'nullable|string',
        ]);

        $data   = DataStore::load();
        $exists = false;
        foreach ($data['tenants'] as $t) {
            if ($t['id'] === $id) { $exists = true; break; }
        }
        if (!$exists) abort(404);

        $paymentId = $data['next_ids']['payment']++;
        $payment   = array_merge(
            ['id' => $paymentId, 'tenant_id' => $id, 'notes' => null],
            $validated,
            ['amount' => (float) $validated['amount']]
        );
        $data['payments'][] = $payment;
        DataStore::save($data);

        return response()->json($payment, 201);
    }
}
