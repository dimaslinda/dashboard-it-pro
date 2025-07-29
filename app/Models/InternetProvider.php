<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InternetProvider extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'contact_phone',
        'contact_email',
        'website',
        'notes',
        'status',
    ];

    /**
     * Get all WiFi networks for this provider
     */
    public function wifiNetworks()
    {
        return $this->hasMany(WifiNetwork::class, 'provider_id');
    }

    /**
     * Scope for active providers
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
            default => 'secondary'
        };
    }
}
