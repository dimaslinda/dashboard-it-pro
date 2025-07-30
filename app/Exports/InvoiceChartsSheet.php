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
use Carbon\Carbon;

class InvoiceChartsSheet implements FromArray, WithStyles, WithColumnWidths, WithTitle
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
        
        // Service type data for pie chart
        $serviceData = $invoices->groupBy('service_type')->map(function ($group, $type) {
            return [
                'type' => $this->getServiceTypeLabel($type),
                'count' => $group->count(),
                'amount' => $group->sum('total_amount'),
            ];
        })->values();
        
        // Status data for pie chart
        $statusData = $invoices->groupBy('status')->map(function ($group, $status) {
            return [
                'status' => $this->getStatusLabel($status),
                'count' => $group->count(),
                'amount' => $group->sum('total_amount'),
            ];
        })->values();
        
        // Monthly trend data for line chart
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
            ['GRAFIK ANALISIS FAKTUR', '', '', '', '', ''],
            ['Petunjuk: Pilih data di bawah untuk membuat grafik secara manual di Excel', '', '', '', '', ''],
            ['', '', '', '', '', ''],
            
            // Service Type Chart Data
            ['DISTRIBUSI BERDASARKAN JENIS LAYANAN', '', '', '', '', ''],
            ['Jenis Layanan', 'Jumlah', 'Nilai (Juta)', '', '', ''],
        ];
        
        foreach ($serviceData as $service) {
            $data[] = [
                $service['type'],
                $service['count'],
                round($service['amount'] / 1000000, 2), // Convert to millions
                '',
                '',
                ''
            ];
        }
        
        $data[] = ['', '', '', '', '', ''];
        $data[] = ['', '', '', '', '', ''];
        
        // Status Chart Data
        $data[] = ['DISTRIBUSI BERDASARKAN STATUS', '', '', '', '', ''];
        $data[] = ['Status', 'Jumlah', 'Nilai (Juta)', '', '', ''];
        
        foreach ($statusData as $status) {
            $data[] = [
                $status['status'],
                $status['count'],
                round($status['amount'] / 1000000, 2), // Convert to millions
                '',
                '',
                ''
            ];
        }
        
        $data[] = ['', '', '', '', '', ''];
        $data[] = ['', '', '', '', '', ''];
        
        // Monthly Trend Chart Data
        $data[] = ['TREN BULANAN', '', '', '', '', ''];
        $data[] = ['Bulan', 'Jumlah Faktur', 'Total Nilai (Juta)', 'Dibayar (Juta)', '', ''];
        
        foreach ($monthlyData as $month) {
            $data[] = [
                $month['month'],
                $month['count'],
                round($month['amount'] / 1000000, 2),
                round($month['paid'] / 1000000, 2),
                '',
                ''
            ];
        }
        
        return $data;
    }
    
    public function styles(Worksheet $sheet)
    {
        return [
            // Main header
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 16,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '1F2937'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
            
            // Section headers
            3 => $this->getSectionHeaderStyle(),
            4 => $this->getTableHeaderStyle(),
            
            // Data styling
            'A:F' => [
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
            
            // Number columns
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
            'A' => 20,
            'B' => 12,
            'C' => 15,
            'D' => 15,
            'E' => 3,
            'F' => 3,
        ];
    }
    
    public function title(): string
    {
        return 'Grafik';
    }
    
    private function getSectionHeaderStyle()
    {
        return [
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4F46E5'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ];
    }
    
    private function getTableHeaderStyle()
    {
        return [
            'font' => [
                'bold' => true,
                'size' => 10,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '6B7280'],
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