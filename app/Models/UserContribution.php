<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserContribution extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'value',
        'year',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function milestones()
    {
        return $this->hasMany(Milestone::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
}