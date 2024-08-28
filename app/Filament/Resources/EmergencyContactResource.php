<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmergencyContactResource\Pages;
use App\Filament\Resources\EmergencyContactResource\RelationManagers;
use App\Models\EmergencyContact;
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
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EmergencyContactResource extends Resource
{
    protected static ?string $model = EmergencyContact::class;

    protected static ?string $navigationIcon = 'heroicon-o-device-phone-mobile';

    protected static ?string $navigationGroup = 'Content Management - CM -';

    protected static ?string $navigationLabel = 'Driver Emergency Contact';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //

                Group::make()->schema([
                    Section::make('General')->schema([

                        TextInput::make('name')
                            ->label('Contact name')
                            ->required()
                            ->minValue(2)
                            ->maxValue(50),

                        Select::make('relationship')
                            ->label('Contact relationship')
                            ->options(returnWithKeyValuesArray(EmergencyContact::RELATIONSHIPS))
                            ->required()
                            ->in(returnWithKeyValuesArray(EmergencyContact::RELATIONSHIPS, true)),

                        MarkdownEditor::make('description')
                            ->label('Contact description')
                            ->columnSpan('full'),

                    ])->columns(2),
                ]),


                Group::make()->schema([
                    Section::make('Contact Information')->schema([

                        TextInput::make('phone')
                            ->label('Phone No.')
                            ->helperText('No. for Number')
                            ->required()
                            ->rules('required', 'regex:/^\+?[1-9]\d{1,14}$/'),

                        TextInput::make('email')
                            ->label('E-mail address')
                            ->email(),

                        Select::make('driver_id')
                            ->relationship('driver', 'fname')
                            ->required()
                            ->exists('users', 'id')
                            ->getOptionLabelFromRecordUsing(fn($record) => $record->fname . ' ' . $record->lname)
                            ->columnSpan(2),


                    ]),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //

                TextColumn::make('name')
                    ->label('Contact name')
                    ->searchable()
                    ->sortable(),


                TextColumn::make('relationship')
                    ->label('Driver relationship')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('driver.fname')
                    ->label('Driver')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn($record) => $record->driver->fname . ' ' . $record->driver->lname),

                TextColumn::make('phone')
                    ->label('Phone No.')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label('E-mail address')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('description')
                    ->label('Contact description')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Added at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Last update')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //

                SelectFilter::make('relationship')
                    ->label('Has relationship')
                    ->options(returnWithKeyValuesArray(EmergencyContact::RELATIONSHIPS, true)),

            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListEmergencyContacts::route('/'),
            'create' => Pages\CreateEmergencyContact::route('/create'),
            'edit' => Pages\EditEmergencyContact::route('/{record}/edit'),
        ];
    }
}
