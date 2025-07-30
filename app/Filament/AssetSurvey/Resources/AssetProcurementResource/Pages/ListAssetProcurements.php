<?php

namespace App\Filament\AssetSurvey\Resources\AssetProcurementResource\Pages;

use App\Filament\AssetSurvey\Resources\AssetProcurementResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListAssetProcurements extends ListRecords
{
    protected static string $resource = AssetProcurementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('New Procurement Request'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Requests'),
            'pending' => Tab::make('Pending Approval')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'pending'))
                ->badge(fn () => $this->getModel()::where('status', 'pending')->count()),
            'approved' => Tab::make('Approved')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'approved'))
                ->badge(fn () => $this->getModel()::where('status', 'approved')->count()),
            'ordered' => Tab::make('Ordered')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'ordered'))
                ->badge(fn () => $this->getModel()::where('status', 'ordered')->count()),
            'delivered' => Tab::make('Delivered')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'delivered')),
            'urgent' => Tab::make('Urgent')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('urgency_level', 'urgent'))
                ->badge(fn () => $this->getModel()::where('urgency_level', 'urgent')->count())
                ->badgeColor('danger'),
        ];
    }
}