<?php

namespace App\Filament\AssetSurvey\Widgets;

use App\Models\Asset;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class AssetConditionChart extends ChartWidget
{
    protected static ?string $heading = 'Asset Condition Distribution';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        // Show condition distribution for all assets without tenant filtering
        $conditions = Asset::select('condition')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('condition')
            ->pluck('count', 'condition')
            ->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Assets by Condition',
                    'data' => array_values($conditions),
                    'backgroundColor' => [
                        '#10B981', // excellent - green
                        '#3B82F6', // good - blue
                        '#F59E0B', // fair - yellow
                        '#EF4444', // poor - red
                        '#DC2626', // critical - dark red
                    ],
                ],
            ],
            'labels' => array_map('ucfirst', array_keys($conditions)),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}