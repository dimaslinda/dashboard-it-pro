<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetProcurement extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id',
        'requester_name',
        'requester_position',
        'request_date',
        'item_name',
        'item_specification',
        'quantity',
        'unit_price',
        'total_price',
        'supplier_name',
        'supplier_contact',
        'justification',
        'urgency_level',
        'budget_source',
        'expected_delivery_date',
        'actual_delivery_date',
        'status', // 'pending', 'approved', 'rejected', 'ordered', 'delivered', 'completed'
        'approval_notes',
        'approved_by',
        'approval_date',
        'purchase_order_number',
        'invoice_number',
        'delivery_notes',
        'condition_received',
        'warranty_period',
        'warranty_expiry_date',
    ];

    protected $casts = [
        'request_date' => 'date',
        'expected_delivery_date' => 'date',
        'actual_delivery_date' => 'date',
        'approval_date' => 'date',
        'warranty_expiry_date' => 'date',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'quantity' => 'integer',
    ];

    /**
     * Get the related asset (if replacing existing asset)
     */
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    /**
     * Get the user who approved the procurement
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Scope by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope pending approvals
     */
    public function scopePendingApproval($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope by urgency level
     */
    public function scopeByUrgency($query, $urgency)
    {
        return $query->where('urgency_level', $urgency);
    }

    /**
     * Get total value of procurement
     */
    public function getTotalValueAttribute(): float
    {
        return $this->quantity * $this->unit_price;
    }

    /**
     * Check if procurement is overdue
     */
    public function getIsOverdueAttribute(): bool
    {
        return $this->status === 'ordered' && 
               $this->expected_delivery_date < now() && 
               is_null($this->actual_delivery_date);
    }

    /**
     * Get delivery status
     */
    public function getDeliveryStatusAttribute(): string
    {
        if ($this->actual_delivery_date) {
            return $this->actual_delivery_date <= $this->expected_delivery_date ? 'On Time' : 'Late';
        }
        
        if ($this->status === 'ordered') {
            return $this->expected_delivery_date < now() ? 'Overdue' : 'Pending';
        }
        
        return 'Not Ordered';
    }

    /**
     * Auto-calculate total price
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($procurement) {
            if ($procurement->quantity && $procurement->unit_price) {
                $procurement->total_price = $procurement->quantity * $procurement->unit_price;
            }
        });
    }
}