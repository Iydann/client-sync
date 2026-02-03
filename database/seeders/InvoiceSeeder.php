<?php

namespace Database\Seeders;
use App\Models\Invoice;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Invoice untuk Project 1
        Invoice::create([
            'project_id' => 1,
            'invoice_number' => 'INV-2026-001',
            'amount' => 15000000.00,
            'status' => 'paid',
            'due_date' => now()->subDays(5),
        ]);

        Invoice::create([
            'project_id' => 1,
            'invoice_number' => 'INV-2026-002',
            'amount' => 10000000.00,
            'status' => 'unpaid',
            'due_date' => now()->addDays(15),
        ]);

        // Invoice untuk Project 2
        Invoice::create([
            'project_id' => 2,
            'invoice_number' => 'INV-2026-003',
            'amount' => 25000000.00,
            'status' => 'unpaid',
            'due_date' => now()->addDays(30),
        ]);

        // Invoice untuk Project 3
        Invoice::create([
            'project_id' => 3,
            'invoice_number' => 'INV-2026-004',
            'amount' => 50000000.00,
            'status' => 'cancelled',
            'due_date' => now()->addDays(20),
        ]);

        Invoice::create([
            'project_id' => 3,
            'invoice_number' => 'INV-2026-005',
            'amount' => 30000000.00,
            'status' => 'paid',
            'due_date' => now()->subDays(10),
        ]);

        // Invoice untuk Project 4 (Completed)
        Invoice::create([
            'project_id' => 4,
            'invoice_number' => 'INV-2026-006',
            'amount' => 35000000.00,
            'status' => 'paid',
            'due_date' => now()->subDays(15),
        ]);

        // Invoice untuk Project 6 (Current)
        Invoice::create([
            'project_id' => 6,
            'invoice_number' => 'INV-2026-007',
            'amount' => 12000000.00,
            'status' => 'paid',
            'due_date' => now()->subDays(2),
        ]);

        Invoice::create([
            'project_id' => 6,
            'invoice_number' => 'INV-2026-008',
            'amount' => 8000000.00,
            'status' => 'unpaid',
            'due_date' => now()->addDays(20),
        ]);
    }
}
