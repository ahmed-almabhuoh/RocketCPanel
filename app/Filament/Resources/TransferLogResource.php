<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransferLogResource\Pages;
use App\Filament\Resources\TransferLogResource\RelationManagers;
use App\Models\TransferLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TransferLogResource extends Resource
{
    protected static ?string $model = TransferLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrows-right-left';

    protected static ?string $navigationGroup = 'Financial Management - FM -';

    protected static ?string $navigationLabel = 'Balance Transactions';

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

                TextColumn::make('orbits')
                    ->label('Transfer orbits')
                    ->formatStateUsing(fn($record) => $record->orbits . ' Os')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('user.fname')
                    ->label('Via user')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(fn($record) => $record->user->fname . ' ' . $record->user->lname),

                TextColumn::make('type')
                    ->label('Transaction type')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(fn($record) => ucfirst($record->type)),

                TextColumn::make('transferCode.code')
                    ->sortable()
                    ->searchable()
                    ->label('Via code'),

                TextColumn::make('created_at')
                    ->label('Established at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Last use at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //

                SelectFilter::make('type')
                    ->label('Balance Transaction Type')
                    ->options(returnWithKeyValuesArray(TransferLog::TYPE)),
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
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
            'index' => Pages\ListTransferLogs::route('/'),
            'create' => Pages\CreateTransferLog::route('/create'),
            'edit' => Pages\EditTransferLog::route('/{record}/edit'),
        ];
    }
}
