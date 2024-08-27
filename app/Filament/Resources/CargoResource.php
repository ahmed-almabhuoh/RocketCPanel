<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CargoResource\Pages;
use App\Filament\Resources\CargoResource\RelationManagers;
use App\Models\Cargo;
use Filament\Forms;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Livewire;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Support\Markdown;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CargoResource extends Resource
{
    protected static ?string $model = Cargo::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox-stack';

    protected static ?string $navigationGroup = 'Content Management - CM -';

    protected static ?string $navigationLabel = 'Cargos';

    protected static ?int $navigationSort = 2;



    public static function form(Form $form): Form
    {
        $volume = 1;

        return $form
            ->schema([
                //

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
                            ->columnSpan(1),

                        TextInput::make('height')
                            ->label('Cargo height')
                            ->helperText('In M - Meter')
                            ->required()
                            ->minValue(1)
                            ->columnSpan(1),

                        TextInput::make('length')
                            ->label('Cargo length')
                            ->helperText('In M - Meter')
                            ->required()
                            ->minValue(1)
                            ->columnSpan(1),

                        TextInput::make('volume')
                            ->label('Cargo volume')
                            ->helperText('In M3 - Meter³')
                            ->required()
                            ->disabled()
                            ->dehydrated()
                            ->columnSpan(3),

                    ])->columns(3)->collapsible(),
                ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //

                TextColumn::make('name')
                    ->label('Name')
                    ->sortable()
                    ->searchable()
                    ->limit(35)
                    ->wrap()
                    ->extraAttributes([
                        'style' => 'width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;',
                    ]),

                TextColumn::make('description')
                    ->label('Description')
                    ->sortable()
                    ->searchable()
                    ->limit(35)
                    ->wrap()
                    ->extraAttributes([
                        'style' => 'width: 250px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;',
                    ]),

                TextColumn::make('weight')
                    ->label('Weight')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(function ($record) {
                        return $record->weight . ' ' . $record->weight_unit;
                    }),

                TextColumn::make('width')
                    ->label('Width')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->formatStateUsing(function ($record) {
                        return $record->width . ' M';
                    }),

                TextColumn::make('height')
                    ->label('Height')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->formatStateUsing(function ($record) {
                        return $record->height . ' M';
                    }),

                TextColumn::make('length')
                    ->label('Length')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->formatStateUsing(function ($record) {
                        return $record->length . ' M';
                    }),

                TextColumn::make('volume')
                    ->label('Volume')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(function ($record) {
                        return $record->length . ' M3';
                    }),

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
                    ->formatStateUsing(function ($record) {
                        return $record->customer->fname . ' ' . $record->customer->lname;
                    })
                    ->action(function ($record) {
                        // return redirect()->route('filament.resources.users.edit', $record->customer->id);
                    }),

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
