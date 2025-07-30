<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Website extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

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
     * Scope for websites with domain expiring in specific days
     */
    public function scopeDomainExpiringIn($query, int $days)
    {
        $targetDate = now()->addDays($days);
        return $query->whereDate('domain_expiry', $targetDate)
                    ->where('status', '!=', 'expired');
    }

    /**
     * Scope for websites with hosting expiring in specific days
     */
    public function scopeHostingExpiringIn($query, int $days)
    {
        $targetDate = now()->addDays($days);
        return $query->whereDate('hosting_expiry', $targetDate)
                    ->where('status', '!=', 'expired');
    }

    /**
     * Get days until domain expiry
     */
    public function getDaysUntilDomainExpiry(): ?int
    {
        if (!$this->domain_expiry) return null;
        return now()->diffInDays($this->domain_expiry, false);
    }

    /**
     * Get days until hosting expiry
     */
    public function getDaysUntilHostingExpiry(): ?int
    {
        if (!$this->hosting_expiry) return null;
        return now()->diffInDays($this->hosting_expiry, false);
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