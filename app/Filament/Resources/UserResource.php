<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Human Resources - HR-';

    protected static ?string $navigationLabel = 'Users & admins';

    protected static ?int $navigationSort = 0;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //

                Group::make()->schema([
                    Section::make('General')->schema([
                        TextInput::make('fname')
                            ->required()
                            ->minValue(2)
                            ->maxValue(50)
                            ->label('First name'),

                        TextInput::make('lname')
                            ->required()
                            ->minValue(2)
                            ->maxValue(50)
                            ->label('Last name'),

                        TextInput::make('phone')
                            ->required()
                            ->unique()
                            ->minValue(7)
                            ->maxValue(20)
                            ->label('Phone No.'),

                        TextInput::make('email')
                            ->required()
                            ->unique()
                            ->email()
                            ->label('Email address'),

                        TextInput::make('username')
                            ->label('Username')
                            ->required()
                            ->unique()
                            ->columnSpanFull(),

                        TextInput::make('password')
                            ->required()
                            ->minValue(8)
                            ->maxValue(50)
                            ->helperText('User password will automatically change, with notify user about this action.')
                            ->default(Str::random(8))
                            ->columnSpanFull(),

                    ])->columns(2),
                ]),

                Group::make()->schema([
                    Section::make('Account Settings')->schema([
                        Select::make('role')
                            ->options([
                                'Director' => 'Director',
                                'Customer' => 'Customer',
                                'Driver' => 'Driver',
                            ])
                            ->required()
                            ->rule([
                                'in:Director,Customer,Driver'
                            ])
                            ->reactive()
                            ->label('Account role'),

                        Select::make('account_status')
                            ->options([
                                'active' => 'Active',
                                'pending' => 'Pending',
                                'inactive' => 'Inactive',
                            ])
                            ->required()
                            ->rule([
                                'in:active,pending,inactive'
                            ])
                            ->label('Account status'),

                        Select::make('user_id')
                            ->relationship('director', 'fname')
                            ->label('Direct')
                            ->rule([
                                'exists:users,id'
                            ])
                            ->visible(fn($get) => $get('role') == 'Driver'),

                        Toggle::make('is_admin')
                            ->helperText('Marking account as admin enable user to access this control panel.')
                            ->label('Mark it as admin?'),

                    ]),
                ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                TextColumn::make('fname')->label('First name')->searchable()->sortable(),
                TextColumn::make('lname')->label('Last name')->searchable()->sortable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('phone')->label('Phone No.')->searchable()->sortable(),
                TextColumn::make('email')->label('E-mail')->searchable()->sortable(),
                TextColumn::make('username')->label('Username')->searchable()->sortable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('role')->label('User role'),

                // IconColumn::make('account_status')->label('Account status')->boolean(),
                IconColumn::make('account_status')
                    ->label('Account Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle') // Icon for 'active' status
                    ->falseIcon('heroicon-o-x-circle')    // Icon for other statuses
                    ->getStateUsing(fn($record) => $record->account_status === 'active'),

                IconColumn::make('is_admin')
                    ->label('Admin')
                    ->boolean(),


                TextColumn::make('email_verified_at')
                    ->label('Verified at')
                    // ->formatStateUsing(fn($state) => \Carbon\Carbon::parse($state)->diffForHumans())->toggleable(isToggledHiddenByDefault: true)
                    ->date()
                    ->searchable()->sortable(),

                TextColumn::make('created_at')
                    ->label('Registered at')
                    // ->formatStateUsing(fn($state) => \Carbon\Carbon::parse($state)->diffForHumans())
                    ->date()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable()->sortable(),

                TextColumn::make('updated_at')
                    ->label('Reserved at')
                    // ->formatStateUsing(fn($state) => \Carbon\Carbon::parse($state)->diffForHumans())
                    ->date()
                    ->toggleable(isToggledHiddenByDefault: true)->searchable()->sortable(),

            ])
            ->filters([
                //

                // Admins Filter
                SelectFilter::make('is_admin')
                    ->label('User Role')
                    ->options([
                        true => 'Admin',
                        false => 'User',
                    ])
                    ->default(false),

                SelectFilter::make('account_status')
                    ->label('Account Status')
                    ->options([
                        'active' => 'Active accounts',
                        'inactive' => 'Blocked accounts',
                        'pending' => 'Pending accounts',
                    ]),

                Filter::make('email_verified_at')
                    ->label('Verified Account')
                    ->toggle() // Adds a toggle button for the filter
                    ->default(true)
                    ->query(fn($query) => $query->whereNotNull('email_verified_at')),


            ])
            ->actions([

                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\ViewAction::make(),

                    Tables\Actions\Action::make('unblocked')
                        ->label('Activate User')
                        ->color('success')
                        ->icon('heroicon-o-lock-open')
                        ->action(function (User $record) {
                            $record->account_status = 'active';
                            $record->save();

                            Notification::make()
                                ->title('User Unblocked')
                                ->success()
                                ->body("User {$record->fname} has been activated.")
                                ->send();
                        })
                        ->requiresConfirmation()
                        ->visible(fn(User $record) => $record->account_status != 'active'),


                    Tables\Actions\Action::make('pending')
                        ->label('Set Pending')
                        ->color('warning')
                        ->icon('heroicon-o-clock')
                        ->action(function (User $record) {
                            $record->account_status = 'pending';
                            $record->save();

                            Notification::make()
                                ->title('User Set to Pending')
                                ->success()
                                ->body("User {$record->fname}'s account is now pending.")
                                ->send();
                        })
                        ->requiresConfirmation()
                        ->visible(fn(User $record) => $record->account_status != 'pending'),


                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\Action::make('block')
                        ->label('Block User')
                        ->color('danger')
                        ->icon('heroicon-o-lock-closed')
                        ->action(function (User $record) {
                            $record->account_status = 'inactive';
                            $record->save();

                            Notification::make()
                                ->title('User Blocked')
                                ->success()
                                ->body("User {$record->fname} has been blocked.")
                                ->send();
                        })
                        ->requiresConfirmation()
                        ->visible(fn(User $record) => $record->account_status != 'inactive'),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
