<?php

namespace App\Models;

use App\ProjectRequestStatus;
use App\ProjectRequestType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class ProjectRequest extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'project_id',
        'client_id',
        'created_by',
        'type',
        'title',
        'description',
        'status',
        'last_message_at',
    ];

    protected $casts = [
        'type' => ProjectRequestType::class,
        'status' => ProjectRequestStatus::class,
        'last_message_at' => 'datetime',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }


    public function isDiscussionClosed(): bool
    {
        return in_array($this->status, [ProjectRequestStatus::Rejected, ProjectRequestStatus::Completed], true);
    }

    public function isDiscussionOpenForParticipants(): bool
    {
        return in_array($this->status, [
            ProjectRequestStatus::InProgress,
        ], true);
    }

    protected static function booted(): void
    {
        static::creating(function (ProjectRequest $request) {
            if (!$request->last_message_at) {
                $request->last_message_at = now();
            }
        });
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('request-attachments')
            ->useDisk('public');
    }
}
