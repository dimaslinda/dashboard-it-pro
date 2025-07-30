<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
class ProviderContract extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider_id',
        'company_name',
        'monthly_cost',
        'installation_cost',
        'speed_package',
        'bandwidth_mbps',
        'connection_type',
        'service_expiry_date',
        'contract_start_date',
        'contract_duration_months',
        'contract_status',
        'notes',
    ];

    protected $casts = [
        'monthly_cost' => 'decimal:2',
        'installation_cost' => 'decimal:2',
        'bandwidth_mbps' => 'integer',
        'service_expiry_date' => 'date',
        'contract_start_date' => 'date',
        'contract_duration_months' => 'integer',
    ];

    /**
     * Get the provider that owns this contract
     */
    public function provider()
    {
        return $this->belongsTo(InternetProvider::class);
    }

    /**
     * Get all WiFi networks for this contract
     */
    public function wifiNetworks()
    {
        return $this->hasMany(WifiNetwork::class, 'contract_id');
    }

    /**
     * Scope for active contracts
     */
    public function scopeActive($query)
    {
        return $query->where('contract_status', 'active');
    }

    /**
     * Scope for expired contracts
     */
    public function scopeExpired($query)
    {
        return $query->where('service_expiry_date', '<', now());
    }

    /**
     * Scope for expiring soon contracts
     */
    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->whereBetween('service_expiry_date', [now(), now()->addDays($days)]);
    }

    /**
     * Check if contract is expired
     */
    public function isExpired()
    {
        return $this->service_expiry_date && $this->service_expiry_date->isPast();
    }

    /**
     * Check if contract is expiring soon (within 30 days)
     */
    public function isExpiringSoon($days = 30)
    {
        if (!$this->service_expiry_date) {
            return false;
        }
        
        return $this->service_expiry_date->isFuture() && 
               $this->service_expiry_date->diffInDays(now()) <= $days;
    }

    /**
     * Get days until expiry
     */
    public function daysUntilExpiry()
    {
        if (!$this->service_expiry_date) {
            return null;
        }
        
        return now()->diffInDays($this->service_expiry_date, false);
    }

    /**
     * Get formatted expiry status
     */
    public function getExpiryStatusAttribute()
    {
        $days = $this->daysUntilExpiry();
        
        if ($days === null) {
            return 'No Date';
        }
        
        if ($days < 0) {
            return 'Expired';
        }
        
        if ($days <= 7) {
            return 'Critical';
        }
        
        if ($days <= 30) {
            return 'Warning';
        }
        
        return 'Active';
    }


}