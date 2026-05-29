<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the database with 24 demo units, 14 tenants, and realistic payment history.
     *
     * Unit layout:
     *   Row A (6): Small   — 25 sqft  @ $89/mo
     *   Row B (6): Medium  — 50 sqft  @ $149/mo
     *   Row C (6): Large   — 100 sqft @ $229/mo
     *   Row D (6): X-Large — 150 sqft @ $319/mo
     *
     * Statuses: 14 occupied, 4 reserved, 6 available spread across all rows.
     */
    public function run(): void
    {
        $this->seedUnits();
        $this->seedTenants();
        $this->seedPayments();
    }

    // -------------------------------------------------------------------------
    // Units
    // -------------------------------------------------------------------------

    private function seedUnits(): void
    {
        $now = now();

        $rows = [
            'A' => ['size_label' => 'Small',       'size_sqft' => 25,  'monthly_rate' => 89.00],
            'B' => ['size_label' => 'Medium',      'size_sqft' => 50,  'monthly_rate' => 149.00],
            'C' => ['size_label' => 'Large',       'size_sqft' => 100, 'monthly_rate' => 229.00],
            'D' => ['size_label' => 'Extra Large', 'size_sqft' => 150, 'monthly_rate' => 319.00],
        ];

        // Predetermined statuses per unit (14 occupied, 4 reserved, 6 available)
        // Format: row => [unit_index_1..6 => status]
        $statusMap = [
            'A' => ['occupied', 'occupied', 'occupied', 'reserved', 'available', 'occupied'],
            'B' => ['occupied', 'occupied', 'reserved', 'occupied', 'available', 'occupied'],
            'C' => ['occupied', 'occupied', 'occupied', 'reserved', 'available', 'occupied'],
            'D' => ['occupied', 'occupied', 'reserved', 'occupied', 'available', 'occupied'],
        ];

        $units = [];
        foreach ($rows as $rowLabel => $spec) {
            for ($i = 1; $i <= 6; $i++) {
                $status = $statusMap[$rowLabel][$i - 1];
                $units[] = [
                    'unit_number'  => $rowLabel . '-10' . $i,
                    'row_label'    => $rowLabel,
                    'size_label'   => $spec['size_label'],
                    'size_sqft'    => $spec['size_sqft'],
                    'monthly_rate' => $spec['monthly_rate'],
                    'status'       => $status,
                    'floor'        => 1,
                    'notes'        => null,
                    'created_at'   => $now,
                    'updated_at'   => $now,
                ];
            }
        }

        DB::table('units')->insert($units);
    }

    // -------------------------------------------------------------------------
    // Tenants
    // -------------------------------------------------------------------------

    private function seedTenants(): void
    {
        $now = now();

        // Map each occupied unit to a tenant. The DB auto-increments IDs starting
        // at 1, so we resolve unit IDs by unit_number after insertion.
        $occupiedUnits = DB::table('units')->where('status', 'occupied')->orderBy('id')->pluck('id')->all();

        // 14 real NZ-flavoured tenants.
        // lease_end: first 4 expire within 30 days of today (2026-05-29).
        $tenants = [
            [
                'first_name'        => 'Aroha',
                'last_name'         => 'Ngata',
                'email'             => 'aroha.ngata@email.co.nz',
                'phone'             => '021 456 7890',
                'lease_start'       => '2025-06-01',
                'lease_end'         => '2026-06-01',   // upcoming: 3 days away
                'emergency_contact' => 'Hemi Ngata — 021 111 2222',
                'notes'             => null,
            ],
            [
                'first_name'        => 'James',
                'last_name'         => 'Fitzgerald',
                'email'             => 'jfitz@xtra.co.nz',
                'phone'             => '027 334 8821',
                'lease_start'       => '2025-03-15',
                'lease_end'         => '2026-06-10',   // upcoming: 12 days away
                'emergency_contact' => 'Claire Fitzgerald — 027 334 8822',
                'notes'             => null,
            ],
            [
                'first_name'        => 'Mei',
                'last_name'         => 'Wong',
                'email'             => 'mei.wong@gmail.com',
                'phone'             => '022 789 1234',
                'lease_start'       => '2025-05-01',
                'lease_end'         => '2026-06-15',   // upcoming: 17 days away
                'emergency_contact' => null,
                'notes'             => 'Prefers contact by email.',
            ],
            [
                'first_name'        => 'Daniel',
                'last_name'         => 'Parata',
                'email'             => 'd.parata@outlook.com',
                'phone'             => '021 099 3344',
                'lease_start'       => '2025-07-01',
                'lease_end'         => '2026-06-20',   // upcoming: 22 days away
                'emergency_contact' => 'Sina Parata — 021 099 3345',
                'notes'             => null,
            ],
            [
                'first_name'        => 'Sarah',
                'last_name'         => 'Henderson',
                'email'             => 'sarah.h@me.com',
                'phone'             => '027 611 9900',
                'lease_start'       => '2024-11-01',
                'lease_end'         => '2026-11-01',
                'emergency_contact' => 'Tom Henderson — 027 611 9901',
                'notes'             => null,
            ],
            [
                'first_name'        => 'Tama',
                'last_name'         => 'Heke',
                'email'             => 'tama.heke@gmail.com',
                'phone'             => '021 222 6655',
                'lease_start'       => '2024-08-15',
                'lease_end'         => '2026-08-15',
                'emergency_contact' => null,
                'notes'             => 'Access code 4412.',
            ],
            [
                'first_name'        => 'Lucy',
                'last_name'         => 'Morrison',
                'email'             => 'lucy.morrison@yahoo.com',
                'phone'             => '022 345 6789',
                'lease_start'       => '2024-09-01',
                'lease_end'         => '2026-09-01',
                'emergency_contact' => 'Greg Morrison — 022 345 6790',
                'notes'             => null,
            ],
            [
                'first_name'        => 'Ravi',
                'last_name'         => 'Sharma',
                'email'             => 'ravi.sharma@hotmail.com',
                'phone'             => '027 887 4433',
                'lease_start'       => '2024-10-15',
                'lease_end'         => '2026-10-15',
                'emergency_contact' => 'Priya Sharma — 027 887 4434',
                'notes'             => null,
            ],
            [
                'first_name'        => 'Olivia',
                'last_name'         => 'Campbell',
                'email'             => 'olivia.campbell@gmail.com',
                'phone'             => '021 543 2210',
                'lease_start'       => '2024-12-01',
                'lease_end'         => '2026-12-01',
                'emergency_contact' => null,
                'notes'             => 'Business storage — invoices to olivia.campbell@campbellco.nz',
            ],
            [
                'first_name'        => 'Michael',
                'last_name'         => 'Te Awa',
                'email'             => 'm.teawa@email.co.nz',
                'phone'             => '022 100 5577',
                'lease_start'       => '2024-07-01',
                'lease_end'         => '2026-07-01',
                'emergency_contact' => 'Ngahuia Te Awa — 022 100 5578',
                'notes'             => null,
            ],
            [
                'first_name'        => 'Fiona',
                'last_name'         => 'Blackwood',
                'email'             => 'fblackwood@xtra.co.nz',
                'phone'             => '027 765 4320',
                'lease_start'       => '2025-01-15',
                'lease_end'         => '2027-01-15',
                'emergency_contact' => 'Paul Blackwood — 027 765 4321',
                'notes'             => null,
            ],
            [
                'first_name'        => 'Hana',
                'last_name'         => 'Tūhoe',
                'email'             => 'hana.tuhoe@gmail.com',
                'phone'             => '021 334 7788',
                'lease_start'       => '2025-02-01',
                'lease_end'         => '2027-02-01',
                'emergency_contact' => null,
                'notes'             => null,
            ],
            [
                'first_name'        => 'Ben',
                'last_name'         => 'Crawford',
                'email'             => 'ben.crawford@outlook.com',
                'phone'             => '027 998 1122',
                'lease_start'       => '2025-04-01',
                'lease_end'         => '2027-04-01',
                'emergency_contact' => 'Anne Crawford — 027 998 1123',
                'notes'             => null,
            ],
            [
                'first_name'        => 'Yuki',
                'last_name'         => 'Tanaka',
                'email'             => 'yuki.tanaka@me.com',
                'phone'             => '022 678 9900',
                'lease_start'       => '2025-03-01',
                'lease_end'         => '2027-03-01',
                'emergency_contact' => null,
                'notes'             => 'Climate-sensitive items — do not stack.',
            ],
        ];

        foreach ($tenants as $index => $tenant) {
            DB::table('tenants')->insert(array_merge($tenant, [
                'unit_id'    => $occupiedUnits[$index],
                'status'     => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ]));
        }
    }

    // -------------------------------------------------------------------------
    // Payments
    // -------------------------------------------------------------------------

    private function seedPayments(): void
    {
        $now = now();

        // Fetch all tenants with their unit monthly_rate so we can produce
        // realistic payment amounts. Ordered by id to match insertion order.
        $tenants = DB::table('tenants')
            ->join('units', 'tenants.unit_id', '=', 'units.id')
            ->orderBy('tenants.id')
            ->select('tenants.id as tenant_id', 'units.monthly_rate', 'tenants.lease_start')
            ->get();

        $methods = ['cash', 'card', 'bank_transfer'];

        // Payment history per tenant: each entry is [year, month, method_index]
        // Covers the last 3-6 months of payments (prior to or including May 2026).
        $paymentSchedules = [
            // Tenant 1 — Aroha Ngata (6 payments)
            [[2025, 12, 2], [2026, 1, 1], [2026, 2, 1], [2026, 3, 2], [2026, 4, 0], [2026, 5, 2]],
            // Tenant 2 — James Fitzgerald (5 payments)
            [[2026, 1, 0], [2026, 2, 0], [2026, 3, 0], [2026, 4, 1], [2026, 5, 0]],
            // Tenant 3 — Mei Wong (4 payments)
            [[2026, 2, 2], [2026, 3, 2], [2026, 4, 2], [2026, 5, 2]],
            // Tenant 4 — Daniel Parata (5 payments)
            [[2025, 12, 1], [2026, 1, 2], [2026, 2, 1], [2026, 3, 1], [2026, 4, 1]],
            // Tenant 5 — Sarah Henderson (6 payments)
            [[2025, 11, 0], [2025, 12, 0], [2026, 1, 1], [2026, 2, 0], [2026, 3, 0], [2026, 4, 0]],
            // Tenant 6 — Tama Heke (3 payments)
            [[2026, 3, 0], [2026, 4, 0], [2026, 5, 0]],
            // Tenant 7 — Lucy Morrison (5 payments)
            [[2025, 12, 2], [2026, 1, 2], [2026, 2, 2], [2026, 3, 1], [2026, 4, 2]],
            // Tenant 8 — Ravi Sharma (4 payments)
            [[2026, 2, 1], [2026, 3, 1], [2026, 4, 1], [2026, 5, 1]],
            // Tenant 9 — Olivia Campbell (6 payments)
            [[2025, 11, 2], [2025, 12, 2], [2026, 1, 2], [2026, 2, 2], [2026, 3, 2], [2026, 4, 2]],
            // Tenant 10 — Michael Te Awa (5 payments)
            [[2025, 12, 0], [2026, 1, 0], [2026, 2, 1], [2026, 3, 0], [2026, 4, 0]],
            // Tenant 11 — Fiona Blackwood (4 payments)
            [[2026, 2, 1], [2026, 3, 1], [2026, 4, 1], [2026, 5, 1]],
            // Tenant 12 — Hana Tūhoe (3 payments)
            [[2026, 3, 2], [2026, 4, 2], [2026, 5, 2]],
            // Tenant 13 — Ben Crawford (4 payments)
            [[2026, 2, 0], [2026, 3, 0], [2026, 4, 0], [2026, 5, 0]],
            // Tenant 14 — Yuki Tanaka (5 payments)
            [[2025, 12, 1], [2026, 1, 1], [2026, 2, 1], [2026, 3, 1], [2026, 4, 1]],
        ];

        $payments = [];

        foreach ($tenants as $index => $tenant) {
            $schedule = $paymentSchedules[$index] ?? [];
            foreach ($schedule as [$year, $month, $methodIndex]) {
                // Payment is recorded on the 5th of each month for tidiness
                $paymentDate = sprintf('%04d-%02d-05', $year, $month);
                $payments[]  = [
                    'tenant_id'    => $tenant->tenant_id,
                    'amount'       => $tenant->monthly_rate,
                    'payment_date' => $paymentDate,
                    'period_month' => $month,
                    'period_year'  => $year,
                    'method'       => $methods[$methodIndex],
                    'notes'        => null,
                    'created_at'   => $now,
                    'updated_at'   => $now,
                ];
            }
        }

        DB::table('payments')->insert($payments);
    }
}
