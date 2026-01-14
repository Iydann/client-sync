<?php

namespace App\Models;

use App\ClientType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Client extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'client_type',
        'company_name',
        'phone',
        'address',
    ];

    protected $casts = [
        'client_type' => ClientType::class,
    ];

    // Relationships
    public function user() {
        return $this->belongsTo(User::class);
    }
    public function projects() {
        return $this->hasMany(Project::class);
    }
}
