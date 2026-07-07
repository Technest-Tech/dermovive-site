<?php

namespace App\Filament\Widgets;

use App\Models\Category;
use App\Models\HeroSlide;
use App\Models\Page;
use App\Models\Product;
use App\Models\Tag;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class CatalogStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        $totalProducts = Product::count();
        $activeProducts = Product::where('is_active', true)->count();
        $featuredProducts = Product::where('is_featured', true)->count();

        $now = Carbon::now();
        $liveSlides = HeroSlide::query()
            ->where('is_active', true)
            ->where(fn ($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now))
            ->where(fn ($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>=', $now))
            ->count();

        return [
            Stat::make('Products', $totalProducts)
                ->description("{$activeProducts} active · {$featuredProducts} featured")
                ->descriptionIcon('heroicon-m-sparkles')
                ->color('primary')
                ->chart($this->productTrend()),

            Stat::make('Categories', Category::where('is_active', true)->count())
                ->description('Active catalog sections')
                ->descriptionIcon('heroicon-m-rectangle-stack')
                ->color('gray'),

            Stat::make('Live Hero Slides', $liveSlides)
                ->description(HeroSlide::count().' total configured')
                ->descriptionIcon('heroicon-m-photo')
                ->color($liveSlides > 0 ? 'success' : 'warning'),

            Stat::make('Published Pages', Page::where('is_published', true)->count())
                ->description(Tag::count().' filter tags')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('gray'),
        ];
    }

    /**
     * Products created per day over the last 7 days — a small sparkline for the
     * Products stat.
     *
     * @return array<int, int>
     */
    protected function productTrend(): array
    {
        $start = Carbon::now()->subDays(6)->startOfDay();

        $counts = Product::query()
            ->where('created_at', '>=', $start)
            ->get(['created_at'])
            ->groupBy(fn ($p) => $p->created_at->format('Y-m-d'))
            ->map->count();

        return collect(range(0, 6))
            ->map(fn ($i) => (int) ($counts[$start->copy()->addDays($i)->format('Y-m-d')] ?? 0))
            ->all();
    }
}
