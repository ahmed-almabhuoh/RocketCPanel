<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Filament\Resources\TransactionResource\RelationManagers;
use App\Models\Transaction;
use Carbon\Carbon;
use DeepCopy\Filter\Filter;
use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter as FiltersFilter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use ValentinMorice\FilamentJsonColumn\FilamentJsonColumn;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationGroup = 'Financial Management - FM -';

    protected static ?string $navigationLabel = 'Payment Transactions';

    protected static ?int $navigationSort = 2;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function canView(Model $record): bool
    {
        return true;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Group::make()->schema([
                    Section::make('Transaction Payload')->schema([

                        FilamentJsonColumn::make('method_meta')
                            ->label('Transaction Payload'),

                    ]),
                ])->columnSpanFull(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //

                TextColumn::make('invoice_code')
                    ->label('Invoice No.')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('user.fname')
                    ->label('User')
                    ->formatStateUsing(fn($record) => $record->user->fname . ' ' . $record->user->lname)
                    ->sortable()
                    ->searchable(),

                TextColumn::make('orbits')
                    ->label('Orbits')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(fn($record) => $record->orbits . ' Os'),


                TextColumn::make('price')
                    ->label('Price')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(fn($record) => $record->price  . ' USD'),


                TextColumn::make('operation_type')
                    ->label('Transaction type')
                    ->formatStateUsing(fn($record) => ucfirst($record->operation_type))
                    ->sortable()
                    ->searchable(),

                TextColumn::make('status')
                    ->formatStateUsing(fn($record) => ucfirst($record->status))
                    ->label('Transaction status')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('credit_types')
                    ->label('Credit type')
                    ->formatStateUsing(fn($record) => ucfirst($record->credit_types))
                    ->sortable()
                    ->searchable(),

                TextColumn::make('method')
                    ->label('Transaction method')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->formatStateUsing(fn($record) => ucfirst($record->method))
                    ->sortable()
                    ->searchable(),

                TextColumn::make('vat')
                    ->label('VAT')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->formatStateUsing(fn($record) => round($record->vat) . ' USD'),

                TextColumn::make('expiration_days')
                    ->label('Days Remaining')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->formatStateUsing(function ($record) {
                        $createdAt = Carbon::parse($record->created_at);
                        $expirationDays = $record->expiration_days;

                        $expirationDate = $createdAt->copy()->addDays($expirationDays);
                        $remainingDays = round(Carbon::now()->diffInDays($expirationDate, false)); // Calculate remaining days

                        if ($remainingDays < 0) {
                            return 'Expired';
                        }

                        return $remainingDays . ' days remaining';
                    })
                    ->searchable(),


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

                FiltersFilter::make('vat')
                    ->label('With VATs')
                    ->toggle()
                    ->query(fn($query) => $query->whereNotNull('vat')),

                SelectFilter::make('operation_type')
                    ->label('Operation Type')
                    ->options(returnWithKeyValuesArray(Transaction::TYPE)),

                SelectFilter::make('status')
                    ->label('Transaction Status')
                    ->options(returnWithKeyValuesArray(Transaction::STATUS)),

                SelectFilter::make('method')
                    ->label('Transaction Method')
                    ->options(returnWithKeyValuesArray(Transaction::METHODS)),

                SelectFilter::make('credit_types')
                    ->label('Credit Type')
                    ->options(returnWithKeyValuesArray(Transaction::CREDIT_TYPE)),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
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
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }
}
