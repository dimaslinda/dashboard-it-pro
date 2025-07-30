<?php

namespace App\Filament\AssetSurvey\Widgets;

use App\Models\Asset;
use App\Models\AssetSurvey;
use App\Models\Company;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class AssetOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        // Show statistics for all assets without tenant filtering
        $totalAssets = Asset::count();
        $activeAssets = Asset::where('status', 'active')->count();
        $assetsNeedingMaintenance = Asset::where(function($query) {
                $query->where('condition', 'poor')
                      ->orWhere('condition', 'critical');
            })
            ->count();
        
        $totalSurveys = AssetSurvey::count();
        $completedSurveys = AssetSurvey::where('status', 'completed')->count();
        $pendingSurveys = AssetSurvey::where('status', 'pending')->count();
        
        // Additional stats for company breakdown
        $rekanuseCompany = Company::where('code', 'RKN')->first();
        $kaizenCompany = Company::where('code', 'KZN')->first();
        
        $rekanuseAssets = $rekanuseCompany ? Asset::where('company_id', $rekanuseCompany->id)->count() : 0;
        $kaizenAssets = $kaizenCompany ? Asset::where('company_id', $kaizenCompany->id)->count() : 0;

        return [
            Stat::make('Total Assets', $totalAssets)
                ->description('All registered assets')
                ->descriptionIcon('heroicon-m-archive-box')
                ->color('primary'),

            Stat::make('Active Assets', $activeAssets)
                ->description('Currently in use')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Need Maintenance', $assetsNeedingMaintenance)
                ->description('Poor or critical condition')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('warning'),

            Stat::make('Total Surveys', $totalSurveys)
                ->description('All survey records')
                ->descriptionIcon('heroicon-m-clipboard-document-check')
                ->color('info'),

            Stat::make('Completed Surveys', $completedSurveys)
                ->description('Finished assessments')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),

            Stat::make('Pending Surveys', $pendingSurveys)
                ->description('Awaiting completion')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
                
            Stat::make('REKANUSA Assets', $rekanuseAssets)
                ->description('Assets owned by Rekanusa')
                ->descriptionIcon('heroicon-m-building-office')
                ->color('info'),
                
            Stat::make('KAIZEN Assets', $kaizenAssets)
                ->description('Assets owned by Kaizen')
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('info'),
        ];
    }
}