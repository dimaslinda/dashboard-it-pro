<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

class Equipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'brand',
        'model',
        'serial_number',
        'mac_address',
        'ip_address',
        'location',
        'purchase_date',
        'purchase_price',
        'vendor',
        'warranty_expiry',
        'admin_username',
        'admin_password',
        'firmware_version',
        'last_maintenance',
        'next_maintenance',
        'specifications',
        'notes',
        'status',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'warranty_expiry' => 'date',
        'last_maintenance' => 'date',
        'next_maintenance' => 'date',
        'purchase_price' => 'decimal:2',
    ];

    protected $hidden = [
        'admin_password',
    ];

    /**
     * Encrypt admin password
     */
    protected function adminPassword(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? decrypt($value) : null,
            set: fn ($value) => $value ? encrypt($value) : null,
        );
    }

    /**
     * Scope for active equipment
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope by equipment type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope for CCTV equipment
     */
    public function scopeCctv($query)
    {
        return $query->where('type', 'cctv');
    }

    /**
     * Scope for Router equipment
     */
    public function scopeRouters($query)
    {
        return $query->where('type', 'router');
    }

    /**
     * Check if warranty is expiring soon (within 30 days)
     */
    public function isWarrantyExpiringSoon(): bool
    {
        if (!$this->warranty_expiry) return false;
        return $this->warranty_expiry->diffInDays(now()) <= 30 && $this->warranty_expiry->isFuture();
    }

    /**
     * Check if maintenance is due
     */
    public function isMaintenanceDue(): bool
    {
        if (!$this->next_maintenance) return false;
        return $this->next_maintenance->isPast() || $this->next_maintenance->isToday();
    }

    /**
     * Get equipment age in years
     */
    public function getAgeAttribute(): ?float
    {
        if (!$this->purchase_date) return null;
        return round($this->purchase_date->diffInYears(now(), true), 1);
    }

    /**
     * Get type badge color
     */
    public function getTypeColorAttribute(): string
    {
        return match($this->type) {
            'cctv' => 'info',
            'router' => 'primary',
            'switch' => 'success',
            'firewall' => 'danger',
            'server' => 'warning',
            'printer' => 'secondary',
            'ups' => 'dark',
            default => 'light'
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
            'broken' => 'danger',
            'retired' => 'secondary',
            default => 'light'
        };
    }

    /**
     * Get formatted equipment name
     */
    public function getFullNameAttribute(): string
    {
        $parts = array_filter([$this->brand, $this->model, $this->name]);
        return implode(' - ', $parts);
    }
}