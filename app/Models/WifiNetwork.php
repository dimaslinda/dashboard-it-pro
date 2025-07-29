<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class WifiNetwork extends Model
{
    use HasFactory;

    protected $fillable = [
        'ssid',
        'password',
        'security_type',
        'frequency_band',
        'channel',
        'location',
        'router_brand',
        'router_model',
        'router_ip',
        'admin_username',
        'admin_password',
        'max_devices',
        'guest_network',
        'guest_ssid',
        'guest_password',
        'notes',
        'status',
    ];

    protected $casts = [
        'channel' => 'integer',
        'max_devices' => 'integer',
        'guest_network' => 'boolean',
    ];

    protected $hidden = [
        'password',
        'admin_password',
        'guest_password',
    ];

    /**
     * Encrypt sensitive fields
     */
    protected function password(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => decrypt($value),
            set: fn ($value) => encrypt($value),
        );
    }

    protected function adminPassword(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? decrypt($value) : null,
            set: fn ($value) => $value ? encrypt($value) : null,
        );
    }

    protected function guestPassword(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? decrypt($value) : null,
            set: fn ($value) => $value ? encrypt($value) : null,
        );
    }

    /**
     * Scope for active networks
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for guest networks
     */
    public function scopeWithGuestNetwork($query)
    {
        return $query->where('guest_network', true);
    }

    /**
     * Get security level badge
     */
    public function getSecurityLevelAttribute(): string
    {
        return match($this->security_type) {
            'WPA3' => 'High',
            'WPA2' => 'Medium',
            'WPA' => 'Low',
            'Open' => 'None',
            default => 'Unknown'
        };
    }

    /**
     * Get security color
     */
    public function getSecurityColorAttribute(): string
    {
        return match($this->security_type) {
            'WPA3' => 'success',
            'WPA2' => 'info',
            'WPA' => 'warning',
            'Open' => 'danger',
            default => 'secondary'
        };
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'active' => 'success',
            'inactive' => 'warning',
            'maintenance' => 'info',
            default => 'secondary'
        };
    }
}