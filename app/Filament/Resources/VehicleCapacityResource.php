<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VehicleCapacityResource\Pages;
use App\Filament\Resources\VehicleCapacityResource\RelationManagers;
use App\Models\VehicleCapacity;
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

class VehicleCapacityResource extends Resource
{
    protected static ?string $model = VehicleCapacity::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube-transparent';

    protected static ?string $navigationGroup = 'Content Management - CM -';

    protected static ?string $navigationLabel = 'Vehicle Capacity';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //

                Group::make()->schema([

                    Section::make()->schema([

                        Select::make('vehicle_id')
                            ->label('Vehicle')
                            ->searchable()
                            ->relationship('vehicle', 'code', function ($query) {
                                $query->doesntHave('vehicleCapacity');
                            })

                    ]),

                    Section::make('Vehicle Dimentions')->schema([

                        TextInput::make('length')
                            ->label('Vehicle Length')
                            ->required()
                            ->numeric()
                            ->reactive()
                            ->minValue(1)
                            ->maxValue(500)
                            ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                                self::calculateGVWR($set, $get);
                            })
                            ->helperText('in M - Meter'),

                        TextInput::make('width')
                            ->label('Vehicle Width')
                            ->required()
                            ->numeric()
                            ->reactive()
                            ->minValue(1)
                            ->maxValue(500)
                            ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                                self::calculateGVWR($set, $get);
                            })
                            ->helperText('in M - Meter'),

                        TextInput::make('height')
                            ->label('Vehicle Height')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->reactive()
                            ->maxValue(500)
                            ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                                self::calculateGVWR($set, $get);
                            })
                            ->helperText('in M - Meter'),

                        TextInput::make('gvwr')
                            ->label('GVWR')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->disabled()
                            ->dehydrated()
                            ->helperText('(Gross Vehicle Weight Rating) in M3 - Meter3')
                            ->columnSpanFull(),

                    ])->columns(3),
                ]),

                Group::make()->schema([

                    Section::make('Vehicle Carried Weight')->schema([
                        TextInput::make('weight')
                            ->label('Weight')
                            ->numeric()
                            ->minValue(1)
                            ->required()
                            ->helperText('in Ton = 100 KG, KG for Kilo Gram'),
                    ])->collapsible(),

                ]),
            ]);
    }

    public static function calculateGVWR(Forms\Set $set, Forms\Get $get)
    {

        $width = $get('width');
        $length = $get('length');
        $height = $get('height');

        if ($width && $length && $height) {
            $set('gvwr', $width * $length * $height);
        }
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //

                TextColumn::make('vehicle.director.fname')
                    ->label('Director')
                    ->sortable()
                    ->formatStateUsing(fn($record) => $record->vehicle->director->fname . ' ' . $record->vehicle->director->lname)
                    ->searchable(),

                TextColumn::make('vehicle.code')
                    ->label('Vehicle')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('length')
                    ->label('Length')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(fn($record) => $record->length . ' M'),

                TextColumn::make('width')
                    ->label('Width')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(fn($record) => $record->width . ' M'),

                TextColumn::make('height')
                    ->label('Height')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(fn($record) => $record->height . ' M'),

                TextColumn::make('gvwr')
                    ->label('GVWR (Gross Vehicle Weight Rating)')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->formatStateUsing(fn($record) => $record->gvwr . ' M3'),

                TextColumn::make('weight')
                    ->label('Weight')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(fn($record) => $record->weight . ' Ton/s'),

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
                    ->relationship('vehicle', 'code')
                    ->searchable(),

                SelectFilter::make('director_id')
                    ->label('Director')
                    ->relationship('vehicle.director', 'username')
                    ->searchable(),
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
            'index' => Pages\ListVehicleCapacities::route('/'),
            'create' => Pages\CreateVehicleCapacity::route('/create'),
            'edit' => Pages\EditVehicleCapacity::route('/{record}/edit'),
        ];
    }
}
