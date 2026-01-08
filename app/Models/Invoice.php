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

    // Relationships
    public function project() {
        return $this->belongsTo(Project::class);
    }

    // Helper methods
    public function isOverdue() {
        return $this->status !== 'paid' && $this->due_date->isPast();
    }
    public function isPaid() {
        return $this->status === 'paid';
    }
}
