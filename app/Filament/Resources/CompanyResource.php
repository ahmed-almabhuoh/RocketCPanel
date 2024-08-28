<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompanyResource\Pages;
use App\Filament\Resources\CompanyResource\RelationManagers;
use App\Models\Company;
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

class CompanyResource extends Resource
{
    protected static ?string $model = Company::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    protected static ?string $navigationGroup = 'Content Management - CM -';

    protected static ?string $navigationLabel = 'Companies';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //

                Group::make()->schema([
                    Section::make('Details Information')->schema([

                        TextInput::make('name')
                            ->label('Company name')
                            ->required()
                            ->minValue(2)
                            ->maxValue(50),

                        TextInput::make('email')
                            ->label('Company E-mail address')
                            ->required()
                            ->unique()
                            ->email(),

                        TextInput::make('phone')
                            ->label('Company Phone No.')
                            ->helperText('No. for Number')
                            ->required()
                            ->unique()
                            ->regex('/^\+?[0-9\s\-()]*$/')
                            ->maxLength(15),

                        TextInput::make('website')
                            ->label('Company Website')
                            ->helperText('e.g. company.com')
                            ->required()
                            ->unique()
                            ->regex('/^(https?:\/\/)?(www\.)?([a-zA-Z0-9-]+\.)+[a-zA-Z]{2,}(\/\S*)?$/')
                            ->maxLength(255),

                        Select::make('director_id')
                            ->relationship('director', 'fname')
                            ->label('For director')
                            ->getOptionLabelFromRecordUsing(function ($record) {
                                return $record->fname . ' ' . $record->lname;
                            })
                            ->required()
                            ->rule(['integer'])
                            ->exists('users', 'id')
                            ->columnSpan('full'),

                        MarkdownEditor::make('description')
                            ->label('Description about company')
                            ->columnSpan('full'),

                    ])->columns(2),
                ]),

                Group::make()->schema([
                    Section::make('Location')->schema([

                        TextInput::make('general_location')
                            ->label('Location')
                            ->required()
                            ->minValue(2)
                            ->maxValue(50),

                        TextInput::make('breif_location')
                            ->label('Detailed location'),

                        Select::make('country')
                            ->label('Country')
                            ->options(config('countries'))
                            ->required()
                            ->in(returnWithKeyValuesArray(config('countries'), true)),

                        TextInput::make('city')
                            ->label('City')
                            ->required()
                            ->minValue(2)
                            ->maxValue(50),

                        TextInput::make('state')
                            ->label('State')
                            ->required()
                            ->minValue(2)
                            ->maxValue(50),

                        TextInput::make('postcode')
                            ->label('Postal code')
                            ->required()
                            ->minValue(2)
                            ->maxValue(50)
                            ->regex('/^[A-Za-z0-9\s\-]{2,50}$/')
                            ->helperText('Enter a valid postal code (e.g., 12345, A1A 1A1, 123-4567)'),

                    ])->columns(2),
                ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //

                TextColumn::make('name')
                    ->label('Company')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('director.fname')
                    ->label('Director')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn($record) => $record->director->fname . ' ' .  $record->director->lname),

                TextColumn::make('email')
                    ->label('E-mail address')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('phone')
                    ->label('Phone No.')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('website')
                    ->label('Website')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('general_location')
                    ->label('Address')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn($record) => ucfirst($record->general_location)),


                TextColumn::make('country')
                    ->label('Country')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->formatStateUsing(fn($record) => ucfirst($record->general_location)),

                TextColumn::make('brief_location')
                    ->label('Detailed location')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->formatStateUsing(fn($record) => ucfirst($record->general_location)),

                TextColumn::make('post_code')
                    ->label('Post code')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('city')
                    ->label('City')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->formatStateUsing(fn($record) => ucfirst($record->general_location)),

                TextColumn::make('state')
                    ->label('State')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->formatStateUsing(fn($record) => ucfirst($record->general_location)),

                TextColumn::make('description')
                    ->label('About')
                    ->markdown()
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Added at')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Last used at')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
                SelectFilter::make('country')
                    ->options(config('countries')),

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
            'index' => Pages\ListCompanies::route('/'),
            'create' => Pages\CreateCompany::route('/create'),
            'edit' => Pages\EditCompany::route('/{record}/edit'),
        ];
    }
}
