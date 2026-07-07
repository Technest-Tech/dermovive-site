<?php

namespace App\Filament\Widgets;

use App\Models\Category;
use Filament\Widgets\ChartWidget;

class ProductsByCategoryChart extends ChartWidget
{
    protected static ?int $sort = 2;

    protected static ?string $heading = 'Products by category';

    protected static ?string $maxHeight = '260px';

    protected int|string|array $columnSpan = 1;

    protected function getData(): array
    {
        $categories = Category::query()
            ->withCount('products')
            ->orderByDesc('products_count')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Products',
                    'data' => $categories->pluck('products_count')->all(),
                    'backgroundColor' => [
                        '#E87A8E', '#1C4A45', '#F0A6B4', '#3E7A72',
                        '#D4506B', '#6BA89F', '#F5C6D0', '#2A5C55',
                    ],
                    'borderWidth' => 0,
                ],
            ],
            'labels' => $categories
                ->map(fn (Category $c) => $c->getTranslation('name', app()->getLocale()) ?: $c->slug)
                ->all(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                ],
            ],
        ];
    }
}
