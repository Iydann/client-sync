<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectRequestMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_request_id',
        'user_id',
        'message',
    ];

    public function projectRequest(): BelongsTo
    {
        return $this->belongsTo(ProjectRequest::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected static function booted(): void
    {
        static::created(function (ProjectRequestMessage $message) {
            $message->projectRequest?->updateQuietly([
                'last_message_at' => now(),
            ]);
        });
    }
}
