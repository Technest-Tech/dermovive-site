<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\CatalogStatsOverview;
use App\Filament\Widgets\LatestProducts;
use App\Filament\Widgets\ProductsByCategoryChart;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    public function getTitle(): string
    {
        return 'Dermovive Control Center';
    }

    public function getSubheading(): ?string
    {
        return 'Manage your catalog, storefront content and site settings from one place.';
    }

    public function getColumns(): int|string|array
    {
        return 2;
    }

    public function getWidgets(): array
    {
        return [
            CatalogStatsOverview::class,
            ProductsByCategoryChart::class,
            LatestProducts::class,
        ];
    }
}
