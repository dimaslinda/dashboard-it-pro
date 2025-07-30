<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetSurvey extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id',
        'user_id',
        'surveyor_name',
        'surveyor_position',
        'survey_date',
        'survey_type',
        'condition_assessment',
        'physical_condition',
        'functional_status',
        'condition_good_count',
        'condition_bad_count',
        'availability_status',
        'usage_frequency',
        'maintenance_required',
        'maintenance_priority',
        'maintenance_needs',
        'estimated_repair_cost',
        'recommendations',
        'photos',
        'location_verified',
        'location_notes',
        'surveyor_notes',
        'checklist_results',
        'next_survey_date',
        'status',
    ];

    protected $casts = [
        'survey_date' => 'date',
        'next_survey_date' => 'date',
        'estimated_repair_cost' => 'decimal:2',
        'maintenance_required' => 'boolean',
        'location_verified' => 'boolean',
        'photos' => 'array',
        'recommendations' => 'array',
        'checklist_results' => 'array',
    ];

    /**
     * Get the asset being surveyed
     */
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    /**
     * Get the user who conducted the survey
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope by asset code prefix (for grouping by organization)
     */
    public function scopeByAssetCodePrefix($query, $prefix)
    {
        return $query->whereHas('asset', function($q) use ($prefix) {
            $q->where('asset_code', 'like', $prefix . '%');
        });
    }

    /**
     * Scope by survey type
     */
    public function scopeBySurveyType($query, $type)
    {
        return $query->where('survey_type', $type);
    }

    /**
     * Scope by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope surveys that require maintenance
     */
    public function scopeRequiresMaintenance($query)
    {
        return $query->where('maintenance_required', true);
    }

    /**
     * Scope surveys by priority
     */
    public function scopeByPriority($query, $priority)
    {
        return $query->where('maintenance_priority', $priority);
    }

    /**
     * Get condition assessment color
     */
    public function getConditionColorAttribute()
    {
        return match($this->condition_assessment) {
            'excellent' => 'success',
            'good' => 'primary',
            'fair' => 'warning',
            'poor' => 'danger',
            'critical' => 'danger',
            default => 'secondary',
        };
    }

    /**
     * Get maintenance priority color
     */
    public function getPriorityColorAttribute()
    {
        return match($this->maintenance_priority) {
            'low' => 'success',
            'medium' => 'warning',
            'high' => 'danger',
            'urgent' => 'danger',
            default => 'secondary',
        };
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'completed' => 'success',
            'in_progress' => 'warning',
            'pending' => 'info',
            'cancelled' => 'danger',
            default => 'secondary',
        };
    }
}