<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransferCodeResource\Pages;
use App\Filament\Resources\TransferCodeResource\RelationManagers;
use App\Models\TransferCode;
use DeepCopy\Filter\Filter;
use Filament\Forms;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\MultiSelect;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TagsColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter as FiltersFilter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class TransferCodeResource extends Resource
{
    protected static ?string $model = TransferCode::class;


    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $navigationGroup = 'Financial Management - FM -';

    protected static ?string $navigationLabel = 'Transfer Codes';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //

                Group::make()->schema([
                    Section::make('Credentials')->schema([
                        TextInput::make('code')
                            ->label('Transfer Code')
                            ->minLength(2)
                            ->maxLength(50)
                            ->default(fn() => Str::random(10))
                            ->required()
                            ->unique(table: 'transfer_codes', column: 'code', ignoreRecord: true)
                            ->columnSpan(2),

                        TextInput::make('secret')
                            ->label('Secret')
                            ->helperText('Secret will be hashed, so keep it safe.')
                            ->unique()
                            ->minLength(2)
                            ->maxLength(50)
                            ->default(fn() => Str::random(20))
                            ->required()
                            ->columnSpan(2)
                            ->dehydrateStateUsing(function ($state) {
                                return Hash::make($state);
                            })
                            ->hidden(fn($context) => $context === 'edit'),

                        Select::make('user_id')
                            ->label('For user')
                            ->relationship('user', 'fname')
                            ->required()
                            ->exists('users', 'id')
                            ->getOptionLabelFromRecordUsing(fn($record) => $record->fname . ' ' . $record->lname)
                            ->columnSpan(2),
                    ])->columns(2),
                ]),

                Group::make()->schema([
                    Section::make('Code Limitation')->schema([

                        Select::make('status')
                            ->label('Code status')
                            ->options(returnWithKeyValuesArray(TransferCode::STATUS))
                            ->required()
                            ->in(['active', 'inactive'])
                            ->columnSpan(2),


                        Toggle::make('limited')
                            ->label('Limited to use?')
                            ->columnSpan(2)
                            ->reactive()
                            ->helperText('You can use your code to transfer or receive orbits N times'),

                        TextInput::make('time_to_use')
                            ->label('Time to use')
                            ->numeric()
                            ->minValue(0)
                            ->visible(fn($get) => $get('limited') == true)
                            ->columnSpan(2),

                        MultiSelect::make('only_for')
                            ->label('Only for Users')
                            ->options(function () {
                                return \App\Models\User::pluck('username', 'username')->toArray();
                            })
                            ->columnSpan(2)
                            ->afterStateHydrated(function ($state, $set) {
                                if (is_string($state)) {
                                    $set('only_for', json_decode($state, true) ?? []);
                                } else {
                                    $set('only_for', $state);
                                }
                            })
                            ->dehydrateStateUsing(function ($state) {
                                return is_array($state) ? json_encode($state) : $state;
                            }),

                    ])->columns(2),
                ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //

                TextColumn::make('code')
                    ->label('Transfer code')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('user.fname')
                    ->label('User')
                    ->sortable()
                    ->formatStateUsing(fn($record) => $record->user->fname . ' ' . $record->user->lname)
                    ->searchable(),

                IconColumn::make('status')
                    ->options([
                        'heroicon-o-check-circle' => 'active',
                        'heroicon-o-x-circle' => 'inactive',
                    ])
                    ->colors([
                        'success' => 'active',
                        'danger' => 'inactive',
                    ])
                    ->label('Status')
                    ->getStateUsing(fn($record) => $record->status),


                TextColumn::make('only_for')
                    ->label('Only for')
                    ->getStateUsing(function ($record) {
                        if (is_string($record->only_for)) {
                            return implode(", ", json_decode($record->only_for));
                        } else {
                            return implode(", ", $record->only_for);
                        }
                    }),


                IconColumn::make('limited')
                    ->boolean(),

                TextColumn::make('time_to_use')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                IconColumn::make('agreements')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Added at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),

            ])
            ->filters([
                //

                SelectFilter::make('status')
                    ->options(returnWithKeyValuesArray(TransferCode::STATUS)),

                FiltersFilter::make('limited')
                    ->toggle(),

                FiltersFilter::make('agreements')
                    ->toggle(),
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
            'index' => Pages\ListTransferCodes::route('/'),
            'create' => Pages\CreateTransferCode::route('/create'),
            'edit' => Pages\EditTransferCode::route('/{record}/edit'),
        ];
    }
}
