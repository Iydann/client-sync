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
    ];

    protected $casts = [
        'status' => ProjectStatus::class,
        'start_date' => 'date',
        'deadline' => 'date',
        'contract_value' => 'decimal:0',
        'contract_date' => 'date',
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
