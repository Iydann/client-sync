<?php

namespace App\Models;

use App\ProjectStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Project extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'client_id',
        'parent_project_id',
        'title',
        'description',
        'contract_value',
        'progress',
        'payment_progress',
        'contract_date',
        'contract_number',
        'status',
        'start_date',
        'deadline',
        'include_tax',
        'ppn_rate',
        'pph_rate',
        'ppn_amount',
        'pph_amount',
    ];

    protected $casts = [
        'status' => ProjectStatus::class,
        'start_date' => 'date',
        'deadline' => 'date',
        'contract_value' => 'decimal:0',
        'contract_date' => 'date',
        'include_tax' => 'boolean',
        'ppn_rate' => 'decimal:2',
        'pph_rate' => 'decimal:2',
        'ppn_amount' => 'decimal:0',
        'pph_amount' => 'decimal:0',
    ];

    /**
     * Get subtotal (before tax) value
     */
    public function getSubtotalAttribute(): int|float
    {
        if ($this->include_tax) {
            // If include_tax, calculate subtotal by removing tax from contract_value
            $totalTaxPercent = ($this->ppn_rate + $this->pph_rate) / 100;
            return round($this->contract_value / (1 + $totalTaxPercent), 2);
        }
        return $this->contract_value;
    }

    /**
     * Get grand total (after tax) value
     */
    public function getGrandTotalAttribute(): int|float
    {
        if ($this->include_tax) {
            return $this->contract_value;
        }
        return round($this->contract_value + $this->ppn_amount + $this->pph_amount, 2);
    }

    /**
     * Get default tax rates based on client type
     */
    public static function getDefaultTaxRatesByClientType($clientType): array
    {
        $rates = config('tax.rates');
        
        return match((string)$clientType) {
            'individual' => [
                'ppn_rate' => $rates['individual']['ppn'],
                'pph_rate' => $rates['individual']['pph'],
            ],
            'corporate' => [
                'ppn_rate' => $rates['corporate']['ppn'],
                'pph_rate' => $rates['corporate']['pph'],
            ],
            'government' => [
                'ppn_rate' => $rates['government']['ppn'],
                'pph_rate' => $rates['government']['pph'],
            ],
            default => [
                'ppn_rate' => $rates['individual']['ppn'],
                'pph_rate' => $rates['individual']['pph'],
            ],
        };
    }

    /**
     * Calculate tax amounts based on contract_value, rates, and include_tax setting
     */
    private function calculateTaxAmounts(): void
    {
        if ($this->include_tax) {
            // If contract_value includes tax, calculate amounts from it
            $totalTaxPercent = ($this->ppn_rate + $this->pph_rate) / 100;
            $subtotal = $this->contract_value / (1 + $totalTaxPercent);
            
            $this->ppn_amount = round($subtotal * $this->ppn_rate / 100, 2);
            $this->pph_amount = round($subtotal * $this->pph_rate / 100, 2);
        } else {
            // If contract_value is before tax, calculate amounts from it
            $this->ppn_amount = round($this->contract_value * $this->ppn_rate / 100, 2);
            $this->pph_amount = round($this->contract_value * $this->pph_rate / 100, 2);
        }
    }

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
    public function client() {
        return $this->belongsTo(Client::class);
    }
    
    public function parentProject() {
        return $this->belongsTo(Project::class, 'parent_project_id');
    }
    
    public function childProjects() {
        return $this->hasMany(Project::class, 'parent_project_id');
    }
    
    public function relatedProjects()
    {
        // Get the root parent project
        $rootParent = $this->parentProject ?? $this;
        
        // Get all projects linked to this root parent (children + parent itself if this is child)
        return Project::where(function ($query) use ($rootParent) {
            $query->where('id', $rootParent->id)
                  ->orWhere('parent_project_id', $rootParent->id);
        })
        ->where('client_id', $this->client_id)
        ->orderBy('parent_project_id')
        ->orderBy('created_at');
    }
    
    public function milestones() {
        return $this->hasMany(Milestone::class);
    }
    
    public function invoices() {
        return $this->hasMany(Invoice::class);
    }
    
    public function members(){
        return $this->belongsToMany(User::class, 'project_members')
            ->withTimestamps();
    }

    protected static function booted(): void {
        // Auto-calculate tax amounts when relevant fields change
        static::saving(function (Project $project) {
            if ($project->isDirty(['contract_value', 'ppn_rate', 'pph_rate', 'include_tax'])) {
                $project->calculateTaxAmounts();
            }
        });

        static::updated(function (Project $project) {
            if ($project->wasChanged('contract_value')) {
                $project->updatePaymentProgress();
            }
        });
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('project-assets')
            ->useDisk('public');
    }
}
