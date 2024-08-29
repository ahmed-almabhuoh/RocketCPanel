<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ConversationResource\Pages;
use App\Filament\Resources\ConversationResource\RelationManagers;
use App\Models\Conversation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ConversationResource extends Resource
{
    protected static ?string $model = Conversation::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Community';

    protected static ?string $navigationLabel = 'Conversations';

    protected static ?int $navigationSort = 2;


    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function canView(Model $record): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //

                TextColumn::make('id')
                ->label('Conversation')
                ->sortable()
                ->searchable(),

                TextColumn::make('sender.fname')
                    ->label('Sender')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(fn($record) => $record->sender->fname . ' ' . $record->sender->lname),

                TextColumn::make('receiver.fname')
                    ->label('Receiver')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(fn($record) => $record->receiver->fname . ' ' . $record->receiver->lname),

                IconColumn::make('is_deleted')
                    ->label('Is deleted?')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('deleted_at')
                    ->label('Deleted at')
                    ->dateTime()
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->formatStateUsing(fn($record) => $record->deleted_at),

                TextColumn::make('created_at')
                    ->label('Established at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Last chatting at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //

                Filter::make('deleted_at')
                    ->toggle()
                    ->label('Deleted')
                    ->query(fn($query) => $query->whereNotNull('deleted_at')),

            ])
            ->actions([
                // Tables\Actions\ActionGroup::make([
                //     Tables\Actions\ViewAction::make(),
                //     Tables\Actions\EditAction::make(),
                //     Tables\Actions\DeleteAction::make(),
                // ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListConversations::route('/'),
            'create' => Pages\CreateConversation::route('/create'),
            'edit' => Pages\EditConversation::route('/{record}/edit'),
        ];
    }
}
