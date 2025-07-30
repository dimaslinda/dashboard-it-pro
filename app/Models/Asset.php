<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Asset extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'asset_code',
        'asset_number',
        'name',
        'tool_name',
        'tool_category',
        'subcategory',
        'type',
        'brand',
        'model',
        'serial_number',
        'total_units',
        'location',
        'department',
        'condition',
        'purchase_date',
        'purchase_price',
        'current_value',
        'depreciation_rate',
        'warranty_expiry',
        'last_maintenance',
        'next_maintenance',
        'status',
        'notes',
        'specifications',
        'availability_checklist',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'warranty_expiry' => 'date',
        'last_maintenance' => 'date',
        'next_maintenance' => 'date',
        'purchase_price' => 'decimal:2',
        'current_value' => 'decimal:2',
        'depreciation_rate' => 'decimal:2',
        'specifications' => 'array',
        'availability_checklist' => 'array',
    ];

    /**
     * Get surveys for this asset
     */
    public function surveys(): HasMany
    {
        return $this->hasMany(AssetSurvey::class);
    }

    /**
     * Get loans for this asset
     */
    public function loans(): HasMany
    {
        return $this->hasMany(AssetLoan::class);
    }

    /**
     * Get procurements for this asset
     */
    public function procurements(): HasMany
    {
        return $this->hasMany(AssetProcurement::class);
    }

    /**
     * Get current active loan for this asset
     */
    public function currentLoan()
    {
        return $this->hasOne(AssetLoan::class)
                    ->where('status', 'out')
                    ->whereNull('actual_return_date')
                    ->latest('loan_date');
    }

    /**
     * Check if asset is currently on loan
     */
    public function isCurrentlyOnLoan(): bool
    {
        return $this->currentLoan()->exists();
    }

    /**
     * Get asset availability status
     */
    public function getAvailabilityStatusAttribute(): string
    {
        if ($this->isCurrentlyOnLoan()) {
            return 'On Loan';
        }
        
        return match($this->status) {
            'active' => 'Available',
            'maintenance' => 'Under Maintenance',
            'damaged' => 'Damaged',
            'disposed' => 'Disposed',
            default => 'Unknown'
        };
    }

    /**
     * Get the company that owns the asset.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Scope by asset code prefix (for grouping by organization)
     */
    public function scopeByAssetCodePrefix($query, $prefix)
    {
        return $query->where('asset_code', 'like', $prefix . '%');
    }

    /**
     * Scope a query to filter assets by company.
     */
    public function scopeByCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Scope by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope by condition
     */
    public function scopeByCondition($query, $condition)
    {
        return $query->where('condition', $condition);
    }

    /**
     * Get assets that need maintenance
     */
    public function scopeNeedsMaintenance($query)
    {
        return $query->where('next_maintenance', '<=', now()->addDays(30));
    }

    /**
     * Get assets with expired warranty
     */
    public function scopeExpiredWarranty($query)
    {
        return $query->where('warranty_expiry', '<', now());
    }

    /**
     * Calculate current depreciated value
     */
    public function getDepreciatedValueAttribute()
    {
        if (!$this->purchase_date || !$this->purchase_price || !$this->depreciation_rate) {
            return $this->current_value ?? $this->purchase_price;
        }

        $yearsOld = $this->purchase_date->diffInYears(now());
        $depreciatedAmount = $this->purchase_price * ($this->depreciation_rate / 100) * $yearsOld;
        
        return max(0, $this->purchase_price - $depreciatedAmount);
    }

    /**
     * Get condition badge color
     */
    public function getConditionColorAttribute()
    {
        return match($this->condition) {
            'excellent' => 'success',
            'good' => 'primary',
            'fair' => 'warning',
            'poor' => 'danger',
            'damaged' => 'danger',
            default => 'secondary',
        };
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'active' => 'success',
            'inactive' => 'warning',
            'maintenance' => 'info',
            'retired' => 'danger',
            'disposed' => 'secondary',
            default => 'secondary',
        };
    }
}