<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class EmailAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'password',
        'provider',
        'smtp_server',
        'smtp_port',
        'imap_server',
        'imap_port',
        'ssl_enabled',
        'department',
        'assigned_to',
        'notes',
        'status',
    ];

    protected $casts = [
        'ssl_enabled' => 'boolean',
        'smtp_port' => 'integer',
        'imap_port' => 'integer',
    ];

    protected $hidden = [
        'password',
    ];

    /**
     * Get the password attribute (encrypted)
     */
    protected function password(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => decrypt($value),
            set: fn ($value) => encrypt($value),
        );
    }

    /**
     * Scope for active email accounts
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'active' => 'success',
            'inactive' => 'warning',
            'suspended' => 'danger',
            default => 'secondary'
        };
    }
}