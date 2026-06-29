<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Form;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CategoryResource extends Resource
{
    use Translatable;

    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Catalog';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true),

                    Forms\Components\Select::make('parent_id')
                        ->label('Parent category')
                        ->placeholder('— Root level —')
                        ->options(fn (?Category $record) => static::parentOptions($record))
                        ->searchable()
                        ->native(false),

                    Forms\Components\TextInput::make('slug')
                        ->maxLength(255)
                        ->unique(ignoreRecord: true)
                        ->helperText('Leave blank to auto-generate from the name.'),

                    Forms\Components\Toggle::make('is_active')
                        ->default(true)
                        ->inline(false),

                    Forms\Components\Textarea::make('description')
                        ->rows(3)
                        ->columnSpanFull(),

                    SpatieMediaLibraryFileUpload::make('image')
                        ->collection('image')
                        ->image()
                        ->imageEditor()
                        ->columnSpanFull(),
                ]),

            Forms\Components\Section::make('SEO')
                ->collapsed()
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('meta_title')->maxLength(255),
                    Forms\Components\TextInput::make('meta_description')->maxLength(255),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->withDepth()->defaultOrder())
            ->columns([
                Tables\Columns\SpatieMediaLibraryImageColumn::make('image')
                    ->collection('image')
                    ->conversion('thumb')
                    ->circular()
                    ->label(''),

                Tables\Columns\TextColumn::make('name')
                    ->formatStateUsing(fn ($state, Category $record) => str_repeat('— ', (int) $record->depth).$state)
                    ->searchable()
                    ->weight('medium'),

                Tables\Columns\TextColumn::make('slug')
                    ->color('gray')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('products_count')
                    ->counts('products')
                    ->label('Products')
                    ->badge(),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active'),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label('Active'),
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

    /**
     * Indented parent options, excluding the record itself and its
     * descendants to prevent creating a cycle in the tree.
     */
    protected static function parentOptions(?Category $record): array
    {
        $query = Category::query()->withDepth()->defaultOrder();

        if ($record?->exists) {
            $excluded = $record->descendants()->pluck('id')->push($record->id);
            $query->whereNotIn('id', $excluded);
        }

        return $query->get()
            ->mapWithKeys(fn (Category $c) => [
                $c->id => str_repeat('— ', (int) $c->depth).$c->name,
            ])
            ->all();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
