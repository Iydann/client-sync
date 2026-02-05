<?php

namespace App\Models;

use App\InvoiceStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Invoice extends Model
{
    use HasFactory;
    protected $fillable = [
        'project_id',
        'invoice_number',
        'amount',
        'status',
        'due_date',
        'sent_at',
        'ppn_rate',
        'pph_rate',
        'ppn_amount',
        'pph_amount',
        'include_tax',
    ];

    protected $casts = [
        'due_date' => 'date',
        'amount' => 'decimal:0',
        'status' => InvoiceStatus::class,
        'sent_at' => 'datetime',
        'ppn_rate' => 'decimal:2',
        'pph_rate' => 'decimal:2',
        'ppn_amount' => 'decimal:0',
        'pph_amount' => 'decimal:0',
        'include_tax' => 'boolean',
    ];

    // generate invoice number
    public static function generateInvoiceNumber(): string
    {
        $year = now()->year;

        $lastInvoice = self::query()
            ->whereYear('created_at', $year)
            ->where('invoice_number', 'like', "INV-{$year}-%")
            ->orderBy('invoice_number', 'desc')
            ->lockForUpdate()
            ->first();

        $nextNumber = 1;

        if ($lastInvoice) {
            $parts = explode('-', $lastInvoice->invoice_number);
            $nextNumber = ((int) ($parts[2] ?? 0)) + 1;
        }

        return sprintf('INV-%s-%03d', $year, $nextNumber);
    }
    
    // preview invoice number before creating
    public static function previewInvoiceNumber(): string
    {
        return self::generateInvoiceNumber();
    }

    // Relationships
    public function project() {
        return $this->belongsTo(Project::class);
    }

    public function invoices() {
        return $this->project->invoices();
    }

    // Helper methods
    public function isPaid() {
        return $this->status === InvoiceStatus::Paid;
    }

    protected static function booted(): void
    {
        // Capture tax snapshot from project when creating invoice
        static::creating(function (Invoice $invoice) {
            if ($invoice->project) {
                if ($invoice->ppn_rate === null) {
                    $invoice->ppn_rate = $invoice->project->ppn_rate;
                }
                if ($invoice->pph_rate === null) {
                    $invoice->pph_rate = $invoice->project->pph_rate;
                }
                if ($invoice->include_tax === null) {
                    $invoice->include_tax = $invoice->project->include_tax;
                }
            }

            $ppnRate = (float) ($invoice->ppn_rate ?? 0);
            $pphRate = (float) ($invoice->pph_rate ?? 0);
            $amount = (float) ($invoice->amount ?? 0);
            $includeTax = (bool) ($invoice->include_tax ?? false);

            if ($amount > 0 && ($invoice->ppn_amount === null || $invoice->pph_amount === null)) {
                $totalTaxPercent = ($ppnRate + $pphRate) / 100;
                
                $subtotal = $totalTaxPercent > 0 ? ($amount / (1 + $totalTaxPercent)) : $amount;
                
                $invoice->ppn_amount = round($subtotal * $ppnRate / 100, 0);
                $invoice->pph_amount = round($subtotal * $pphRate / 100, 0);
            }
        });

        static::saved(function (Invoice $invoice) {
            $invoice->project->updatePaymentProgress();
        });
        
        static::deleted(function (Invoice $invoice) {
            $invoice->project->updatePaymentProgress();
        });
    }

}