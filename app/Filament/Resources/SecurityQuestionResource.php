<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SecurityQuestionResource\Pages;
use App\Filament\Resources\SecurityQuestionResource\RelationManagers;
use App\Models\SecurityQuestion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SecurityQuestionResource extends Resource
{
    protected static ?string $model = SecurityQuestion::class;

    protected static ?string $navigationIcon = 'heroicon-o-question-mark-circle';

    protected static ?string $navigationGroup = 'Security Configuration - SC -';

    protected static ?string $navigationLabel = 'Security Questions';

    protected static ?int $navigationSort = 2;

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

                TextColumn::make('question')
                    ->label('Question')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('user.fname')
                    ->label('For user')
                    ->sortable()
                    ->formatStateUsing(fn($record) => $record->user->fname . ' ' . $record->user->lname)
                    ->searchable(),

                TextColumn::make('created_at')
                    ->label('Added at')
                    ->dateTime()
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Updated at')
                    ->dateTime()
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListSecurityQuestions::route('/'),
            'create' => Pages\CreateSecurityQuestion::route('/create'),
            'edit' => Pages\EditSecurityQuestion::route('/{record}/edit'),
        ];
    }
}
