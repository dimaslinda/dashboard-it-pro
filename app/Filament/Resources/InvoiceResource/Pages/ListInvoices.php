<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Filament\Resources\InvoiceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Invoice;

class ListInvoices extends ListRecords
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('yearly_report')
                ->label('Yearly Report')
                ->icon('heroicon-o-chart-bar')
                ->color('info')
                ->url(fn () => static::$resource::getUrl('yearly-report')),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Invoices'),
            
            'draft' => Tab::make('Draft')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'draft'))
                ->badge(Invoice::where('status', 'draft')->count()),
            
            'sent' => Tab::make('Sent')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'sent'))
                ->badge(Invoice::where('status', 'sent')->count()),
            
            'paid' => Tab::make('Paid')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'paid'))
                ->badge(Invoice::where('status', 'paid')->count()),
            
            'overdue' => Tab::make('Overdue')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', '!=', 'paid')->where('due_date', '<', now()))
                ->badge(Invoice::where('status', '!=', 'paid')->where('due_date', '<', now())->count())
                ->badgeColor('danger'),
            
            'this_year' => Tab::make('This Year')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereYear('invoice_date', date('Y')))
                ->badge(Invoice::whereYear('invoice_date', date('Y'))->count()),
        ];
    }
}