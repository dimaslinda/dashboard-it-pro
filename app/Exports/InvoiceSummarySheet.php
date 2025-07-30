<?php

namespace App\Exports;

use App\Models\Invoice;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use Carbon\Carbon;

class InvoiceSummarySheet implements FromArray, WithStyles, WithColumnWidths, WithTitle
{
    protected $filters;
    
    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }
    
    public function array(): array
    {
        $query = Invoice::query();
        
        // Apply filters
        if (isset($this->filters['status']) && $this->filters['status']) {
            $query->where('status', $this->filters['status']);
        }
        
        if (isset($this->filters['service_type']) && $this->filters['service_type']) {
            $query->where('service_type', $this->filters['service_type']);
        }
        
        if (isset($this->filters['date_from']) && $this->filters['date_from']) {
            $query->where('invoice_date', '>=', $this->filters['date_from']);
        }
        
        if (isset($this->filters['date_to']) && $this->filters['date_to']) {
            $query->where('invoice_date', '<=', $this->filters['date_to']);
        }
        
        $invoices = $query->get();
        
        // Calculate summary data
        $totalInvoices = $invoices->count();
        $totalAmount = $invoices->sum('total_amount');
        $paidInvoices = $invoices->where('status', 'paid')->count();
        $paidAmount = $invoices->where('status', 'paid')->sum('total_amount');
        $overdueInvoices = $invoices->where('status', 'overdue')->count();
        $overdueAmount = $invoices->where('status', 'overdue')->sum('total_amount');
        $pendingAmount = $totalAmount - $paidAmount;
        
        // Service type breakdown
        $serviceBreakdown = $invoices->groupBy('service_type')->map(function ($group, $type) {
            return [
                'type' => $this->getServiceTypeLabel($type),
                'count' => $group->count(),
                'amount' => $group->sum('total_amount'),
            ];
        })->values()->toArray();
        
        // Status breakdown
        $statusBreakdown = $invoices->groupBy('status')->map(function ($group, $status) {
            return [
                'status' => $this->getStatusLabel($status),
                'count' => $group->count(),
                'amount' => $group->sum('total_amount'),
            ];
        })->values()->toArray();
        
        // Monthly breakdown (last 12 months)
        $monthlyData = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthInvoices = $invoices->filter(function ($invoice) use ($date) {
                return Carbon::parse($invoice->invoice_date)->format('Y-m') === $date->format('Y-m');
            });
            
            $monthlyData[] = [
                'month' => $date->format('M Y'),
                'count' => $monthInvoices->count(),
                'amount' => $monthInvoices->sum('total_amount'),
                'paid' => $monthInvoices->where('status', 'paid')->sum('total_amount'),
            ];
        }
        
        $data = [
            ['RINGKASAN FAKTUR', '', '', ''],
            ['Periode:', $this->getPeriodText(), '', ''],
            ['Tanggal Export:', Carbon::now()->format('d/m/Y H:i'), '', ''],
            ['', '', '', ''],
            
            ['STATISTIK UMUM', '', '', ''],
            ['Total Faktur', $totalInvoices, '', ''],
            ['Total Nilai', 'Rp ' . number_format($totalAmount, 0, ',', '.'), '', ''],
            ['Faktur Dibayar', $paidInvoices, '', ''],
            ['Nilai Dibayar', 'Rp ' . number_format($paidAmount, 0, ',', '.'), '', ''],
            ['Faktur Overdue', $overdueInvoices, '', ''],
            ['Nilai Overdue', 'Rp ' . number_format($overdueAmount, 0, ',', '.'), '', ''],
            ['Nilai Pending', 'Rp ' . number_format($pendingAmount, 0, ',', '.'), '', ''],
            ['', '', '', ''],
            
            ['BREAKDOWN BERDASARKAN LAYANAN', '', '', ''],
            ['Jenis Layanan', 'Jumlah', 'Total Nilai', ''],
        ];
        
        foreach ($serviceBreakdown as $service) {
            $data[] = [
                $service['type'],
                $service['count'],
                'Rp ' . number_format($service['amount'], 0, ',', '.'),
                ''
            ];
        }
        
        $data[] = ['', '', '', ''];
        $data[] = ['BREAKDOWN BERDASARKAN STATUS', '', '', ''];
        $data[] = ['Status', 'Jumlah', 'Total Nilai', ''];
        
        foreach ($statusBreakdown as $status) {
            $data[] = [
                $status['status'],
                $status['count'],
                'Rp ' . number_format($status['amount'], 0, ',', '.'),
                ''
            ];
        }
        
        $data[] = ['', '', '', ''];
        $data[] = ['TREN BULANAN (12 BULAN TERAKHIR)', '', '', ''];
        $data[] = ['Bulan', 'Jumlah', 'Total Nilai', 'Dibayar'];
        
        foreach ($monthlyData as $month) {
            $data[] = [
                $month['month'],
                $month['count'],
                'Rp ' . number_format($month['amount'], 0, ',', '.'),
                'Rp ' . number_format($month['paid'], 0, ',', '.')
            ];
        }
        
        return $data;
    }
    
    public function styles(Worksheet $sheet)
    {
        return [
            // Main headers
            1 => $this->getHeaderStyle(),
            5 => $this->getHeaderStyle(),
            15 => $this->getHeaderStyle(),
            16 => $this->getSubHeaderStyle(),
            count($this->array()) - 25 => $this->getHeaderStyle(),
            count($this->array()) - 24 => $this->getSubHeaderStyle(),
            count($this->array()) - 12 => $this->getHeaderStyle(),
            count($this->array()) - 11 => $this->getSubHeaderStyle(),
            
            // General styling
            'A:D' => [
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
            
            // Amount columns
            'B:D' => [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_RIGHT,
                ],
            ],
        ];
    }
    
    public function columnWidths(): array
    {
        return [
            'A' => 25,
            'B' => 15,
            'C' => 20,
            'D' => 20,
        ];
    }
    
    public function title(): string
    {
        return 'Ringkasan';
    }
    
    private function getHeaderStyle()
    {
        return [
            'font' => [
                'bold' => true,
                'size' => 14,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1F2937'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ];
    }
    
    private function getSubHeaderStyle()
    {
        return [
            'font' => [
                'bold' => true,
                'size' => 11,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4F46E5'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];
    }
    
    private function getPeriodText()
    {
        if (isset($this->filters['date_from']) && isset($this->filters['date_to'])) {
            return Carbon::parse($this->filters['date_from'])->format('d/m/Y') . ' - ' . 
                   Carbon::parse($this->filters['date_to'])->format('d/m/Y');
        }
        
        return 'Semua Data';
    }
    
    private function getServiceTypeLabel($type)
    {
        $types = [
            'domain' => 'Domain',
            'hosting' => 'Hosting',
            'wifi' => 'WiFi/Internet',
            'equipment' => 'Peralatan',
            'maintenance' => 'Pemeliharaan',
            'consultation' => 'Konsultasi',
            'development' => 'Pengembangan',
            'support' => 'Dukungan',
            'electric_token' => 'Token Listrik',
            'other' => 'Lainnya',
        ];
        
        return $types[$type] ?? $type;
    }
    
    private function getStatusLabel($status)
    {
        $statuses = [
            'draft' => 'Draft',
            'sent' => 'Terkirim',
            'paid' => 'Dibayar',
            'overdue' => 'Terlambat',
            'cancelled' => 'Dibatalkan',
        ];
        
        return $statuses[$status] ?? $status;
    }
}