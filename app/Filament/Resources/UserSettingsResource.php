<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserSettingsResource\Pages;
use App\Filament\Resources\UserSettingsResource\RelationManagers;
use App\Models\UserSettings;
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
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserSettingsResource extends Resource
{
    protected static ?string $model = UserSettings::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-8-tooth';

    protected static ?string $navigationGroup = 'Content Management - CM -';

    protected static ?string $navigationLabel = 'User Account Settings';

    protected static ?int $navigationSort = 2;

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //

                Group::make()->schema([

                    Section::make('Establish Time & Localization')->schema([

                        Select::make('time_zone')
                            ->label('Timezone')
                            ->required()
                            ->searchable()
                            ->options(returnWithKeyValuesArray(array_keys(config('timezones'), true)))
                            ->columnSpan(1),

                        Select::make('lang')
                            ->label('Language')
                            ->required()
                            ->in(['ar', 'en'])
                            ->searchable()
                            ->options([
                                'ar' => 'Arabic',
                                'en' => 'English',
                            ])
                            ->columnSpan(1),

                        Select::make('user_id')
                            ->label('User')
                            ->searchable()
                            ->relationship('user', 'username', function ($query) {
                                $query->doesntHave('settings');
                            })
                            ->required()
                            ->columnSpanFull(),

                    ])->columns(2),
                ]),


                Group::make()->schema([

                    Section::make('Account Privacy')->schema([

                        Toggle::make('private_email')
                            ->label('Make email private?'),

                        Toggle::make('private_phone')
                            ->label('Make phone private?'),

                        Toggle::make('private_account')
                            ->label('Make account private?'),

                    ]),

                    Section::make('Account Security')->schema([

                        Toggle::make('login_verification')
                            ->label('Verify user when login?'),

                        Toggle::make('enable_2fa')
                            ->label('Enable 2FA?'),

                        Toggle::make('required_personal_information_to_reset_password')
                            ->label('Ask for personal information to reset password?'),

                    ]),
                ]),

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

                TextColumn::make('lang')
                    ->label('Language')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(fn($record) => strtoupper($record->lang)),

                IconColumn::make('enable_2fa')
                    ->label('Enable 2FA')
                    ->boolean(),

                IconColumn::make('login_verification')
                    ->label('Verify when login')
                    ->boolean(),

                IconColumn::make('required_personal_information_to_reset_password')
                    ->label('Information required to reset password')
                    ->boolean(),

                IconColumn::make('private_email')
                    ->label('Make email private')
                    ->boolean(),

                IconColumn::make('private_phone')
                    ->label('Make phone private')
                    ->boolean(),

                IconColumn::make('private_account')
                    ->label('Make account private')
                    ->boolean(),

                TextColumn::make('time_zone')
                    ->label('Timezone')
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

                Filter::make('login_verification')
                    ->toggle()
                    ->label('Verify When Logged In'),

                Filter::make('enable_2fa')
                    ->toggle()
                    ->label('2FA Enabled'),

                Filter::make('required_personal_information_to_reset_password')
                    ->toggle()
                    ->label('Constraints on Change Password'),

                Filter::make('private_email')
                    ->toggle()
                    ->label('Email Private'),

                Filter::make('private_account')
                    ->toggle()
                    ->label('Email Account'),

                Filter::make('private_phone')
                    ->toggle()
                    ->label('Email Phone'),

                SelectFilter::make('time_zone')
                    ->label('Timezone')
                    ->searchable()
                    ->options(returnWithKeyValuesArray(array_keys(config('timezones'))))
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\ViewAction::make(),
                ]),
            ])
            ->bulkActions([]);
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
            'index' => Pages\ListUserSettings::route('/'),
            'create' => Pages\CreateUserSettings::route('/create'),
            'edit' => Pages\EditUserSettings::route('/{record}/edit'),
        ];
    }
}
