<?php

namespace App\Models;

use App\ProjectStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'title',
        'description',
        'contract_value',
        'progress',
        'payment_progress',
        'status',
        'deadline',
    ];

    protected $casts = [
        'status' => ProjectStatus::class,
        'deadline' => 'date',
        'contract_value' => 'decimal:0',
    ];

    public function updateProgress(): void
    {
        $totalMilestones = $this->milestones()->count();
        if ($totalMilestones === 0) {
            $this->updateQuietly(['progress' => 0]);
            return;
        }

        $completedMilestones = $this->milestones()->where('is_completed', true)->count();
        $progressPercentage = round(($completedMilestones / $totalMilestones) * 100);
        $this->updateQuietly(['progress' => $progressPercentage]);
    }
    
    public function updatePaymentProgress(): void
    {
        if ($this->contract_value == 0) {
            $this->updateQuietly(['payment_progress' => 0]);
            return;
        }

        $paidAmount = $this->invoices()->where('status', 'paid')->sum('amount');
        $paymentProgressPercentage = round(($paidAmount / $this->contract_value) * 100);
        $this->updateQuietly(['payment_progress' => $paymentProgressPercentage]);
    }

    // Relationships
    public function client()
    {
        return $this->belongsTo(Client::class);
    }
    
    public function milestones()
    {
        return $this->hasMany(Milestone::class);
    }
    
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
    
    public function members()
    {
        return $this->belongsToMany(User::class, 'project_members')
            ->withTimestamps();
    }

    protected static function booted(): void
    {
        static::updated(function (Project $project) {
            if ($project->wasChanged('contract_value')) {
                $project->updatePaymentProgress();
            }
        });
    }
}
