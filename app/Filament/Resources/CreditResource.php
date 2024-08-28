<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CreditResource\Pages;
use App\Filament\Resources\CreditResource\RelationManagers;
use App\Models\Balance;
use App\Models\Credit;
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
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CreditResource extends Resource
{
    protected static ?string $model = Credit::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $navigationGroup = 'Financial Management - FM -';

    protected static ?string $navigationLabel = 'Credits Management';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //

                Group::make()->schema([

                    Section::make()->schema([

                        TextInput::make('credits')
                            ->label('Credits')
                            ->helperText('Credit will added to customer account.')
                            ->required()
                            ->rules(['integer'])
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(100)
                            ->reactive()
                            ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get) {
                                self::calculateBalanceBeforeAndAfter($set, $get);
                            }),

                        Select::make('user_id')
                            ->label('For user')
                            ->relationship('user', 'fname')
                            ->required()
                            ->exists('users', 'id')
                            ->reactive()
                            ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get) {
                                self::calculateBalanceBeforeAndAfter($set, $get);
                            })
                            ->getOptionLabelFromRecordUsing(fn($record) => $record->fname . ' ' . $record->lname),

                        Select::make('type')
                            ->label('Operation type')
                            ->options([
                                'deposit' => 'Deposit',
                                'withdraw' => 'Withdraw',
                            ])
                            ->required()
                            ->in(['deposit', 'withdraw'])
                            ->reactive()
                            ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get) {
                                self::calculateBalanceBeforeAndAfter($set, $get);
                            })->columnSpanFull(),

                        TextInput::make('balance_before')
                            ->label('Balance before')
                            ->required()
                            ->disabled()
                            ->dehydrated()
                            ->rules(['integer']),

                        TextInput::make('balance_after')
                            ->label('Balance after')
                            ->disabled()
                            ->required()
                            ->dehydrated()
                            ->rules(['integer']),

                    ])->columns(2),

                ]),

                Group::make()->schema([

                    Section::make('Reason')->schema([
                        MarkdownEditor::make('reason')
                            ->label('Operation description')
                            ->required(),
                    ]),

                ]),

            ]);
    }

    protected static function calculateBalanceBeforeAndAfter(Forms\Set $set, Forms\Get $get)
    {
        $credits = $get('credits');
        $user_id = $get('user_id');
        $type = $get('type');

        if ($credits && $user_id && $user_id) {
            $userBalance = Balance::where('user_id', $user_id)->first();
            $set('balance_before', $userBalance?->orbits);
            $set('balance_after', $type == 'deposit' ? $userBalance?->orbits + $credits : $userBalance?->orbits - $credits);
        }
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //

                TextColumn::make('credits')
                    ->label('Orbit credits')
                    ->formatStateUsing(fn($state) => $state . ' Os')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('balance_before')
                    ->label('Balance before')
                    ->formatStateUsing(fn($state) => $state . ' Os')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('balance_after')
                    ->label('Balance after')
                    ->formatStateUsing(fn($state) => $state . ' Os')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('type')
                    ->label('Operation type')
                    ->formatStateUsing(fn($state) => ucfirst($state))
                    ->sortable()
                    ->searchable(),

                TextColumn::make('user.fname')
                    ->label('User')
                    ->formatStateUsing(fn($record) => $record->user->fname . ' ' . $record->user->lname)
                    ->sortable()
                    ->searchable(),

                TextColumn::make('transaction.invoice_code')
                    ->label('Transaction')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('reason')
                    ->label('Description')
                    ->sortable()
                    ->searchable()
                    ->markdown()
                    ->toggleable(isToggledHiddenByDefault: true),

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

                SelectFilter::make('type')
                    ->options([
                        'deposit' => 'Deposit',
                        'withdraw' => 'Withdraw',
                    ]),

            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListCredits::route('/'),
            'create' => Pages\CreateCredit::route('/create'),
            'edit' => Pages\EditCredit::route('/{record}/edit'),
        ];
    }
}
