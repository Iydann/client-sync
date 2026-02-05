<?php

namespace App\Models;

use App\Models\Project;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Milestone extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'project_id',
        'name',
        'is_completed',
        'order',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
    ];

    // Relationships
    public function project() {
        return $this->belongsTo(Project::class);
    }
    public function tasks() {
        return $this->hasMany(Task::class);
    }

    /**
     * Update milestone completion status based on task progress
     */
    public function updateCompletion(): void
    {
        $totalTasks = $this->tasks()->count();
        
        if ($totalTasks === 0) {
            $this->updateQuietly(['is_completed' => false]);
            return;
        }

        $completedTasks = $this->tasks()->where('is_completed', true)->count();
        $progress = round(($completedTasks / $totalTasks) * 100);
        
        $isCompleted = ($progress === 100);
        $this->updateQuietly(['is_completed' => $isCompleted]);
    }

    protected static function booted(): void
    {
        static::saved(function (Milestone $milestone) {
            $milestone->updateCompletion();
            $milestone->project->updateProgress();
        });
        
        static::deleted(function (Milestone $milestone) {
            $milestone->project->updateProgress();
        });
    }
}   

