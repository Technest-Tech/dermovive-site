<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\ProductResource;
use App\Models\Product;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class LatestProducts extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 1;

    protected static ?string $heading = 'Recently added products';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                fn (): Builder => Product::query()->latest()->limit(5)
            )
            ->paginated(false)
            ->columns([
                Tables\Columns\SpatieMediaLibraryImageColumn::make('gallery')
                    ->collection('gallery')
                    ->conversion('thumb')
                    ->label('')
                    ->square(),

                Tables\Columns\TextColumn::make('name')
                    ->weight('medium')
                    ->description(fn (Product $r) => $r->sku),

                Tables\Columns\TextColumn::make('primaryCategory.name')
                    ->label('Category')
                    ->badge()
                    ->color('gray')
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('price')
                    ->money(fn (Product $r) => $r->currency ?: 'USD')
                    ->placeholder('—'),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active'),
            ])
            ->actions([
                Tables\Actions\Action::make('edit')
                    ->url(fn (Product $record): string => ProductResource::getUrl('edit', ['record' => $record]))
                    ->icon('heroicon-m-pencil-square')
                    ->label('Edit'),
            ]);
    }
}
