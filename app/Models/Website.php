<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

class Website extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'url',
        'domain',
        'hosting_provider',
        'registrar',
        'domain_expiry',
        'hosting_expiry',
        'admin_email',
        'admin_username',
        'admin_password',
        'ftp_host',
        'ftp_username',
        'ftp_password',
        'ftp_port',
        'database_host',
        'database_name',
        'database_username',
        'database_password',
        'notes',
        'status',
    ];

    protected $casts = [
        'domain_expiry' => 'date',
        'hosting_expiry' => 'date',
        'ftp_port' => 'integer',
    ];

    protected $hidden = [
        'admin_password',
        'ftp_password',
        'database_password',
    ];

    /**
     * Encrypt sensitive fields
     */
    protected function adminPassword(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? decrypt($value) : null,
            set: fn ($value) => $value ? encrypt($value) : null,
        );
    }

    protected function ftpPassword(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? decrypt($value) : null,
            set: fn ($value) => $value ? encrypt($value) : null,
        );
    }

    protected function databasePassword(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? decrypt($value) : null,
            set: fn ($value) => $value ? encrypt($value) : null,
        );
    }

    /**
     * Scope for active websites
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Check if domain is expiring soon (within 30 days)
     */
    public function isDomainExpiringSoon(): bool
    {
        if (!$this->domain_expiry) return false;
        return $this->domain_expiry->diffInDays(now()) <= 30 && $this->domain_expiry->isFuture();
    }

    /**
     * Check if hosting is expiring soon (within 30 days)
     */
    public function isHostingExpiringSoon(): bool
    {
        if (!$this->hosting_expiry) return false;
        return $this->hosting_expiry->diffInDays(now()) <= 30 && $this->hosting_expiry->isFuture();
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
            'expired' => 'danger',
            default => 'secondary'
        };
    }
}