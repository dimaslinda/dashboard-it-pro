<?php

namespace App\Exports;

use App\Models\Invoice;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use App\Exports\InvoiceSummarySheet;
use App\Exports\InvoiceChartsSheet;

class InvoicesExport implements WithMultipleSheets
{
    protected $filters;
    
    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }
    
    public function sheets(): array
    {
        return [
            new InvoiceDataSheet($this->filters),
            new InvoiceSummarySheet($this->filters),
            new InvoiceChartsSheet($this->filters),
        ];
    }
}

class InvoiceDataSheet implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle
{
    protected $filters;
    
    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }
    
    public function collection()
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
        
        return $query->orderBy('invoice_date', 'desc')->get();
    }
    
    public function headings(): array
    {
        return [
            'No. Faktur',
            'Klien',
            'Jenis Layanan',
            'Deskripsi',
            'Jumlah',
            'Pajak',
            'Total',
            'Tanggal Faktur',
            'Jatuh Tempo',
            'Status',
            'Tanggal Dibayar',
            'Metode Pembayaran',
            'Catatan Pembayaran',
            'Catatan',
        ];
    }
    
    public function map($invoice): array
    {
        return [
            $invoice->invoice_number,
            $invoice->client_name,
            $this->getServiceTypeLabel($invoice->service_type),
            $invoice->description,
            'Rp ' . number_format($invoice->amount, 0, ',', '.'),
            'Rp ' . number_format($invoice->tax_amount, 0, ',', '.'),
            'Rp ' . number_format($invoice->total_amount, 0, ',', '.'),
            $invoice->invoice_date ? Carbon::parse($invoice->invoice_date)->format('d/m/Y') : '',
            $invoice->due_date ? Carbon::parse($invoice->due_date)->format('d/m/Y') : '',
            $this->getStatusLabel($invoice->status),
            $invoice->paid_date ? Carbon::parse($invoice->paid_date)->format('d/m/Y') : '',
            $invoice->payment_method ? $this->getPaymentMethodLabel($invoice->payment_method) : '',
            $invoice->payment_notes ?? '',
            $invoice->notes ?? '',
        ];
    }
    
    public function styles(Worksheet $sheet)
    {
        return [
            // Header styling
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                    'size' => 12,
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
            ],
            // Data rows styling
            'A2:N1000' => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CCCCCC'],
                    ],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
            // Amount columns alignment
            'E:G' => [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_RIGHT,
                ],
            ],
        ];
    }
    
    public function columnWidths(): array
    {
        return [
            'A' => 15, // No. Faktur
            'B' => 25, // Klien
            'C' => 15, // Jenis Layanan
            'D' => 30, // Deskripsi
            'E' => 15, // Jumlah
            'F' => 12, // Pajak
            'G' => 15, // Total
            'H' => 12, // Tanggal Faktur
            'I' => 12, // Jatuh Tempo
            'J' => 12, // Status
            'K' => 12, // Tanggal Dibayar
            'L' => 15, // Metode Pembayaran
            'M' => 25, // Catatan Pembayaran
            'N' => 25, // Catatan
        ];
    }
    
    public function title(): string
    {
        return 'Data Faktur';
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
    
    private function getPaymentMethodLabel($method)
    {
        $methods = [
            'bank_transfer' => 'Transfer Bank',
            'cash' => 'Tunai',
            'credit_card' => 'Kartu Kredit',
            'debit_card' => 'Kartu Debit',
            'e_wallet' => 'E-Wallet',
            'check' => 'Cek',
            'other' => 'Lainnya',
        ];
        
        return $methods[$method] ?? $method;
    }
}