<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GiftResource\Pages;
use App\Filament\Resources\GiftResource\RelationManagers;
use App\Models\Gift;
use Filament\Forms;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
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

class GiftResource extends Resource
{
    protected static ?string $model = Gift::class;

    protected static ?string $navigationIcon = 'heroicon-o-gift';

    protected static ?string $navigationGroup = 'Content Management - CM -';

    protected static ?string $navigationLabel = 'Gifts';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //

                Group::make()->schema([
                    Section::make()->schema([
                        TextInput::make('title_ar')
                            ->label('Title AR')
                            ->required()
                            ->minValue(2)
                            ->unique()
                            ->maxValue(50),

                        TextInput::make('title_en')
                            ->label('Title EN')
                            ->required()
                            ->minValue(2)
                            ->unique()
                            ->maxValue(50),

                        Select::make('status')
                            ->label('Gift Status')
                            ->required()
                            ->in(['active', 'inactive'])
                            ->options(returnWithKeyValuesArray(Gift::STATUS))
                            ->helperText('Active gift will be directly used')
                            ->columnSpanFull(),

                    ])->columns(2),
                ]),

                Group::make()->schema([
                    Section::make('Percentage')->schema([
                        Toggle::make('fixed')
                            ->label('Mark as fixed gift')
                            ->helperText('Fixed gift will be used for both registration and deposit operations')
                            ->columnSpanFull(),

                        TextInput::make('credits')
                            ->label('Gift Credits - Os')
                            ->required()
                            ->minValue(1),

                        TextInput::make('percentage')
                            ->label('Percentage')
                            ->required()
                            ->maxValue(100)
                            ->minValue(1),

                    ])->columns(2),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                TextColumn::make('title_ar')
                    ->label('Title AR')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('title_en')
                    ->label('Title EN')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(fn($record) => ucfirst($record->title_en)),

                IconColumn::make('fixed')
                    ->label('Fixed')
                    ->boolean(),

                // TextColumn::make('status')
                //     ->label('Gift Status')
                //     ->sortable()
                //     ->searchable()
                //     ->formatStateUsing(fn($record) => ucfirst($record->status)),
                IconColumn::make('status')
                    ->options([
                        'heroicon-o-check-circle' => 'active',
                        'heroicon-o-x-circle' => 'inactive',
                    ])
                    ->colors([
                        'success' => 'active',
                        'danger' => 'inactive',
                    ])
                    ->label('Gift status')
                    ->getStateUsing(fn($record) => $record->status),

                TextColumn::make('credits')
                    ->label('Credits')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(fn($record) => $record->credits . ' Os'),

                TextColumn::make('percentage')
                    ->label('Percentage')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(fn($record) => $record->percentage . '%'),

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
                SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                    ]),

                Filter::make('fixed')
                    ->toggle()
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
            'index' => Pages\ListGifts::route('/'),
            'create' => Pages\CreateGift::route('/create'),
            'edit' => Pages\EditGift::route('/{record}/edit'),
        ];
    }
}
