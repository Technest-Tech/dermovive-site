<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactMessageResource\Pages;
use App\Models\ContactMessage;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class ContactMessageResource extends Resource
{
    protected static ?string $model = ContactMessage::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationGroup = 'Content';

    protected static ?int $navigationSort = 5;

    protected static ?string $navigationLabel = 'Contact Messages';

    protected static ?string $recordTitleAttribute = 'name';

    /** Messages are created by visitors via the API, never in the panel. */
    public static function canCreate(): bool
    {
        return false;
    }

    public static function getNavigationBadge(): ?string
    {
        $unread = static::getModel()::query()->where('is_read', false)->count();

        return $unread > 0 ? (string) $unread : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make()
                ->columns(2)
                ->schema([
                    Infolists\Components\TextEntry::make('name'),
                    Infolists\Components\TextEntry::make('email')
                        ->icon('heroicon-m-envelope')
                        ->copyable(),
                    Infolists\Components\TextEntry::make('phone')
                        ->icon('heroicon-m-phone')
                        ->placeholder('—'),
                    Infolists\Components\TextEntry::make('subject')
                        ->placeholder('—'),
                    Infolists\Components\TextEntry::make('created_at')
                        ->label('Received')
                        ->dateTime(),
                    Infolists\Components\TextEntry::make('locale')
                        ->label('Language')
                        ->badge()
                        ->placeholder('—'),
                ]),
            Infolists\Components\Section::make('Message')
                ->schema([
                    Infolists\Components\TextEntry::make('message')
                        ->hiddenLabel()
                        ->prose()
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\IconColumn::make('is_read')
                    ->label('Read')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->weight(fn (ContactMessage $record) => $record->is_read ? null : 'bold'),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->copyable()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('subject')
                    ->limit(30)
                    ->placeholder('—')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('phone')
                    ->toggleable()
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Received')
                    ->dateTime()
                    ->since()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_read')
                    ->label('Read status')
                    ->trueLabel('Read')
                    ->falseLabel('Unread'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('toggleRead')
                    ->label(fn (ContactMessage $record) => $record->is_read ? 'Mark unread' : 'Mark read')
                    ->icon(fn (ContactMessage $record) => $record->is_read ? 'heroicon-o-envelope' : 'heroicon-o-envelope-open')
                    ->color('gray')
                    ->action(fn (ContactMessage $record) => $record->update(['is_read' => ! $record->is_read])),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('markRead')
                        ->label('Mark as read')
                        ->icon('heroicon-o-envelope-open')
                        ->action(fn (Collection $records) => $records->each->update(['is_read' => true]))
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContactMessages::route('/'),
            'view' => Pages\ViewContactMessage::route('/{record}'),
        ];
    }
}
