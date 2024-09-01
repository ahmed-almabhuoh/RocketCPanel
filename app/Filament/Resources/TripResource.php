<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TripResource\Pages;
use App\Filament\Resources\TripResource\RelationManagers;
use App\Models\Trip;
use App\Models\Vehicle;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TripResource extends Resource
{
    protected static ?string $model = Trip::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-trending-up';

    protected static ?string $navigationGroup = 'Trip Configurations - TC -';

    protected static ?string $navigationLabel = 'Trips Management';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //

                Group::make()->schema([

                    Section::make('Trip Start & End Points')->schema([

                        TextInput::make('from_lat')
                            ->label('From LAT')
                            ->required()
                            ->regex('/^[-+]?([1-8]?\d(\.\d+)?|90(\.0+)?)$/')
                            ->helperText('e.g. -34.232299768855'),

                        TextInput::make('from_lng')
                            ->label('From LNG')
                            ->required()
                            ->regex('/^[-+]?([1-8]?\d(\.\d+)?|90(\.0+)?)$/')
                            ->helperText('e.g. -34.232299768855'),

                        TextInput::make('to_lat')
                            ->label('To LAT')
                            ->required()
                            ->regex('/^[-+]?([1-8]?\d(\.\d+)?|90(\.0+)?)$/')
                            ->helperText('e.g. -34.232299768855'),

                        TextInput::make('to_lng')
                            ->label('To LNG')
                            ->required()
                            ->regex('/^[-+]?([1-8]?\d(\.\d+)?|90(\.0+)?)$/')
                            ->helperText('e.g. -34.232299768855'),

                        TextInput::make('from_city')
                            ->required()
                            ->minValue(2)
                            ->maxValue(50),

                        TextInput::make('to_city')
                            ->minValue(2)
                            ->maxValue(50)
                            ->required(),

                    ])->columns(2),

                    Section::make('Credits & Financial')->schema([

                        Select::make('director_id')
                            ->label('Director')
                            ->required()
                            ->exists('users', 'id')
                            ->searchable()
                            ->reactive()
                            ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get) {
                                self::calculateTripFinancial($set, $get);
                            })
                            ->relationship('director', 'username'),

                        Select::make('vehicle_id')
                            ->label('On vehicle')
                            ->required()
                            ->exists('vehicles', 'id')
                            ->searchable()
                            ->reactive()
                            ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get) {
                                self::calculateTripFinancial($set, $get);
                            })
                            ->relationship('vehicle', 'code'),

                        TextInput::make('trip_proposal_limitation')
                            ->label('Proposal limitation')
                            ->numeric()
                            ->default(20)
                            ->required()
                            ->minValue(1)
                            ->maxValue(100)
                            ->helperText('Trip will receive the number of proposals')
                            ->columnSpan(2),

                        TextInput::make('trip_credits')
                            ->label('Trip Credits')
                            ->required()
                            ->minValue(1)
                            ->disabled()
                            ->dehydrated()
                            ->helperText('To publish trip we wil take.'),

                        TextInput::make('trip_proposal_credits')
                            ->label('Trip Proposal Credits')
                            ->disabled()
                            ->dehydrated()
                            ->minValue(1)
                            ->helperText('Each proposal will deposit'),

                    ])->columns(2)->collapsible(),
                ]),

                Group::make()->schema([

                    Section::make('Trip Timeline')->schema([

                        DateTimePicker::make('start_at')
                            ->label('Trip start at')
                            ->required(),

                        DateTimePicker::make('end_at')
                            ->label('Trip end at')
                            ->required(),

                    ])->columns(1)->collapsible(),


                    Section::make('Exceeds')->schema([

                        Select::make('when_exceed')
                            ->label('Close when')
                            ->options(returnWithKeyValuesArray(Trip::WHEN_EXCEED))
                            ->required()
                            ->in([
                                'gvwr',
                                'weight'
                            ]),

                    ])->columns(1)->collapsible(),

                    Section::make('General')->schema([

                        Select::make('status')
                            ->label('Trip status')
                            ->options(returnWithKeyValuesArray(Trip::STATUS))
                            ->required(),

                        MarkdownEditor::make('description')
                            ->label('Trip description')
                            ->helperText('Text will visible to community')
                            ->required(),

                    ])->columns(1)->collapsible(),

                ]),


            ]);
    }

    public static function calculateTripFinancial(Forms\Set $set, Forms\Get $get)
    {
        $director_id = $get('director_id');
        $vehicle_id = $get('vehicle_id');

        if ($vehicle_id && $director_id) {
            $vehicle = Vehicle::where('id', $vehicle_id)->first();

            $set('trip_credits', floor(env('APP_STATIC_UNION_CREDITS') * getCreditsLevel($vehicle->size)));

            $trip_credits = $get('trip_credits');

            if ($trip_credits)
                $set('trip_proposal_credits', floor(($trip_credits / 35) * 100));
        }
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //

                TextColumn::make('from_lat')
                    ->label('From point')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(fn($record) => '(' . $record->from_lat . ', ' .  $record->from_lng . ')'),

                TextColumn::make('to_lat')
                    ->label('To point')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(fn($record) => '(' . $record->to_lat . ', ' .  $record->to_lng . ')'),

                TextColumn::make('from_city')
                    ->label('(From, To) city')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(fn($record) => '(' . $record->from_city . ', ' .  $record->to_city . ')'),

                TextColumn::make('start_at')
                    ->label('Start at')
                    ->dateTime()
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('end_at')
                    ->label('Start at')
                    ->dateTime()
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('description')
                    ->label('Description')
                    ->markdown()
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),


                IconColumn::make('status')
                    ->options([
                        'heroicon-o-check-circle' => 'open',
                        'heroicon-o-x-circle' => 'close',
                    ])
                    ->colors([
                        'success' => 'open',
                        'danger' => 'close',
                    ])
                    ->label('Trip status')
                    ->getStateUsing(fn($record) => $record->status),

                TextColumn::make('trip_credits')
                    ->label('Credits')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->formatStateUsing(fn($record) => $record->trip_credits . ' Os'),

                TextColumn::make('trip_proposal_limitation')
                    ->label('Proposal limitation')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->formatStateUsing(fn($record) => $record->trip_credits . ' P'),

                TextColumn::make('trip_proposal_credits')
                    ->label('Proposal credits')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->formatStateUsing(fn($record) => $record->trip_proposal_credits . ' Os'),

                TextColumn::make('when_exceed')
                    ->label('Close When?')
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->formatStateUsing(fn($record) => strtoupper($record->when_exceed)),

                TextColumn::make('vehicle.code')
                    ->label('Via vehicle')
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('driver.fname')
                    ->label('Via driver')
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->formatStateUsing(fn($record) => $record->driver->fname . ' ' . $record->driver->lname),

                TextColumn::make('created_at')
                    ->label('Submitted at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Updated at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),


            ])
            ->filters([
                //

                Filter::make('today')
                    ->toggle()
                    ->label('Submitted Today')
                    ->query(function ($query) {
                        $today = Carbon::today();

                        return $query->whereDate('created_at', $today);
                    }),

                SelectFilter::make('status')
                    ->label('Trip Status')
                    ->options(returnWithKeyValuesArray(Trip::STATUS)),

                SelectFilter::make('when_exceed')
                    ->label('Close When')
                    ->options(returnWithKeyValuesArray(Trip::WHEN_EXCEED))
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\DeleteAction::make(),

                    // Tables\Actions\Action::make('status')
                    //     ->label('Publish Trip')
                    //     ->icon('heroicon-o-exclamation-circle')
                    //     ->color('warning')
                    //     ->action(function ($record) {
                    //         $record->status = 'open';
                    //         $record->save();
                    //     }),

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
            'index' => Pages\ListTrips::route('/'),
            'create' => Pages\CreateTrip::route('/create'),
            'edit' => Pages\EditTrip::route('/{record}/edit'),
        ];
    }
}
