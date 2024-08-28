<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DriverPositionResource\Pages;
use App\Filament\Resources\DriverPositionResource\RelationManagers;
use App\Models\DriverPosition;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DriverPositionResource extends Resource
{
    protected static ?string $model = DriverPosition::class;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';

    protected static ?string $navigationGroup = 'Content Management - CM -';

    protected static ?string $navigationLabel = 'Last Drivers Position';

    protected static ?int $navigationSort = 0;

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

    public static function canDelete(Model $record): bool
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

                TextColumn::make('lat')
                    ->label('LAT')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('lng')
                    ->label('LNG')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('trip.from_city')
                    ->label('From city')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('trip.to_city')
                    ->label('To city')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('driver.fname')
                    ->label('Driver')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(fn($record) => $record->driver->fname . ' ' . $record->driver->lname),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListDriverPositions::route('/'),
            'create' => Pages\CreateDriverPosition::route('/create'),
            'edit' => Pages\EditDriverPosition::route('/{record}/edit'),
        ];
    }
}
