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
            'ppn_rate' => 11.00,
            'pph_rate' => 2.50,
            'ppn_amount' => 1323529.41,
            'pph_amount' => 301470.59,
            'include_tax' => true,
            'status' => 'paid',
            'due_date' => now()->subDays(5),
        ]);

        Invoice::create([
            'project_id' => 1,
            'invoice_number' => 'INV-2026-002',
            'amount' => 10000000.00,
            'ppn_rate' => 11.00,
            'pph_rate' => 2.50,
            'ppn_amount' => 882352.94,
            'pph_amount' => 200980.39,
            'include_tax' => true,
            'status' => 'unpaid',
            'due_date' => now()->addDays(15),
        ]);

        // Invoice untuk Project 2
        Invoice::create([
            'project_id' => 2,
            'invoice_number' => 'INV-2026-003',
            'amount' => 25000000.00,
            'ppn_rate' => 11.00,
            'pph_rate' => 2.50,
            'ppn_amount' => 2205882.35,
            'pph_amount' => 501960.78,
            'include_tax' => true,
            'status' => 'unpaid',
            'due_date' => now()->addDays(30),
        ]);

        // Invoice untuk Project 3
        Invoice::create([
            'project_id' => 3,
            'invoice_number' => 'INV-2026-004',
            'amount' => 50000000.00,
            'ppn_rate' => 11.00,
            'pph_rate' => 2.00,
            'ppn_amount' => 4424778.76,
            'pph_amount' => 806451.61,
            'include_tax' => true,
            'status' => 'cancelled',
            'due_date' => now()->addDays(20),
        ]);

        Invoice::create([
            'project_id' => 3,
            'invoice_number' => 'INV-2026-005',
            'amount' => 30000000.00,
            'ppn_rate' => 11.00,
            'pph_rate' => 2.00,
            'ppn_amount' => 2654867.26,
            'pph_amount' => 483870.97,
            'include_tax' => true,
            'status' => 'paid',
            'due_date' => now()->subDays(10),
        ]);

        // Invoice untuk Project 4 (Completed)
        Invoice::create([
            'project_id' => 4,
            'invoice_number' => 'INV-2026-006',
            'amount' => 35000000.00,
            'ppn_rate' => 11.00,
            'pph_rate' => 2.50,
            'ppn_amount' => 3088235.29,
            'pph_amount' => 701960.78,
            'include_tax' => true,
            'status' => 'paid',
            'due_date' => now()->subDays(15),
        ]);

        // Invoice untuk Project 6 (Current)
        Invoice::create([
            'project_id' => 6,
            'invoice_number' => 'INV-2026-007',
            'amount' => 12000000.00,
            'ppn_rate' => 11.00,
            'pph_rate' => 2.50,
            'ppn_amount' => 1058823.53,
            'pph_amount' => 240784.31,
            'include_tax' => true,
            'status' => 'paid',
            'due_date' => now()->subDays(2),
        ]);

        Invoice::create([
            'project_id' => 6,
            'invoice_number' => 'INV-2026-008',
            'amount' => 8000000.00,
            'ppn_rate' => 11.00,
            'pph_rate' => 2.50,
            'ppn_amount' => 705882.35,
            'pph_amount' => 160522.88,
            'include_tax' => true,
            'status' => 'unpaid',
            'due_date' => now()->addDays(20),
        ]);
    }
}
