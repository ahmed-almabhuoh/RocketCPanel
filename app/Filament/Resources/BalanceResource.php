<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BalanceResource\Pages;
use App\Filament\Resources\BalanceResource\RelationManagers;
use App\Models\Balance;
use Filament\Actions\Modal\Actions\Action;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BalanceResource extends Resource
{
    protected static ?string $model = Balance::class;

    protected static ?string $navigationIcon = 'heroicon-o-scale';

    protected static ?string $navigationGroup = 'Financial Management - FM -';

    protected static ?string $navigationLabel = 'Balances';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canView(Model $record): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //

                TextColumn::make('orbits')
                    ->label('Orbits')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(function ($record) {
                        return $record->orbits . ' Os';
                    }),

                TextColumn::make('user.full_name')
                    ->label('User')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(function ($record) {
                        return $record->user->fname . ' ' . $record->user->lname;
                    }),

                IconColumn::make('is_freezed')
                    ->label('Freezed')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label('Established at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),

                TextColumn::make('updated_at')
                    ->label('Last used at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->searchable(),

            ])
            ->filters([
                //

                Filter::make('is_freezed')
                    ->toggle()
                    ->label('Freezed balances')
                    ->default(false)
                    ->query(fn($query) => $query->where('is_freezed')),

            ])
            ->actions([

                // Tables\Actions\ViewAction::make(),

                Tables\Actions\ActionGroup::make([


                    Tables\Actions\Action::make('is_freezed')
                        ->label('Freeze balance')
                        ->color('danger')
                        ->icon('heroicon-o-lock-closed')
                        ->requiresConfirmation()
                        ->action(function (Balance $record) {
                            $record->is_freezed = true;
                            $record->save();

                            Notification::make()
                                ->title('Freezed')
                                ->success()
                                ->body("Balance has been freezed.")
                                ->send();
                        })
                        ->visible(fn(Balance $record) => ! $record->is_freezed),

                    Tables\Actions\Action::make('un-freeze')
                        ->label('Un-freeze balance')
                        ->color('success')
                        ->icon('heroicon-o-lock-open')
                        ->requiresConfirmation()
                        ->action(function (Balance $record) {
                            $record->is_freezed = false;
                            $record->save();

                            Notification::make()
                                ->title('Un-freezed')
                                ->success()
                                ->body("Balance has been un-freezed.")
                                ->send();
                        })
                        ->visible(fn(Balance $record) => $record->is_freezed),

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
            'index' => Pages\ListBalances::route('/'),
            'create' => Pages\CreateBalance::route('/create'),
            'edit' => Pages\EditBalance::route('/{record}/edit'),
        ];
    }
}
