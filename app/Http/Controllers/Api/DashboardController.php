<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DataStore;

class DashboardController extends Controller
{
    public function index()
    {
        $data     = DataStore::load();
        $units    = $data['units'];
        $tenants  = $data['tenants'];
        $payments = $data['payments'];

        $totalUnits     = count($units);
        $occupiedUnits  = count(array_filter($units, fn($u) => $u['status'] === 'occupied'));
        $reservedUnits  = count(array_filter($units, fn($u) => $u['status'] === 'reserved'));
        $availableUnits = count(array_filter($units, fn($u) => $u['status'] === 'available'));
        $occupancyRate  = $totalUnits > 0 ? round($occupiedUnits / $totalUnits * 100, 1) : 0;

        $today     = date('Y-m-d');
        $thisMonth = (int) date('m');
        $thisYear  = (int) date('Y');
        $in30Days  = date('Y-m-d', strtotime('+30 days'));

        $monthlyRevenue = array_sum(array_map(
            fn($p) => (float) $p['amount'],
            array_filter($payments, fn($p) =>
                (int) date('m', strtotime($p['payment_date'])) === $thisMonth &&
                (int) date('Y', strtotime($p['payment_date'])) === $thisYear
            )
        ));

        $annualRevenue = array_sum(array_map(
            fn($p) => (float) $p['amount'],
            array_filter($payments, fn($p) =>
                (int) date('Y', strtotime($p['payment_date'])) === $thisYear
            )
        ));

        $activeTenants = count(array_filter($tenants, fn($t) => $t['status'] === 'active'));

        $unitMap = array_column($units, null, 'id');

        $renewals = array_values(array_filter($tenants, fn($t) =>
            $t['status'] === 'active' &&
            $t['lease_end'] !== null &&
            $t['lease_end'] >= $today &&
            $t['lease_end'] <= $in30Days
        ));
        usort($renewals, fn($a, $b) => strcmp($a['lease_end'], $b['lease_end']));
        $upcomingRenewals = array_map(fn($t) => [
            'id'        => $t['id'],
            'name'      => $t['first_name'] . ' ' . $t['last_name'],
            'unit'      => $unitMap[$t['unit_id']]['unit_number'] ?? null,
            'lease_end' => $t['lease_end'],
            'days_left' => (int) round((strtotime($t['lease_end']) - strtotime($today)) / 86400),
        ], $renewals);

        $tenantMap     = array_column($tenants, null, 'id');
        $sortedPayments = $payments;
        usort($sortedPayments, fn($a, $b) => strcmp($b['payment_date'], $a['payment_date']));
        $recentPayments = array_map(fn($p) => [
            'id'     => $p['id'],
            'tenant' => isset($tenantMap[$p['tenant_id']])
                ? $tenantMap[$p['tenant_id']]['first_name'] . ' ' . $tenantMap[$p['tenant_id']]['last_name']
                : null,
            'amount' => (float) $p['amount'],
            'date'   => $p['payment_date'],
            'method' => $p['method'],
        ], array_slice($sortedPayments, 0, 5));

        return response()->json([
            'stats' => [
                'total_units'     => $totalUnits,
                'occupied_units'  => $occupiedUnits,
                'reserved_units'  => $reservedUnits,
                'available_units' => $availableUnits,
                'occupancy_rate'  => $occupancyRate,
                'monthly_revenue' => $monthlyRevenue,
                'annual_revenue'  => $annualRevenue,
                'active_tenants'  => $activeTenants,
            ],
            'upcoming_renewals' => $upcomingRenewals,
            'recent_payments'   => $recentPayments,
        ]);
    }
}
