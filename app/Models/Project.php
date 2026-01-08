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
        'status',
        'deadline',
    ];

    protected $casts = [
        'status' => ProjectStatus::class,
        'deadline' => 'date',
    ];

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
}
