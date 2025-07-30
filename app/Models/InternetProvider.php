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

    protected $casts = [
        //
    ];

    /**
     * Get all WiFi networks for this provider
     */
    public function wifiNetworks()
    {
        return $this->hasMany(WifiNetwork::class, 'provider_id');
    }

    /**
     * Get all contracts for this provider
     */
    public function contracts()
    {
        return $this->hasMany(ProviderContract::class);
    }

    /**
     * Get all provider contracts for this provider
     */
    public function providerContracts()
    {
        return $this->hasMany(ProviderContract::class, 'provider_id');
    }

    /**
     * Get active contracts for this provider
     */
    public function activeContracts()
    {
        return $this->hasMany(ProviderContract::class, 'provider_id')->active();
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

    /**
     * Check if any active contract is expired
     */
    public function isExpired()
    {
        $activeContract = $this->activeContracts()->first();
        return $activeContract ? $activeContract->isExpired() : false;
    }

    /**
     * Check if any active contract is expiring soon
     */
    public function isExpiringSoon($days = 30)
    {
        $activeContract = $this->activeContracts()->first();
        return $activeContract ? $activeContract->isExpiringSoon($days) : false;
    }

    /**
     * Get service expiry date from active contract
     */
    public function getServiceExpiryDateAttribute()
    {
        $activeContract = $this->activeContracts()->first();
        return $activeContract ? $activeContract->service_expiry_date : null;
    }

    /**
     * Get monthly cost from active contract
     */
    public function getMonthlyCostAttribute()
    {
        $activeContract = $this->activeContracts()->first();
        return $activeContract ? $activeContract->monthly_cost : null;
    }
}
