<?php

namespace App\Models;

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
    ];

    protected $casts = [
        'due_date' => 'date',
        'amount' => 'decimal:2',
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

    // Helper methods
    public function isPaid() {
        return $this->status === 'paid';
    }
}
