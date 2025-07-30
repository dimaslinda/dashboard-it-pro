<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class AssetLoan extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id',
        'borrower_name',
        'borrower_position',
        'pic_name',
        'pic_contact',
        'loan_date',
        'expected_return_date',
        'actual_return_date',
        'status', // 'out', 'in', 'overdue'
        'purpose',
        'location_used',
        'calibration_count',
        'calibration_date',
        'condition_out',
        'condition_in',
        'notes',
        'approved_by',
        'approval_date',
    ];

    protected $casts = [
        'loan_date' => 'date',
        'expected_return_date' => 'date',
        'actual_return_date' => 'date',
        'calibration_date' => 'date',
        'approval_date' => 'date',
        'calibration_count' => 'integer',
    ];

    /**
     * Get the asset being loaned
     */
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    /**
     * Get the user who approved the loan
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Check if asset is currently on loan
     */
    public function scopeCurrentlyOnLoan($query)
    {
        return $query->where('status', 'out')
                    ->whereNull('actual_return_date');
    }

    /**
     * Check if loan is overdue
     */
    public function scopeOverdue($query)
    {
        return $query->where('status', 'out')
                    ->where('expected_return_date', '<', now())
                    ->whereNull('actual_return_date');
    }

    /**
     * Get loans by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Check if loan is overdue
     */
    public function getIsOverdueAttribute(): bool
    {
        return $this->status === 'out' && 
               $this->expected_return_date < now() && 
               is_null($this->actual_return_date);
    }

    /**
     * Get loan duration in days
     */
    public function getLoanDurationAttribute(): int
    {
        $endDate = $this->actual_return_date ?? now();
        return $this->loan_date->diffInDays($endDate);
    }

    /**
     * Auto-update status based on dates
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($loan) {
            if ($loan->actual_return_date) {
                $loan->status = 'in';
            } elseif ($loan->expected_return_date < now() && $loan->status === 'out') {
                $loan->status = 'overdue';
            }
        });
    }
}