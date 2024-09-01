<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TripConfigurationResource\Pages;
use App\Filament\Resources\TripConfigurationResource\RelationManagers;
use App\Models\TripConfiguration;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TripConfigurationResource extends Resource
{
    protected static ?string $model = TripConfiguration::class;

    protected static ?string $navigationIcon = 'heroicon-o-adjustments-horizontal';

    protected static ?string $navigationGroup = 'Trip Configurations - TC -';

    protected static ?string $navigationLabel = 'Trip Configuration';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //

                Group::make()->schema([
                    Section::make()->schema([

                        Toggle::make('enable_tracking')
                            ->label('Enable trip tracking'),


                        Toggle::make('show_vehicle')
                            ->label('Show trip vehicle')
                            ->helperText('Vehicle will be visible for community after posting trip'),

                        Toggle::make('show_driver')
                            ->label('Show trip driver')
                            ->helperText('Driver will be visible for community after posting trip'),

                        Toggle::make('schedule_trip_posting')
                            ->label('Schedule trip')
                            ->reactive()
                            ->helperText('Trip will post on a specific time.'),


                        DateTimePicker::make('posting_schedule')
                            ->label('Posting time')
                            ->visible(fn(Forms\Get $get) => $get('schedule_trip_posting'))

                    ])
                        ->columnSpanFull(),
                ])->columnSpanFull(),
            ]);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //

                TextColumn::make('trip.from_city')
                    ->label('Trip')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(fn($record) => $record->trip->from_city . ' - ' . $record->trip->to_city),

                TextColumn::make('trip.from_lat')
                    ->label('Points')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(fn($record) => '(' . $record->trip->from_lat . ', ' . $record->trip->from_lng . ') (' . $record->trip->to_lat . ', ' . $record->trip->to_lng . ')'),

                IconColumn::make('enable_tracking')
                    ->label('Tracking')
                    ->boolean(),

                IconColumn::make('show_vehicle')
                    ->label('Show vehicle?')
                    ->boolean(),

                IconColumn::make('show_driver')
                    ->label('Show driver?')
                    ->boolean(),

                IconColumn::make('schedule_trip_posting')
                    ->label('Schedule')
                    ->boolean(),

                TextColumn::make('posting_schedule')
                    ->label('Trip schedule')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Created at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Updated at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),



            ])
            ->filters([
                //

                Filter::make('enable_tracking')
                    ->label('Tracking Enabled')
                    ->toggle(),

                Filter::make('show_vehicle')
                    ->label('Vehicle Visible')
                    ->toggle(),

                Filter::make('show_driver')
                    ->label('Driver Visible')
                    ->toggle(),

                Filter::make('schedule_trip_posting')
                    ->label('Scheduled')
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListTripConfigurations::route('/'),
            'create' => Pages\CreateTripConfiguration::route('/create'),
            'edit' => Pages\EditTripConfiguration::route('/{record}/edit'),
        ];
    }
}
