<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Filament\Resources\InvoiceResource;
use Filament\Resources\Pages\Page;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use App\Models\Invoice;
use Carbon\Carbon;
use Filament\Support\Colors\Color;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Infolist;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;

class YearlyReport extends Page implements HasForms, HasInfolists, HasActions
{
    use InteractsWithForms, InteractsWithInfolists, InteractsWithActions;

    protected static string $resource = InvoiceResource::class;

    protected static string $view = 'filament.resources.invoice-resource.pages.yearly-report';

    protected static ?string $title = 'Yearly Payment Report';

    protected static ?string $navigationLabel = 'Yearly Report';

    public ?array $data = [];
    public int $selectedYear;
    public array $reportData = [];

    public function mount(): void
    {
        $this->selectedYear = (int) date('Y');
        $this->form->fill(['year' => $this->selectedYear]);
        $this->loadReportData();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Report Settings')
                    ->schema([
                        Select::make('year')
                            ->label('Select Year')
                            ->options($this->getYearOptions())
                            ->default($this->selectedYear)
                            ->live()
                            ->afterStateUpdated(function ($state) {
                                $this->selectedYear = (int) $state;
                                $this->loadReportData();
                            }),
                    ])
                    ->columns(1),
            ])
            ->statePath('data');
    }

    protected function getYearOptions(): array
    {
        $currentYear = (int) date('Y');
        $startYear = $currentYear - 5; // Show last 5 years
        $endYear = $currentYear + 1; // Include next year
        
        $years = [];
        for ($year = $endYear; $year >= $startYear; $year--) {
            $years[$year] = $year;
        }
        
        return $years;
    }

    protected function loadReportData(): void
    {
        $this->reportData = Invoice::getPaymentStats($this->selectedYear);
        $this->reportData['invoices'] = Invoice::paid()
            ->inYear($this->selectedYear)
            ->with(['reference'])
            ->orderBy('paid_date', 'desc')
            ->get();
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->state($this->reportData)
            ->schema([
                Grid::make(4)
                    ->schema([
                        TextEntry::make('total_paid')
                            ->label('Total Payments')
                            ->money('IDR')
                            ->color('success')
                            ->size('lg')
                            ->weight('bold'),
                        
                        TextEntry::make('total_invoices')
                            ->label('Total Invoices')
                            ->numeric()
                            ->color('info')
                            ->size('lg')
                            ->weight('bold'),
                        
                        TextEntry::make('average_invoice')
                            ->label('Average Invoice')
                            ->money('IDR')
                            ->color('warning')
                            ->size('lg')
                            ->weight('bold'),
                        
                        TextEntry::make('year')
                            ->label('Report Year')
                            ->default($this->selectedYear)
                            ->color('gray')
                            ->size('lg')
                            ->weight('bold'),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export_pdf')
                ->label('Export PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('danger')
                ->action(function () {
                    // TODO: Implement PDF export
                    $this->js('alert("PDF export will be implemented soon")');
                }),
            
            Action::make('export_excel')
                ->label('Export Excel')
                ->icon('heroicon-o-table-cells')
                ->color('success')
                ->action(function () {
                    // TODO: Implement Excel export
                    $this->js('alert("Excel export will be implemented soon")');
                }),
        ];
    }

    public function getMonthlyChartData(): array
    {
        $monthlyTotals = $this->reportData['monthly_totals'] ?? [];
        $months = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
            5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug',
            9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec'
        ];
        
        $chartData = [
            'labels' => array_values($months),
            'datasets' => [[
                'label' => 'Monthly Payments (IDR)',
                'data' => array_values($monthlyTotals),
                'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                'borderColor' => 'rgb(59, 130, 246)',
                'borderWidth' => 2,
                'fill' => true,
            ]]
        ];
        
        return $chartData;
    }

    public function getServiceTypeChartData(): array
    {
        $serviceTypeTotals = $this->reportData['service_type_totals'] ?? [];
        
        $colors = [
            'rgba(239, 68, 68, 0.8)',   // red
            'rgba(34, 197, 94, 0.8)',   // green
            'rgba(59, 130, 246, 0.8)',  // blue
            'rgba(245, 158, 11, 0.8)',  // yellow
            'rgba(168, 85, 247, 0.8)',  // purple
            'rgba(236, 72, 153, 0.8)',  // pink
            'rgba(14, 165, 233, 0.8)',  // sky
            'rgba(34, 197, 94, 0.8)',   // emerald
        ];
        
        $chartData = [
            'labels' => array_keys($serviceTypeTotals),
            'datasets' => [[
                'data' => array_values($serviceTypeTotals),
                'backgroundColor' => array_slice($colors, 0, count($serviceTypeTotals)),
                'borderWidth' => 2,
            ]]
        ];
        
        return $chartData;
    }

    public function getTopClients(): array
    {
        return Invoice::paid()
            ->inYear($this->selectedYear)
            ->selectRaw('client_name, SUM(total_amount) as total_paid, COUNT(*) as invoice_count')
            ->groupBy('client_name')
            ->orderBy('total_paid', 'desc')
            ->limit(10)
            ->get()
            ->toArray();
    }
}