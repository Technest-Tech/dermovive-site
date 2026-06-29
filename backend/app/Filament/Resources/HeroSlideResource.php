<?php

namespace App\Filament\Resources;

use App\Enums\LinkType;
use App\Filament\Resources\HeroSlideResource\Pages;
use App\Models\HeroSlide;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Form;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class HeroSlideResource extends Resource
{
    use Translatable;

    protected static ?string $model = HeroSlide::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationGroup = 'Content';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Content')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('title')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),
                    Forms\Components\Textarea::make('subtitle')
                        ->rows(2)
                        ->columnSpanFull(),
                    Forms\Components\TextInput::make('cta_label')
                        ->label('Button label'),
                    SpatieMediaLibraryFileUpload::make('image')
                        ->collection('image')
                        ->image()
                        ->imageEditor()
                        ->columnSpanFull(),
                ]),

            Forms\Components\Section::make('Link & scheduling')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('link_type')
                        ->options(LinkType::class)
                        ->native(false)
                        ->live(),
                    Forms\Components\TextInput::make('link_target')
                        ->helperText(fn (Forms\Get $get) => match ($get('link_type')) {
                            LinkType::Product->value => 'Product slug',
                            LinkType::Category->value => 'Category slug',
                            LinkType::Url->value => 'Full URL (https://…)',
                            default => 'Select a link type first',
                        }),
                    Forms\Components\TextInput::make('sort_order')
                        ->numeric()
                        ->default(0),
                    Forms\Components\Toggle::make('is_active')
                        ->default(true)
                        ->inline(false),
                    Forms\Components\DateTimePicker::make('starts_at'),
                    Forms\Components\DateTimePicker::make('ends_at'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                Tables\Columns\SpatieMediaLibraryImageColumn::make('image')
                    ->collection('image')
                    ->conversion('preview')
                    ->label('')
                    ->size(80),
                Tables\Columns\TextColumn::make('title')->searchable()->weight('medium'),
                Tables\Columns\TextColumn::make('link_type')->badge()->placeholder('—'),
                Tables\Columns\TextColumn::make('sort_order')->label('Order')->badge(),
                Tables\Columns\IconColumn::make('is_active')->boolean()->label('Active'),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHeroSlides::route('/'),
            'create' => Pages\CreateHeroSlide::route('/create'),
            'edit' => Pages\EditHeroSlide::route('/{record}/edit'),
        ];
    }
}
