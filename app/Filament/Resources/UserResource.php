<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms;
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

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Human Resources - HR-';

    protected static ?string $navigationLabel = 'Users & admins';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
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

                    // Action::make('login_as_user')
                    //     ->label('Login as User')
                    //     ->color('primary')
                    //     ->icon('heroicon-o-lock-open')
                    //     ->action(function (User $record) {
                    //         $enc_username = Crypt::encrypt($record->username);

                    //         // ShM Link
                    //         $shmLink = config('app.env') == 'local' ? config('app.rocket_shm_dashboard_local') : config('app.rocket_shm_dashboard_production'); // Change to your desired URL

                    //         info($shmLink);

                    //         $response = Http::get($shmLink + '/cp/configs/login/' + $enc_username);

                    //         if ($response) {
                    //             Notification::make()
                    //                 ->title('Login Success')
                    //                 ->success()
                    //                 ->body("You are logged in as {$record->fname}, go to: " + $shmLink)
                    //                 ->send();
                    //             return;
                    //         }


                    //         Notification::make()
                    //             ->title('Login Failed')
                    //             ->failed()
                    //             ->body("Failed to login as {$record->fname}")
                    //             ->send();
                    //     })
                    //     ->requiresConfirmation()
                    //     ->visible(true),

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
                        ->visible(fn(User $record) => ($record->account_status == 'inactive' || $record->account_status == 'pending')),


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
                        ->visible(fn(User $record) => ($record->account_status == 'active' || $record->account_status == 'inactive')),


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
                        ->visible(fn(User $record) => ($record->account_status == 'inactive' || $record->account_status == 'pending')),
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
