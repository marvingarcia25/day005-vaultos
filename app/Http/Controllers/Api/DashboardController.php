<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Tenant;
use App\Models\Unit;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUnits     = Unit::count();
        $occupiedUnits  = Unit::where('status', 'occupied')->count();
        $reservedUnits  = Unit::where('status', 'reserved')->count();
        $availableUnits = Unit::where('status', 'available')->count();
        $occupancyRate  = $totalUnits > 0 ? round(($occupiedUnits / $totalUnits) * 100, 1) : 0;

        $now = Carbon::now();

        $monthlyRevenue = Payment::whereMonth('payment_date', $now->month)
            ->whereYear('payment_date', $now->year)
            ->sum('amount');

        $annualRevenue = Payment::whereYear('payment_date', $now->year)->sum('amount');

        $upcomingRenewals = Tenant::with('unit')
            ->where('status', 'active')
            ->whereNotNull('lease_end')
            ->whereBetween('lease_end', [$now, $now->copy()->addDays(30)])
            ->orderBy('lease_end')
            ->get()
            ->map(fn($tenant) => [
                'id'        => $tenant->id,
                'name'      => $tenant->full_name,
                'unit'      => $tenant->unit?->unit_number,
                'lease_end' => $tenant->lease_end->format('Y-m-d'),
                'days_left' => (int) $now->diffInDays($tenant->lease_end, false),
            ]);

        $recentPayments = Payment::with('tenant')
            ->orderByDesc('payment_date')
            ->limit(5)
            ->get()
            ->map(fn($payment) => [
                'id'     => $payment->id,
                'tenant' => $payment->tenant?->full_name,
                'amount' => (float) $payment->amount,
                'date'   => $payment->payment_date->format('Y-m-d'),
                'method' => $payment->method,
            ]);

        return response()->json([
            'stats' => [
                'total_units'     => $totalUnits,
                'occupied_units'  => $occupiedUnits,
                'reserved_units'  => $reservedUnits,
                'available_units' => $availableUnits,
                'occupancy_rate'  => $occupancyRate,
                'monthly_revenue' => (float) $monthlyRevenue,
                'annual_revenue'  => (float) $annualRevenue,
                'active_tenants'  => Tenant::where('status', 'active')->count(),
            ],
            'upcoming_renewals' => $upcomingRenewals,
            'recent_payments'   => $recentPayments,
        ]);
    }
}
