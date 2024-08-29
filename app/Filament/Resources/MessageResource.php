<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MessageResource\Pages;
use App\Filament\Resources\MessageResource\RelationManagers;
use App\Models\Message;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MessageResource extends Resource
{
    protected static ?string $model = Message::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationGroup = 'Community';

    protected static ?string $navigationLabel = 'Messages';

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

                TextColumn::make('conversation.id')
                    ->label('Conversation No.')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('body')
                    ->label('Message')
                    ->sortable()
                    ->searchable(),

                IconColumn::make('is_read')
                    ->label('Is read?')
                    ->boolean(),

                IconColumn::make('is_receiver_deleted')
                    ->label('Is receiver deleted?')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->boolean(),

                IconColumn::make('is_sender_deleted')
                    ->label('Is sender deleted?')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->boolean(),

                TextColumn::make('read_at')
                    ->label('Read at')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('receiver_deleted_at')
                    ->label('Receiver deleted at')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('sender_deleted_at')
                    ->label('Sender deleted at')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Sent at')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //


                Tables\Filters\Filter::make('today')
                    ->label('Offenders Today')
                    ->toggle()
                    ->query(function (Builder $query) {
                        $patterns = config('offenders');
                        $today = Carbon::today();

                        $query->whereDate('created_at', $today)
                            ->where(function ($query) use ($patterns) {
                                foreach ($patterns as $name => $pattern) {
                                    $query->orWhereRaw("body REGEXP ?", [$pattern]);
                                }
                            });
                    }),

                Tables\Filters\Filter::make('week')
                    ->label('Offenders This Week')
                    ->toggle()
                    ->query(function (Builder $query) {
                        $patterns = config('offenders');
                        $startOfWeek = Carbon::now()->startOfWeek();
                        $endOfWeek = Carbon::now()->endOfWeek();

                        $query->whereBetween('created_at', [$startOfWeek, $endOfWeek])
                            ->where(function ($query) use ($patterns) {
                                foreach ($patterns as $name => $pattern) {
                                    $query->orWhereRaw("body REGEXP ?", [$pattern]);
                                }
                            });
                    }),

                Tables\Filters\Filter::make('month')
                    ->label('Offenders This Month')
                    ->toggle()
                    ->query(function (Builder $query) {
                        $patterns = config('offenders');
                        $startOfMonth = Carbon::now()->startOfMonth();
                        $endOfMonth = Carbon::now()->endOfMonth();

                        $query->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                            ->where(function ($query) use ($patterns) {
                                foreach ($patterns as $name => $pattern) {
                                    $query->orWhereRaw("body REGEXP ?", [$pattern]);
                                }
                            });
                    }),

                Tables\Filters\Filter::make('all')
                    ->label('All Offenders')
                    ->toggle()
                    ->query(function (Builder $query) {
                        $patterns = config('offenders');

                        $query->where(function ($query) use ($patterns) {
                            foreach ($patterns as $name => $pattern) {
                                $query->orWhereRaw("body REGEXP ?", [$pattern]);
                            }
                        });
                    }),

            ])
            ->actions([
                // Tables\Actions\EditAction::make(),

                Tables\Actions\ActionGroup::make([

                    Tables\Actions\Action::make('read_at')
                        ->label('Warn Sender'),

                    Tables\Actions\Action::make('read_at')
                        ->label('Warn Receiver'),

                    Tables\Actions\Action::make('read_at')
                        ->label('Warn Both'),

                    Tables\Actions\Action::make('read_at')
                        ->label('Freeze Sender'),

                    Tables\Actions\Action::make('read_at')
                        ->label('Freeze Receiver'),

                    Tables\Actions\Action::make('read_at')
                        ->label('Freeze Both'),
                ]),


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
            'index' => Pages\ListMessages::route('/'),
            'create' => Pages\CreateMessage::route('/create'),
            'edit' => Pages\EditMessage::route('/{record}/edit'),
        ];
    }
}
