<?php

namespace App\Filament\Resources;

use App\Enums\ProductBadge;
use App\Filament\Resources\ProductResource\Pages;
use App\Models\Category;
use App\Models\Product;
use App\Models\Tag;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Form;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProductResource extends Resource
{
    use Translatable;

    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-sparkles';

    protected static ?string $navigationGroup = 'Catalog';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Tabs::make()
                ->columnSpanFull()
                ->persistTabInQueryString()
                ->tabs([
                    Forms\Components\Tabs\Tab::make('General')
                        ->icon('heroicon-o-information-circle')
                        ->schema([
                            Forms\Components\TextInput::make('name')
                                ->required()
                                ->maxLength(255)
                                ->columnSpanFull(),

                            Forms\Components\Grid::make(3)->schema([
                                Forms\Components\TextInput::make('slug')
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true)
                                    ->helperText('Blank = auto from name.'),
                                Forms\Components\TextInput::make('sku')
                                    ->label('SKU')
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true),
                                Forms\Components\CheckboxList::make('badges')
                                    ->options(collect(ProductBadge::cases())
                                        ->mapWithKeys(fn (ProductBadge $b) => [$b->value => $b->getLabel()])
                                        ->all()),
                            ]),

                            Forms\Components\Textarea::make('short_description')
                                ->rows(2)
                                ->maxLength(500)
                                ->columnSpanFull(),

                            Forms\Components\RichEditor::make('description')
                                ->columnSpanFull(),

                            Forms\Components\Grid::make(2)->schema([
                                Forms\Components\Toggle::make('is_active')->default(true),
                                Forms\Components\Toggle::make('is_featured')
                                    ->helperText('Show on the homepage.'),
                            ]),
                        ]),

                    Forms\Components\Tabs\Tab::make('Media')
                        ->icon('heroicon-o-photo')
                        ->schema([
                            SpatieMediaLibraryFileUpload::make('gallery')
                                ->collection('gallery')
                                ->multiple()
                                ->reorderable()
                                ->image()
                                ->imageEditor()
                                ->panelLayout('grid')
                                ->columnSpanFull(),
                        ]),

                    Forms\Components\Tabs\Tab::make('Categories & Tags')
                        ->icon('heroicon-o-rectangle-stack')
                        ->schema([
                            Forms\Components\Select::make('primary_category_id')
                                ->label('Primary category')
                                ->options(fn () => static::categoryOptions())
                                ->searchable()
                                ->native(false)
                                ->helperText('Used for breadcrumbs.'),

                            Forms\Components\Select::make('categories')
                                ->relationship('categories')
                                ->getOptionLabelFromRecordUsing(fn (Category $record) => $record->name)
                                ->multiple()
                                ->preload()
                                ->searchable(),

                            Forms\Components\Select::make('tags')
                                ->relationship('tags')
                                ->getOptionLabelFromRecordUsing(fn (Tag $record) => $record->type->getLabel().': '.$record->name)
                                ->multiple()
                                ->preload()
                                ->searchable()
                                ->columnSpanFull(),
                        ])->columns(2),

                    Forms\Components\Tabs\Tab::make('Details')
                        ->icon('heroicon-o-beaker')
                        ->schema([
                            Forms\Components\Repeater::make('benefits')
                                ->simple(Forms\Components\TextInput::make('benefit')->required())
                                ->addActionLabel('Add benefit')
                                ->reorderable(),

                            Forms\Components\Repeater::make('how_to_use')
                                ->label('How to use (steps)')
                                ->simple(Forms\Components\TextInput::make('step')->required())
                                ->addActionLabel('Add step')
                                ->reorderable(),

                            Forms\Components\Textarea::make('ingredients')
                                ->rows(4)
                                ->helperText('Full ingredient (INCI) list.')
                                ->columnSpanFull(),
                        ]),

                    Forms\Components\Tabs\Tab::make('Variants')
                        ->icon('heroicon-o-swatch')
                        ->schema([
                            Forms\Components\Repeater::make('variants')
                                ->relationship()
                                ->orderColumn('sort_order')
                                ->reorderable()
                                ->collapsible()
                                ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
                                ->addActionLabel('Add variant')
                                ->schema([
                                    Forms\Components\TextInput::make('name')
                                        ->required()
                                        ->placeholder('e.g. 30 ml / Light')
                                        ->columnSpan(2),
                                    Forms\Components\TextInput::make('sku')->label('SKU'),
                                    Forms\Components\TextInput::make('price')->numeric()->prefix('$'),
                                    Forms\Components\Toggle::make('is_default')->inline(false),
                                    Forms\Components\Toggle::make('is_active')->default(true)->inline(false),
                                ])
                                ->columns(3),
                        ]),

                    Forms\Components\Tabs\Tab::make('Pricing & SEO')
                        ->icon('heroicon-o-currency-dollar')
                        ->schema([
                            Forms\Components\Grid::make(3)->schema([
                                Forms\Components\TextInput::make('price')->numeric()->prefix('$'),
                                Forms\Components\TextInput::make('compare_at_price')
                                    ->label('Compare-at price')->numeric()->prefix('$'),
                                Forms\Components\TextInput::make('currency')
                                    ->default('USD')->maxLength(3),
                            ]),
                            Forms\Components\TextInput::make('meta_title')->maxLength(255)->columnSpanFull(),
                            Forms\Components\Textarea::make('meta_description')->rows(2)->columnSpanFull(),
                        ]),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\SpatieMediaLibraryImageColumn::make('gallery')
                    ->collection('gallery')
                    ->conversion('thumb')
                    ->label('')
                    ->square(),

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->weight('medium')
                    ->description(fn (Product $r) => $r->sku),

                Tables\Columns\TextColumn::make('primaryCategory.name')
                    ->label('Category')
                    ->badge()
                    ->color('gray')
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('price')
                    ->money(fn (Product $r) => $r->currency ?: 'USD')
                    ->placeholder('—')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_featured')->boolean()->label('Featured'),
                Tables\Columns\IconColumn::make('is_active')->boolean()->label('Active'),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label('Active'),
                Tables\Filters\TernaryFilter::make('is_featured')->label('Featured'),
                Tables\Filters\SelectFilter::make('primary_category_id')
                    ->label('Category')
                    ->options(fn () => static::categoryOptions()),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    /** Indented category options for selects/filters. */
    protected static function categoryOptions(): array
    {
        return Category::query()->withDepth()->defaultOrder()->get()
            ->mapWithKeys(fn (Category $c) => [
                $c->id => str_repeat('— ', (int) $c->depth).$c->name,
            ])
            ->all();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
