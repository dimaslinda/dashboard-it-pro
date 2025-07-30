<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Carbon\Carbon;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Invoice extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'invoice_number',
        'client_name',
        'client_email',
        'client_phone',
        'client_address',
        'service_type',
        'description',
        'amount',
        'tax_amount',
        'total_amount',
        'invoice_date',
        'due_date',
        'paid_date',
        'status',
        'payment_method',
        'payment_notes',
        'reference_id',
        'reference_type',
        'notes',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'paid_date' => 'date',
        'amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    /**
     * Get the reference model (website, equipment, etc.)
     */
    public function reference(): MorphTo
    {
        return $this->morphTo('reference', 'reference_type', 'reference_id');
    }

    /**
     * Scope untuk invoice yang sudah dibayar
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    /**
     * Scope untuk invoice dalam tahun tertentu
     */
    public function scopeInYear($query, $year)
    {
        return $query->whereYear('paid_date', $year);
    }

    /**
     * Scope untuk invoice dalam bulan tertentu
     */
    public function scopeInMonth($query, $year, $month)
    {
        return $query->whereYear('paid_date', $year)
                    ->whereMonth('paid_date', $month);
    }

    /**
     * Generate nomor invoice otomatis
     */
    public static function generateInvoiceNumber()
    {
        $year = date('Y');
        $month = date('m');
        
        // Cari invoice terakhir berdasarkan pattern nomor invoice
        $lastInvoice = self::where('invoice_number', 'like', 'INV-' . $year . $month . '-%')
                          ->orderBy('invoice_number', 'desc')
                          ->first();
        
        $sequence = $lastInvoice ? (int)substr($lastInvoice->invoice_number, -4) + 1 : 1;
        
        // Pastikan nomor invoice unik dengan mengecek duplikasi
        do {
            $invoiceNumber = 'INV-' . $year . $month . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);
            $exists = self::where('invoice_number', $invoiceNumber)->exists();
            if ($exists) {
                $sequence++;
            }
        } while ($exists);
        
        return $invoiceNumber;
    }

    /**
     * Get total pembayaran per tahun
     */
    public static function getYearlyTotal($year)
    {
        return self::paid()->inYear($year)->sum('total_amount');
    }

    /**
     * Get total pembayaran per bulan dalam tahun tertentu
     */
    public static function getMonthlyTotals($year)
    {
        $monthlyTotals = [];
        
        for ($month = 1; $month <= 12; $month++) {
            $monthlyTotals[$month] = self::paid()->inMonth($year, $month)->sum('total_amount');
        }
        
        return $monthlyTotals;
    }

    /**
     * Get pembayaran berdasarkan tipe layanan dalam tahun tertentu
     */
    public static function getServiceTypeTotals($year)
    {
        return self::paid()
                  ->inYear($year)
                  ->selectRaw('service_type, SUM(total_amount) as total')
                  ->groupBy('service_type')
                  ->pluck('total', 'service_type')
                  ->toArray();
    }

    /**
     * Get statistik pembayaran untuk dashboard
     */
    public static function getPaymentStats($year = null)
    {
        $year = $year ?? date('Y');
        
        return [
            'total_paid' => self::getYearlyTotal($year),
            'total_invoices' => self::paid()->inYear($year)->count(),
            'monthly_totals' => self::getMonthlyTotals($year),
            'service_type_totals' => self::getServiceTypeTotals($year),
            'average_invoice' => self::paid()->inYear($year)->avg('total_amount') ?? 0,
        ];
    }

    /**
     * Mark invoice as paid
     */
    public function markAsPaid($paymentMethod = null, $paymentNotes = null)
    {
        $this->update([
            'status' => 'paid',
            'paid_date' => now(),
            'payment_method' => $paymentMethod,
            'payment_notes' => $paymentNotes,
        ]);
    }

    /**
     * Check if invoice is overdue
     */
    public function isOverdue()
    {
        return $this->status !== 'paid' && $this->due_date < now();
    }

    /**
     * Get formatted invoice number
     */
    public function getFormattedInvoiceNumberAttribute()
    {
        return $this->invoice_number;
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'paid' => 'success',
            'sent' => 'info',
            'overdue' => 'danger',
            'cancelled' => 'gray',
            default => 'warning'
        };
    }

    /**
     * Register media collections
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('invoices')
            ->acceptsMimeTypes(['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'])
            ->singleFile();
    }
}