<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserAddressResource\Pages;
use App\Filament\Resources\UserAddressResource\RelationManagers;
use App\Models\UserAddress;
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

class UserAddressResource extends Resource
{
    protected static ?string $model = UserAddress::class;

    protected static ?string $navigationIcon = 'heroicon-o-globe-europe-africa';

    protected static ?string $navigationGroup = 'Content Management - CM -';

    protected static ?string $navigationLabel = 'User Address & Location';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //

                Group::make()->schema([

                    Section::make('User address')->schema([

                        TextInput::make('primary_address')
                            ->label('Address')
                            ->required()
                            ->minValue(3)
                            ->maxValue(50),

                        TextInput::make('optional_address')
                            ->label('Detailed address'),

                        TextInput::make('state')
                            ->label('State or City'),

                        Select::make('country')
                            ->searchable()
                            ->required()
                            ->in(returnWithKeyValuesArray(config('countries'), true))
                            ->options(returnWithKeyValuesArray(config('countries'))),

                        Select::make('user_id')
                            ->searchable()
                            ->relationship('user', 'username', function ($query) {
                                $query->doesntHave('address');
                            })
                            ->required()
                            ->exists('users', 'id')
                            ->columnSpanFull(),

                    ])->columns(2),

                ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //

                TextColumn::make('user.fname')
                    ->label('User')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(fn($record) => $record->user->fname . ' ' . $record->user->lname),

                TextColumn::make('primary_address')
                    ->sortable()
                    ->searchable()
                    ->label('Address'),

                TextColumn::make('optional_address')
                    ->sortable()
                    ->searchable()
                    ->label('Detailed address'),

                TextColumn::make('state')
                    ->sortable()
                    ->searchable()
                    ->label('State'),

                TextColumn::make('country')
                    ->sortable()
                    ->searchable(),

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

                SelectFilter::make('country')
                    ->searchable()
                    ->options(returnWithKeyValuesArray(config('countries'))),
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
            'index' => Pages\ListUserAddresses::route('/'),
            'create' => Pages\CreateUserAddress::route('/create'),
            'edit' => Pages\EditUserAddress::route('/{record}/edit'),
        ];
    }
}
