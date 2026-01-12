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
        'progress',
        'status',
        'deadline',
    ];

    protected $casts = [
        'status' => ProjectStatus::class,
        'deadline' => 'date',
    ];

    public function updateProgress(): void
    {
        $totalMilestones = $this->milestones()->count();
        if ($totalMilestones === 0) {
            $this->update(['progress' => 0]);
            return;
        }

        $completedMilestones = $this->milestones()->where('is_completed', true)->count();
        $progressPercentage = round(($completedMilestones / $totalMilestones) * 100);
        $this->update(['progress' => $progressPercentage]);
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
}
