<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VehicleResource\Pages;
use App\Filament\Resources\VehicleResource\RelationManagers;
use App\Models\Vehicle;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VehicleResource extends Resource
{
    protected static ?string $model = Vehicle::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    protected static ?string $navigationGroup = 'Content Management - CM -';

    protected static ?string $navigationLabel = 'Vehicles';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        // We have to complete the form after we when make a centralized system to handle assets
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

                TextColumn::make('director_id')
                    ->label('Director')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(fn($record) => $record->director->fname . ' ' . $record->director->lname),

                TextColumn::make('brand')
                    ->label('Brand')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('code')
                    ->label('Code')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('size')
                    ->label('Size')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(fn($record) => strtoupper($record->size)),

                IconColumn::make('status')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->colors([
                        'success' => 'active',
                        'danger' => 'inactive',
                    ]),

                TextColumn::make('model')
                    ->label('Model')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                //

                SelectFilter::make('size')
                    ->label('Vehicle Size')
                    ->searchable()
                    ->options(returnWithKeyValuesArray(Vehicle::SIZE)),

                SelectFilter::make('status')
                    ->label('Vehicle Status')
                    ->searchable()
                    ->options(returnWithKeyValuesArray(Vehicle::STATUS)),

            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
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
            'index' => Pages\ListVehicles::route('/'),
            'create' => Pages\CreateVehicle::route('/create'),
            'edit' => Pages\EditVehicle::route('/{record}/edit'),
        ];
    }
}
