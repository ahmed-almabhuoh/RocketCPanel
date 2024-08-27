<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CargoResource\Pages;
use App\Models\Cargo;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CargoResource extends Resource
{
    protected static ?string $model = Cargo::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox-stack';

    protected static ?string $navigationGroup = 'Content Management - CM -';

    protected static ?string $navigationLabel = 'Cargos';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()->schema([
                    Section::make()->schema([
                        TextInput::make('name')
                            ->label('Cargo name')
                            ->required()
                            ->minValue(2)
                            ->maxValue(50),

                        MarkdownEditor::make('description')
                            ->label('Cargo description'),
                    ]),

                    Section::make('Cargo Configuration')->schema([

                        Select::make('type')
                            ->label('Cargo type')
                            ->required()
                            ->options(returnWithKeyValuesArray(Cargo::TYPE))
                            ->in(implode(',', Cargo::TYPE))
                            ->columnSpan(1),

                        Select::make('status')
                            ->label('Cargo status')
                            ->required()
                            ->options(returnWithKeyValuesArray(Cargo::STATUS))
                            ->in(implode(',', Cargo::STATUS))
                            ->columnSpan(1),


                        Select::make('customer_id')
                            ->label('For customer')
                            ->relationship('customer', 'fname')
                            ->required()
                            ->exists('users', 'id')
                            ->getOptionLabelFromRecordUsing(fn($record) => $record->fname . ' ' . $record->lname)
                            ->columnSpan(2),

                    ])->columns(2),

                ]),

                Group::make()->schema([
                    Section::make('Dimensions & Weight')->schema([
                        TextInput::make('weight')
                            ->label('Cargo weight')
                            ->helperText('Take care about weight unit.')
                            ->required()
                            ->minValue(1)
                            ->columnSpan(1),

                        Select::make('weight_unit')
                            ->label('Weight unit')
                            ->helperText('KG for Kilo Gram weight unit.')
                            ->options([
                                'kg' => 'KG',
                                'ton' => 'Ton',
                            ])
                            ->required()
                            ->columnSpan(2),

                        TextInput::make('width')
                            ->label('Cargo width')
                            ->helperText('In M - Meter')
                            ->required()
                            ->minValue(1)
                            ->reactive()
                            ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get) {
                                self::calculateVolume($set, $get);
                            })
                            ->columnSpan(1),

                        TextInput::make('height')
                            ->label('Cargo height')
                            ->helperText('In M - Meter')
                            ->required()
                            ->minValue(1)
                            ->reactive()
                            ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get) {
                                self::calculateVolume($set, $get);
                            })
                            ->columnSpan(1),

                        TextInput::make('length')
                            ->label('Cargo length')
                            ->helperText('In M - Meter')
                            ->required()
                            ->minValue(1)
                            ->reactive()
                            ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get) {
                                self::calculateVolume($set, $get);
                            })
                            ->columnSpan(1),

                        TextInput::make('volume')
                            ->label('Cargo volume')
                            ->helperText('In M3 - Meter³')
                            ->required()
                            ->disabled()
                            ->columnSpan(3),

                    ])->columns(3)->collapsible(),
                ]),

            ]);
    }

    protected static function calculateVolume(Forms\Set $set, Forms\Get $get)
    {
        $width = $get('width');
        $height = $get('height');
        $length = $get('length');

        if ($width && $height && $length) {
            $set('volume', $width * $height * $length);
        }
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Name')
                    ->sortable()
                    ->searchable()
                    ->limit(35)
                    ->wrap(),

                TextColumn::make('description')
                    ->label('Description')
                    ->sortable()
                    ->searchable()
                    ->markdown()
                    ->limit(35)
                    ->wrap(),

                TextColumn::make('weight')
                    ->label('Weight')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(fn($record) => $record->weight . ' ' . $record->weight_unit),

                TextColumn::make('width')
                    ->label('Width')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->formatStateUsing(fn($record) => $record->width . ' M'),

                TextColumn::make('height')
                    ->label('Height')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->formatStateUsing(fn($record) => $record->height . ' M'),

                TextColumn::make('length')
                    ->label('Length')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->formatStateUsing(fn($record) => $record->length . ' M'),

                TextColumn::make('volume')
                    ->label('Volume')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(fn($record) => $record->volume . ' M3'),

                TextColumn::make('status')
                    ->label('Shipping status')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('type')
                    ->label('Cargo type')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('customer.fname')
                    ->label('Customer')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(fn($record) => $record->customer->fname . ' ' . $record->customer->lname),

                TextColumn::make('created_at')
                    ->label('Added at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Last use at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //

                SelectFilter::make('status')
                    ->options(returnWithKeyValuesArray(Cargo::STATUS)),

                SelectFilter::make('weight_unite')
                    ->options([
                        'kg' => 'KG - Kilo Gram',
                        'ton' => 'Ton'
                    ]),

                SelectFilter::make('type')
                    ->options(returnWithKeyValuesArray(Cargo::TYPE)),

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
            'index' => Pages\ListCargos::route('/'),
            'create' => Pages\CreateCargo::route('/create'),
            'edit' => Pages\EditCargo::route('/{record}/edit'),
        ];
    }
}
