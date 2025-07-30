<?php

namespace App\Filament\AssetSurvey\Resources\AssetLoanResource\Pages;

use App\Filament\AssetSurvey\Resources\AssetLoanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListAssetLoans extends ListRecords
{
    protected static string $resource = AssetLoanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('New Loan Request'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Loans'),
            'survey' => Tab::make('Survey Loans')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('asset', function (Builder $q) {
                    $q->whereIn('tool_category', ['documentation', 'architecture', 'civil', 'mep']);
                }))
                ->badge(fn () => $this->getModel()::whereHas('asset', function (Builder $q) {
                    $q->whereIn('tool_category', ['documentation', 'architecture', 'civil', 'mep']);
                })->count()),
            'maintenance' => Tab::make('Maintenance Loans')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('asset', function (Builder $q) {
                    $q->whereIn('tool_category', ['safety', 'support']);
                }))
                ->badge(fn () => $this->getModel()::whereHas('asset', function (Builder $q) {
                    $q->whereIn('tool_category', ['safety', 'support']);
                })->count()),
            'pending' => Tab::make('Pending Approval')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'pending'))
                ->badge(fn () => $this->getModel()::where('status', 'pending')->count()),
            'out' => Tab::make('Currently Out')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'out'))
                ->badge(fn () => $this->getModel()::where('status', 'out')->count()),
            'overdue' => Tab::make('Overdue')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'overdue'))
                ->badge(fn () => $this->getModel()::where('status', 'overdue')->count())
                ->badgeColor('danger'),
            'returned' => Tab::make('Returned')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'in')),
        ];
    }
}