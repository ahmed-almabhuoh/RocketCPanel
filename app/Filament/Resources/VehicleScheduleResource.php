<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VehicleScheduleResource\Pages;
use App\Filament\Resources\VehicleScheduleResource\RelationManagers;
use App\Models\VehicleSchedule;
use DateTime;
use Filament\Forms;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VehicleScheduleResource extends Resource
{
    protected static ?string $model = VehicleSchedule::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationGroup = 'Content Management - CM -';

    protected static ?string $navigationLabel = 'Vehicle Schedules';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //

                Group::make()->schema([

                    Section::make('For Resource')->schema([

                        Select::make('director_id')
                            ->label('Director')
                            ->searchable()
                            ->reactive()
                            ->relationship('director', 'username', function ($query) {
                                $query->where('role', 'Director');
                            }),

                        Select::make('vehicle_id')
                            ->label('Vehicle')
                            ->searchable()
                            ->relationship('vehicle', 'code', function ($query, Forms\Get $get) {
                                $query->where([
                                    ['director_id', '=', $get('director_id')],
                                    ['status', '=', 'active'],
                                ]);
                            }),

                        Select::make('driver_id')
                            ->label('Driver')
                            ->reactive()
                            ->searchable()
                            ->relationship('driver', 'username', function ($query, Forms\Get $get) {
                                $query->where([
                                    ['user_id', '=', $get('director_id')],
                                    ['role', '=', 'Driver'],
                                ]);
                            }),

                    ]),

                ]),

                Group::make()->schema([

                    Section::make('Timeline')->schema([

                        TextInput::make('from')
                            ->label('From')
                            ->helperText('Should be between 0 - 24')
                            ->required()
                            ->minValue(0)
                            ->maxValue(24)
                            ->numeric(),


                        TextInput::make('to')
                            ->label('To')
                            ->helperText('Should be between 0 - 24')
                            ->required()
                            ->minValue(0)
                            ->maxValue(24)
                            ->numeric(),

                    ]),
                ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //

                TextColumn::make('director.fname')
                    ->label('Director')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn($record) => $record->director->fname . ' '  . $record->director->lname),

                TextColumn::make('vehicle.code')
                    ->label('Vehicle')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn($record) => $record->vehicle->code),

                TextColumn::make('from')
                    ->label('From - To Schedule')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(function ($record) {
                        $dateTimeFrom = DateTime::createFromFormat('H', $record->from);
                        $dateTimeTo = DateTime::createFromFormat('H', $record->to);

                        return $dateTimeFrom->format('g:i A') . ' - ' . $dateTimeTo->format('g:i A');
                    }),

                TextColumn::make('driver.fname')
                    ->label('Driver')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn($record) => $record->driver->fname . ' ' . $record->driver->lname),

                TextColumn::make('created_at')
                    ->label('Added at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Updated at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),


            ])
            ->filters([
                //

                SelectFilter::make('vehicle_id')
                    ->label('Vehicle')
                    ->searchable()
                    ->relationship('vehicle', 'code'),

                SelectFilter::make('driver_id')
                    ->label('Driver')
                    ->searchable()
                    ->relationship('driver', 'username'),

                SelectFilter::make('director_id')
                    ->label('Director')
                    ->searchable()
                    ->relationship('director', 'username'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListVehicleSchedules::route('/'),
            'create' => Pages\CreateVehicleSchedule::route('/create'),
            'edit' => Pages\EditVehicleSchedule::route('/{record}/edit'),
        ];
    }
}
