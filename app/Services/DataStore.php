<?php

namespace App\Services;

class DataStore
{
    private static function path(): string
    {
        return storage_path('app/vaultos.json');
    }

    public static function load(): array
    {
        $path = self::path();
        if (!file_exists($path)) {
            $data = self::seed();
            file_put_contents($path, json_encode($data), LOCK_EX);
            return $data;
        }
        return json_decode(file_get_contents($path), true);
    }

    public static function save(array $data): void
    {
        file_put_contents(self::path(), json_encode($data), LOCK_EX);
    }

    private static function seed(): array
    {
        $units = [
            ['id'=>1,  'unit_number'=>'A-101','row_label'=>'A','size_label'=>'Small',      'size_sqft'=>25,  'monthly_rate'=>89.00, 'status'=>'occupied', 'floor'=>1,'notes'=>null],
            ['id'=>2,  'unit_number'=>'A-102','row_label'=>'A','size_label'=>'Small',      'size_sqft'=>25,  'monthly_rate'=>89.00, 'status'=>'occupied', 'floor'=>1,'notes'=>null],
            ['id'=>3,  'unit_number'=>'A-103','row_label'=>'A','size_label'=>'Small',      'size_sqft'=>25,  'monthly_rate'=>89.00, 'status'=>'occupied', 'floor'=>1,'notes'=>null],
            ['id'=>4,  'unit_number'=>'A-104','row_label'=>'A','size_label'=>'Small',      'size_sqft'=>25,  'monthly_rate'=>89.00, 'status'=>'reserved', 'floor'=>1,'notes'=>null],
            ['id'=>5,  'unit_number'=>'A-105','row_label'=>'A','size_label'=>'Small',      'size_sqft'=>25,  'monthly_rate'=>89.00, 'status'=>'available','floor'=>1,'notes'=>null],
            ['id'=>6,  'unit_number'=>'A-106','row_label'=>'A','size_label'=>'Small',      'size_sqft'=>25,  'monthly_rate'=>89.00, 'status'=>'occupied', 'floor'=>1,'notes'=>null],
            ['id'=>7,  'unit_number'=>'B-101','row_label'=>'B','size_label'=>'Medium',     'size_sqft'=>50,  'monthly_rate'=>149.00,'status'=>'occupied', 'floor'=>1,'notes'=>null],
            ['id'=>8,  'unit_number'=>'B-102','row_label'=>'B','size_label'=>'Medium',     'size_sqft'=>50,  'monthly_rate'=>149.00,'status'=>'occupied', 'floor'=>1,'notes'=>null],
            ['id'=>9,  'unit_number'=>'B-103','row_label'=>'B','size_label'=>'Medium',     'size_sqft'=>50,  'monthly_rate'=>149.00,'status'=>'reserved', 'floor'=>1,'notes'=>null],
            ['id'=>10, 'unit_number'=>'B-104','row_label'=>'B','size_label'=>'Medium',     'size_sqft'=>50,  'monthly_rate'=>149.00,'status'=>'occupied', 'floor'=>1,'notes'=>null],
            ['id'=>11, 'unit_number'=>'B-105','row_label'=>'B','size_label'=>'Medium',     'size_sqft'=>50,  'monthly_rate'=>149.00,'status'=>'available','floor'=>1,'notes'=>null],
            ['id'=>12, 'unit_number'=>'B-106','row_label'=>'B','size_label'=>'Medium',     'size_sqft'=>50,  'monthly_rate'=>149.00,'status'=>'occupied', 'floor'=>1,'notes'=>null],
            ['id'=>13, 'unit_number'=>'C-101','row_label'=>'C','size_label'=>'Large',      'size_sqft'=>100, 'monthly_rate'=>229.00,'status'=>'occupied', 'floor'=>1,'notes'=>null],
            ['id'=>14, 'unit_number'=>'C-102','row_label'=>'C','size_label'=>'Large',      'size_sqft'=>100, 'monthly_rate'=>229.00,'status'=>'occupied', 'floor'=>1,'notes'=>null],
            ['id'=>15, 'unit_number'=>'C-103','row_label'=>'C','size_label'=>'Large',      'size_sqft'=>100, 'monthly_rate'=>229.00,'status'=>'occupied', 'floor'=>1,'notes'=>null],
            ['id'=>16, 'unit_number'=>'C-104','row_label'=>'C','size_label'=>'Large',      'size_sqft'=>100, 'monthly_rate'=>229.00,'status'=>'reserved', 'floor'=>1,'notes'=>null],
            ['id'=>17, 'unit_number'=>'C-105','row_label'=>'C','size_label'=>'Large',      'size_sqft'=>100, 'monthly_rate'=>229.00,'status'=>'available','floor'=>1,'notes'=>null],
            ['id'=>18, 'unit_number'=>'C-106','row_label'=>'C','size_label'=>'Large',      'size_sqft'=>100, 'monthly_rate'=>229.00,'status'=>'occupied', 'floor'=>1,'notes'=>null],
            ['id'=>19, 'unit_number'=>'D-101','row_label'=>'D','size_label'=>'Extra Large','size_sqft'=>150, 'monthly_rate'=>319.00,'status'=>'occupied', 'floor'=>1,'notes'=>null],
            ['id'=>20, 'unit_number'=>'D-102','row_label'=>'D','size_label'=>'Extra Large','size_sqft'=>150, 'monthly_rate'=>319.00,'status'=>'occupied', 'floor'=>1,'notes'=>null],
            ['id'=>21, 'unit_number'=>'D-103','row_label'=>'D','size_label'=>'Extra Large','size_sqft'=>150, 'monthly_rate'=>319.00,'status'=>'reserved', 'floor'=>1,'notes'=>null],
            ['id'=>22, 'unit_number'=>'D-104','row_label'=>'D','size_label'=>'Extra Large','size_sqft'=>150, 'monthly_rate'=>319.00,'status'=>'occupied', 'floor'=>1,'notes'=>null],
            ['id'=>23, 'unit_number'=>'D-105','row_label'=>'D','size_label'=>'Extra Large','size_sqft'=>150, 'monthly_rate'=>319.00,'status'=>'available','floor'=>1,'notes'=>null],
            ['id'=>24, 'unit_number'=>'D-106','row_label'=>'D','size_label'=>'Extra Large','size_sqft'=>150, 'monthly_rate'=>319.00,'status'=>'occupied', 'floor'=>1,'notes'=>null],
        ];

        $tenants = [
            ['id'=>1,  'first_name'=>'Aroha',   'last_name'=>'Ngata',      'email'=>'aroha.ngata@email.co.nz',    'phone'=>'021 456 7890','unit_id'=>1,  'lease_start'=>'2025-06-01','lease_end'=>'2026-06-01','status'=>'active','emergency_contact'=>'Hemi Ngata — 021 111 2222',    'notes'=>null],
            ['id'=>2,  'first_name'=>'James',   'last_name'=>'Fitzgerald', 'email'=>'jfitz@xtra.co.nz',           'phone'=>'027 334 8821','unit_id'=>2,  'lease_start'=>'2025-03-15','lease_end'=>'2026-06-10','status'=>'active','emergency_contact'=>'Claire Fitzgerald — 027 334 8822','notes'=>null],
            ['id'=>3,  'first_name'=>'Mei',     'last_name'=>'Wong',       'email'=>'mei.wong@gmail.com',          'phone'=>'022 789 1234','unit_id'=>3,  'lease_start'=>'2025-05-01','lease_end'=>'2026-06-15','status'=>'active','emergency_contact'=>null,                            'notes'=>'Prefers contact by email.'],
            ['id'=>4,  'first_name'=>'Daniel',  'last_name'=>'Parata',     'email'=>'d.parata@outlook.com',        'phone'=>'021 099 3344','unit_id'=>6,  'lease_start'=>'2025-07-01','lease_end'=>'2026-06-20','status'=>'active','emergency_contact'=>'Sina Parata — 021 099 3345',   'notes'=>null],
            ['id'=>5,  'first_name'=>'Sarah',   'last_name'=>'Henderson',  'email'=>'sarah.h@me.com',              'phone'=>'027 611 9900','unit_id'=>7,  'lease_start'=>'2024-11-01','lease_end'=>'2026-11-01','status'=>'active','emergency_contact'=>'Tom Henderson — 027 611 9901', 'notes'=>null],
            ['id'=>6,  'first_name'=>'Tama',    'last_name'=>'Heke',       'email'=>'tama.heke@gmail.com',         'phone'=>'021 222 6655','unit_id'=>8,  'lease_start'=>'2024-08-15','lease_end'=>'2026-08-15','status'=>'active','emergency_contact'=>null,                            'notes'=>'Access code 4412.'],
            ['id'=>7,  'first_name'=>'Lucy',    'last_name'=>'Morrison',   'email'=>'lucy.morrison@yahoo.com',     'phone'=>'022 345 6789','unit_id'=>10, 'lease_start'=>'2024-09-01','lease_end'=>'2026-09-01','status'=>'active','emergency_contact'=>'Greg Morrison — 022 345 6790', 'notes'=>null],
            ['id'=>8,  'first_name'=>'Ravi',    'last_name'=>'Sharma',     'email'=>'ravi.sharma@hotmail.com',     'phone'=>'027 887 4433','unit_id'=>12, 'lease_start'=>'2024-10-15','lease_end'=>'2026-10-15','status'=>'active','emergency_contact'=>'Priya Sharma — 027 887 4434',  'notes'=>null],
            ['id'=>9,  'first_name'=>'Olivia',  'last_name'=>'Campbell',   'email'=>'olivia.campbell@gmail.com',   'phone'=>'021 543 2210','unit_id'=>13, 'lease_start'=>'2024-12-01','lease_end'=>'2026-12-01','status'=>'active','emergency_contact'=>null,                            'notes'=>'Business storage — invoices to olivia.campbell@campbellco.nz'],
            ['id'=>10, 'first_name'=>'Michael', 'last_name'=>'Te Awa',     'email'=>'m.teawa@email.co.nz',         'phone'=>'022 100 5577','unit_id'=>14, 'lease_start'=>'2024-07-01','lease_end'=>'2026-07-01','status'=>'active','emergency_contact'=>'Ngahuia Te Awa — 022 100 5578','notes'=>null],
            ['id'=>11, 'first_name'=>'Fiona',   'last_name'=>'Blackwood',  'email'=>'fblackwood@xtra.co.nz',       'phone'=>'027 765 4320','unit_id'=>15, 'lease_start'=>'2025-01-15','lease_end'=>'2027-01-15','status'=>'active','emergency_contact'=>'Paul Blackwood — 027 765 4321', 'notes'=>null],
            ['id'=>12, 'first_name'=>'Hana',    'last_name'=>'Tūhoe',      'email'=>'hana.tuhoe@gmail.com',        'phone'=>'021 334 7788','unit_id'=>18, 'lease_start'=>'2025-02-01','lease_end'=>'2027-02-01','status'=>'active','emergency_contact'=>null,                            'notes'=>null],
            ['id'=>13, 'first_name'=>'Ben',     'last_name'=>'Crawford',   'email'=>'ben.crawford@outlook.com',    'phone'=>'027 998 1122','unit_id'=>19, 'lease_start'=>'2025-04-01','lease_end'=>'2027-04-01','status'=>'active','emergency_contact'=>'Anne Crawford — 027 998 1123',  'notes'=>null],
            ['id'=>14, 'first_name'=>'Yuki',    'last_name'=>'Tanaka',     'email'=>'yuki.tanaka@me.com',          'phone'=>'022 678 9900','unit_id'=>20, 'lease_start'=>'2025-03-01','lease_end'=>'2027-03-01','status'=>'active','emergency_contact'=>null,                            'notes'=>'Climate-sensitive items — do not stack.'],
        ];

        // Payments: [id, tenant_id, amount, payment_date, period_month, period_year, method]
        $payments = [
            // Tenant 1 — Aroha Ngata ($89)
            ['id'=>1, 'tenant_id'=>1,'amount'=>89.00,'payment_date'=>'2025-12-05','period_month'=>12,'period_year'=>2025,'method'=>'bank_transfer','notes'=>null],
            ['id'=>2, 'tenant_id'=>1,'amount'=>89.00,'payment_date'=>'2026-01-05','period_month'=>1, 'period_year'=>2026,'method'=>'card',         'notes'=>null],
            ['id'=>3, 'tenant_id'=>1,'amount'=>89.00,'payment_date'=>'2026-02-05','period_month'=>2, 'period_year'=>2026,'method'=>'card',         'notes'=>null],
            ['id'=>4, 'tenant_id'=>1,'amount'=>89.00,'payment_date'=>'2026-03-05','period_month'=>3, 'period_year'=>2026,'method'=>'bank_transfer','notes'=>null],
            ['id'=>5, 'tenant_id'=>1,'amount'=>89.00,'payment_date'=>'2026-04-05','period_month'=>4, 'period_year'=>2026,'method'=>'cash',         'notes'=>null],
            ['id'=>6, 'tenant_id'=>1,'amount'=>89.00,'payment_date'=>'2026-05-05','period_month'=>5, 'period_year'=>2026,'method'=>'bank_transfer','notes'=>null],
            // Tenant 2 — James Fitzgerald ($89)
            ['id'=>7, 'tenant_id'=>2,'amount'=>89.00,'payment_date'=>'2026-01-05','period_month'=>1, 'period_year'=>2026,'method'=>'cash','notes'=>null],
            ['id'=>8, 'tenant_id'=>2,'amount'=>89.00,'payment_date'=>'2026-02-05','period_month'=>2, 'period_year'=>2026,'method'=>'cash','notes'=>null],
            ['id'=>9, 'tenant_id'=>2,'amount'=>89.00,'payment_date'=>'2026-03-05','period_month'=>3, 'period_year'=>2026,'method'=>'cash','notes'=>null],
            ['id'=>10,'tenant_id'=>2,'amount'=>89.00,'payment_date'=>'2026-04-05','period_month'=>4, 'period_year'=>2026,'method'=>'card','notes'=>null],
            ['id'=>11,'tenant_id'=>2,'amount'=>89.00,'payment_date'=>'2026-05-05','period_month'=>5, 'period_year'=>2026,'method'=>'cash','notes'=>null],
            // Tenant 3 — Mei Wong ($89)
            ['id'=>12,'tenant_id'=>3,'amount'=>89.00,'payment_date'=>'2026-02-05','period_month'=>2, 'period_year'=>2026,'method'=>'bank_transfer','notes'=>null],
            ['id'=>13,'tenant_id'=>3,'amount'=>89.00,'payment_date'=>'2026-03-05','period_month'=>3, 'period_year'=>2026,'method'=>'bank_transfer','notes'=>null],
            ['id'=>14,'tenant_id'=>3,'amount'=>89.00,'payment_date'=>'2026-04-05','period_month'=>4, 'period_year'=>2026,'method'=>'bank_transfer','notes'=>null],
            ['id'=>15,'tenant_id'=>3,'amount'=>89.00,'payment_date'=>'2026-05-05','period_month'=>5, 'period_year'=>2026,'method'=>'bank_transfer','notes'=>null],
            // Tenant 4 — Daniel Parata ($89)
            ['id'=>16,'tenant_id'=>4,'amount'=>89.00,'payment_date'=>'2025-12-05','period_month'=>12,'period_year'=>2025,'method'=>'card',         'notes'=>null],
            ['id'=>17,'tenant_id'=>4,'amount'=>89.00,'payment_date'=>'2026-01-05','period_month'=>1, 'period_year'=>2026,'method'=>'bank_transfer','notes'=>null],
            ['id'=>18,'tenant_id'=>4,'amount'=>89.00,'payment_date'=>'2026-02-05','period_month'=>2, 'period_year'=>2026,'method'=>'card',         'notes'=>null],
            ['id'=>19,'tenant_id'=>4,'amount'=>89.00,'payment_date'=>'2026-03-05','period_month'=>3, 'period_year'=>2026,'method'=>'card',         'notes'=>null],
            ['id'=>20,'tenant_id'=>4,'amount'=>89.00,'payment_date'=>'2026-04-05','period_month'=>4, 'period_year'=>2026,'method'=>'card',         'notes'=>null],
            // Tenant 5 — Sarah Henderson ($149)
            ['id'=>21,'tenant_id'=>5,'amount'=>149.00,'payment_date'=>'2025-11-05','period_month'=>11,'period_year'=>2025,'method'=>'cash','notes'=>null],
            ['id'=>22,'tenant_id'=>5,'amount'=>149.00,'payment_date'=>'2025-12-05','period_month'=>12,'period_year'=>2025,'method'=>'cash','notes'=>null],
            ['id'=>23,'tenant_id'=>5,'amount'=>149.00,'payment_date'=>'2026-01-05','period_month'=>1, 'period_year'=>2026,'method'=>'card','notes'=>null],
            ['id'=>24,'tenant_id'=>5,'amount'=>149.00,'payment_date'=>'2026-02-05','period_month'=>2, 'period_year'=>2026,'method'=>'cash','notes'=>null],
            ['id'=>25,'tenant_id'=>5,'amount'=>149.00,'payment_date'=>'2026-03-05','period_month'=>3, 'period_year'=>2026,'method'=>'cash','notes'=>null],
            ['id'=>26,'tenant_id'=>5,'amount'=>149.00,'payment_date'=>'2026-04-05','period_month'=>4, 'period_year'=>2026,'method'=>'cash','notes'=>null],
            // Tenant 6 — Tama Heke ($149)
            ['id'=>27,'tenant_id'=>6,'amount'=>149.00,'payment_date'=>'2026-03-05','period_month'=>3,'period_year'=>2026,'method'=>'cash','notes'=>null],
            ['id'=>28,'tenant_id'=>6,'amount'=>149.00,'payment_date'=>'2026-04-05','period_month'=>4,'period_year'=>2026,'method'=>'cash','notes'=>null],
            ['id'=>29,'tenant_id'=>6,'amount'=>149.00,'payment_date'=>'2026-05-05','period_month'=>5,'period_year'=>2026,'method'=>'cash','notes'=>null],
            // Tenant 7 — Lucy Morrison ($149)
            ['id'=>30,'tenant_id'=>7,'amount'=>149.00,'payment_date'=>'2025-12-05','period_month'=>12,'period_year'=>2025,'method'=>'bank_transfer','notes'=>null],
            ['id'=>31,'tenant_id'=>7,'amount'=>149.00,'payment_date'=>'2026-01-05','period_month'=>1, 'period_year'=>2026,'method'=>'bank_transfer','notes'=>null],
            ['id'=>32,'tenant_id'=>7,'amount'=>149.00,'payment_date'=>'2026-02-05','period_month'=>2, 'period_year'=>2026,'method'=>'bank_transfer','notes'=>null],
            ['id'=>33,'tenant_id'=>7,'amount'=>149.00,'payment_date'=>'2026-03-05','period_month'=>3, 'period_year'=>2026,'method'=>'card',         'notes'=>null],
            ['id'=>34,'tenant_id'=>7,'amount'=>149.00,'payment_date'=>'2026-04-05','period_month'=>4, 'period_year'=>2026,'method'=>'bank_transfer','notes'=>null],
            // Tenant 8 — Ravi Sharma ($149)
            ['id'=>35,'tenant_id'=>8,'amount'=>149.00,'payment_date'=>'2026-02-05','period_month'=>2,'period_year'=>2026,'method'=>'card','notes'=>null],
            ['id'=>36,'tenant_id'=>8,'amount'=>149.00,'payment_date'=>'2026-03-05','period_month'=>3,'period_year'=>2026,'method'=>'card','notes'=>null],
            ['id'=>37,'tenant_id'=>8,'amount'=>149.00,'payment_date'=>'2026-04-05','period_month'=>4,'period_year'=>2026,'method'=>'card','notes'=>null],
            ['id'=>38,'tenant_id'=>8,'amount'=>149.00,'payment_date'=>'2026-05-05','period_month'=>5,'period_year'=>2026,'method'=>'card','notes'=>null],
            // Tenant 9 — Olivia Campbell ($229)
            ['id'=>39,'tenant_id'=>9,'amount'=>229.00,'payment_date'=>'2025-11-05','period_month'=>11,'period_year'=>2025,'method'=>'bank_transfer','notes'=>null],
            ['id'=>40,'tenant_id'=>9,'amount'=>229.00,'payment_date'=>'2025-12-05','period_month'=>12,'period_year'=>2025,'method'=>'bank_transfer','notes'=>null],
            ['id'=>41,'tenant_id'=>9,'amount'=>229.00,'payment_date'=>'2026-01-05','period_month'=>1, 'period_year'=>2026,'method'=>'bank_transfer','notes'=>null],
            ['id'=>42,'tenant_id'=>9,'amount'=>229.00,'payment_date'=>'2026-02-05','period_month'=>2, 'period_year'=>2026,'method'=>'bank_transfer','notes'=>null],
            ['id'=>43,'tenant_id'=>9,'amount'=>229.00,'payment_date'=>'2026-03-05','period_month'=>3, 'period_year'=>2026,'method'=>'bank_transfer','notes'=>null],
            ['id'=>44,'tenant_id'=>9,'amount'=>229.00,'payment_date'=>'2026-04-05','period_month'=>4, 'period_year'=>2026,'method'=>'bank_transfer','notes'=>null],
            // Tenant 10 — Michael Te Awa ($229)
            ['id'=>45,'tenant_id'=>10,'amount'=>229.00,'payment_date'=>'2025-12-05','period_month'=>12,'period_year'=>2025,'method'=>'cash','notes'=>null],
            ['id'=>46,'tenant_id'=>10,'amount'=>229.00,'payment_date'=>'2026-01-05','period_month'=>1, 'period_year'=>2026,'method'=>'cash','notes'=>null],
            ['id'=>47,'tenant_id'=>10,'amount'=>229.00,'payment_date'=>'2026-02-05','period_month'=>2, 'period_year'=>2026,'method'=>'card','notes'=>null],
            ['id'=>48,'tenant_id'=>10,'amount'=>229.00,'payment_date'=>'2026-03-05','period_month'=>3, 'period_year'=>2026,'method'=>'cash','notes'=>null],
            ['id'=>49,'tenant_id'=>10,'amount'=>229.00,'payment_date'=>'2026-04-05','period_month'=>4, 'period_year'=>2026,'method'=>'cash','notes'=>null],
            // Tenant 11 — Fiona Blackwood ($229)
            ['id'=>50,'tenant_id'=>11,'amount'=>229.00,'payment_date'=>'2026-02-05','period_month'=>2,'period_year'=>2026,'method'=>'card','notes'=>null],
            ['id'=>51,'tenant_id'=>11,'amount'=>229.00,'payment_date'=>'2026-03-05','period_month'=>3,'period_year'=>2026,'method'=>'card','notes'=>null],
            ['id'=>52,'tenant_id'=>11,'amount'=>229.00,'payment_date'=>'2026-04-05','period_month'=>4,'period_year'=>2026,'method'=>'card','notes'=>null],
            ['id'=>53,'tenant_id'=>11,'amount'=>229.00,'payment_date'=>'2026-05-05','period_month'=>5,'period_year'=>2026,'method'=>'card','notes'=>null],
            // Tenant 12 — Hana Tūhoe ($229)
            ['id'=>54,'tenant_id'=>12,'amount'=>229.00,'payment_date'=>'2026-03-05','period_month'=>3,'period_year'=>2026,'method'=>'bank_transfer','notes'=>null],
            ['id'=>55,'tenant_id'=>12,'amount'=>229.00,'payment_date'=>'2026-04-05','period_month'=>4,'period_year'=>2026,'method'=>'bank_transfer','notes'=>null],
            ['id'=>56,'tenant_id'=>12,'amount'=>229.00,'payment_date'=>'2026-05-05','period_month'=>5,'period_year'=>2026,'method'=>'bank_transfer','notes'=>null],
            // Tenant 13 — Ben Crawford ($319)
            ['id'=>57,'tenant_id'=>13,'amount'=>319.00,'payment_date'=>'2026-02-05','period_month'=>2,'period_year'=>2026,'method'=>'cash','notes'=>null],
            ['id'=>58,'tenant_id'=>13,'amount'=>319.00,'payment_date'=>'2026-03-05','period_month'=>3,'period_year'=>2026,'method'=>'cash','notes'=>null],
            ['id'=>59,'tenant_id'=>13,'amount'=>319.00,'payment_date'=>'2026-04-05','period_month'=>4,'period_year'=>2026,'method'=>'cash','notes'=>null],
            ['id'=>60,'tenant_id'=>13,'amount'=>319.00,'payment_date'=>'2026-05-05','period_month'=>5,'period_year'=>2026,'method'=>'cash','notes'=>null],
            // Tenant 14 — Yuki Tanaka ($319)
            ['id'=>61,'tenant_id'=>14,'amount'=>319.00,'payment_date'=>'2025-12-05','period_month'=>12,'period_year'=>2025,'method'=>'card','notes'=>null],
            ['id'=>62,'tenant_id'=>14,'amount'=>319.00,'payment_date'=>'2026-01-05','period_month'=>1, 'period_year'=>2026,'method'=>'card','notes'=>null],
            ['id'=>63,'tenant_id'=>14,'amount'=>319.00,'payment_date'=>'2026-02-05','period_month'=>2, 'period_year'=>2026,'method'=>'card','notes'=>null],
            ['id'=>64,'tenant_id'=>14,'amount'=>319.00,'payment_date'=>'2026-03-05','period_month'=>3, 'period_year'=>2026,'method'=>'card','notes'=>null],
            ['id'=>65,'tenant_id'=>14,'amount'=>319.00,'payment_date'=>'2026-04-05','period_month'=>4, 'period_year'=>2026,'method'=>'card','notes'=>null],
        ];

        return [
            'units'    => $units,
            'tenants'  => $tenants,
            'payments' => $payments,
            'next_ids' => ['unit' => 25, 'tenant' => 15, 'payment' => 66],
        ];
    }
}
