<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Task extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected $fillable = [
        'milestone_id',
        'user_id',
        'name',
        'is_completed',
        'order',
        'description',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
    ];

    public function milestone()
    {
        return $this->belongsTo(Milestone::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function contributors()
    {
        return $this->belongsToMany(User::class, 'task_user')->withTimestamps();
    }

    protected static function booted(): void
    {
        static::saved(function (Task $task) {
            if ($task->milestone) {
                $task->milestone->updateCompletion();
            }
        });
        
        static::deleted(function (Task $task) {
            if ($task->milestone) {
                $task->milestone->updateCompletion();
            }
        });
    }
}