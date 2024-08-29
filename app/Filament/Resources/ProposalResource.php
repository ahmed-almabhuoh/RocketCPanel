<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProposalResource\Pages;
use App\Filament\Resources\ProposalResource\RelationManagers;
use App\Models\Proposal;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProposalResource extends Resource
{
    protected static ?string $model = Proposal::class;

    protected static ?string $navigationIcon = 'heroicon-o-identification';

    protected static ?string $navigationGroup = 'Content Management - CM -';

    protected static ?string $navigationLabel = 'Proposals';

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
        return false;
    }

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

                TextColumn::make('description')
                    ->label('Proposal')
                    ->sortable()
                    ->markdown()
                    ->searchable(),

                TextColumn::make('status')
                    ->label('Proposal status')
                    ->sortable()
                    ->markdown()
                    ->formatStateUsing(fn($record) => ucfirst($record->status))
                    ->searchable(),


                TextColumn::make('cargo.name')
                    ->label('With cargo')
                    ->sortable()
                    ->markdown()
                    ->searchable(),

                TextColumn::make('customer.fname')
                    ->label('Submitted by')
                    ->sortable()
                    ->markdown()
                    ->formatStateUsing(fn($record) => $record->customer->fname . ' ' . $record->customer->lname)
                    ->searchable(),

                TextColumn::make('trip.from_city')
                    ->label('On trip')
                    ->sortable()
                    ->markdown()
                    ->formatStateUsing(fn($record) => $record->trip->from_city . ' - ' . $record->trip->to_city)
                    ->searchable(),

                TextColumn::make('created_at')
                    ->label('Added at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),

            ])
            ->filters([
                //

                SelectFilter::make('status')
                    ->options(returnWithKeyValuesArray(Proposal::STATUS)),

                Tables\Filters\Filter::make('today')
                    ->label('Offenders Today')
                    ->toggle()
                    ->query(function (Builder $query) {
                        $patterns = config('offenders');
                        $today = Carbon::today();

                        $query->whereDate('created_at', $today)
                            ->where(function ($query) use ($patterns) {
                                foreach ($patterns as $name => $pattern) {
                                    $query->orWhereRaw("description REGEXP ?", [$pattern]);
                                }
                            });
                    }),

                Tables\Filters\Filter::make('week')
                    ->label('Offenders This Week')
                    ->toggle()
                    ->query(function (Builder $query) {
                        $patterns = config('offenders');
                        $startOfWeek = Carbon::now()->startOfWeek();
                        $endOfWeek = Carbon::now()->endOfWeek();

                        $query->whereBetween('created_at', [$startOfWeek, $endOfWeek])
                            ->where(function ($query) use ($patterns) {
                                foreach ($patterns as $name => $pattern) {
                                    $query->orWhereRaw("description REGEXP ?", [$pattern]);
                                }
                            });
                    }),

                Tables\Filters\Filter::make('month')
                    ->label('Offenders This Month')
                    ->toggle()
                    ->query(function (Builder $query) {
                        $patterns = config('offenders');
                        $startOfMonth = Carbon::now()->startOfMonth();
                        $endOfMonth = Carbon::now()->endOfMonth();

                        $query->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                            ->where(function ($query) use ($patterns) {
                                foreach ($patterns as $name => $pattern) {
                                    $query->orWhereRaw("description REGEXP ?", [$pattern]);
                                }
                            });
                    }),

                Tables\Filters\Filter::make('all')
                    ->label('All Offenders')
                    ->toggle()
                    ->query(function (Builder $query) {
                        $patterns = config('offenders');

                        $query->where(function ($query) use ($patterns) {
                            foreach ($patterns as $name => $pattern) {
                                $query->orWhereRaw("description REGEXP ?", [$pattern]);
                            }
                        });
                    }),

            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('warn_sender')
                        ->label('Warn Sender')
                        ->icon('heroicon-o-exclamation-circle')
                        ->color('warning')
                        ->action(function ($record) {
                            // Logic to warn the sender
                        }),

                    Tables\Actions\Action::make('warn_receiver')
                        ->label('Warn Receiver')
                        ->icon('heroicon-o-exclamation-circle')
                        ->color('warning')
                        ->action(function ($record) {}),

                    Tables\Actions\Action::make('warn_both')
                        ->label('Warn Both')
                        ->icon('heroicon-o-exclamation-circle')
                        ->color('warning')
                        ->action(function ($record) {}),

                    Tables\Actions\Action::make('freeze_sender')
                        ->label('Freeze Sender')
                        ->icon('heroicon-o-lock-closed')
                        ->color('danger')
                        ->action(function ($record) {}),

                    Tables\Actions\Action::make('freeze_receiver')
                        ->label('Freeze Receiver')
                        ->icon('heroicon-o-lock-closed')
                        ->color('danger')
                        ->action(function ($record) {}),

                    Tables\Actions\Action::make('freeze_both')
                        ->label('Freeze Both')
                        ->icon('heroicon-o-lock-closed')
                        ->color('danger')
                        ->action(function ($record) {}),
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
            'index' => Pages\ListProposals::route('/'),
            'create' => Pages\CreateProposal::route('/create'),
            'edit' => Pages\EditProposal::route('/{record}/edit'),
        ];
    }
}
