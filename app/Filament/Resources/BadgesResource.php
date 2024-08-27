<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BadgesResource\Pages;
use App\Filament\Resources\BadgesResource\RelationManagers;
use App\Models\Badges;
use Filament\Forms;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\MarkdownEditor;
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
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BadgesResource extends Resource
{
    protected static ?string $model = Badges::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Content Management - CM -';

    protected static ?string $navigationLabel = 'Badges & achievements';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //

                Group::make()
                    ->schema([

                        Section::make()->schema([


                            TextInput::make('name_en')
                                ->label('Name EN')
                                ->helperText('Badge English name')
                                ->required()
                                ->minValue(2)
                                ->maxValue(50),

                            TextInput::make('name_ar')
                                ->label('Name AR')
                                ->helperText('Badge Arabic name')
                                ->required()
                                ->minValue(2)
                                ->maxValue(50),

                            MarkdownEditor::make('description_en')
                                ->label('Description EN')
                                ->helperText('Badge English description')
                                ->required()
                                ->columnSpanFull(),

                            MarkdownEditor::make('description_ar')
                                ->label('Description AR')
                                ->helperText('Badge Arabic description')
                                ->required()
                                ->columnSpanFull(),

                        ])->columns(2),
                    ]),

                Group::make()->schema([
                    Section::make('Visibility')->schema([


                        Select::make('status')
                            ->label('Badge status')
                            ->options([
                                'active' => 'Active',
                                'inactive' => 'Inactive',
                            ])
                            ->required()
                            ->in(['active', 'inactive']),

                    ]),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                TextColumn::make('name_en')
                    ->label('Name EN')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('name_ar')
                    ->label('Name AR')
                    ->sortable()
                    ->searchable(),

                IconColumn::make('status')
                    ->label('Badge status')
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->getStateUsing(fn($record) => $record->status === 'active'),

                TextColumn::make('description_en')
                    ->label('Description EN')
                    ->sortable()
                    ->markdown()
                    ->searchable(),

                TextColumn::make('description_ar')
                    ->label('Description AR')
                    ->markdown()
                    ->sortable()
                    ->searchable(),

                TextColumn::make('created_at')->sortable()->searchable()->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')->sortable()->searchable()->toggleable(isToggledHiddenByDefault: true),

            ])
            ->filters([
                //

                SelectFilter::make('status')
                    ->label('Badge status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                    ]),
            ])
            ->actions([

                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\DeleteAction::make(),

                    Tables\Actions\Action::make('activate_status')
                        ->label('De-activate Badge')
                        ->color('danger')
                        ->icon('heroicon-o-lock-closed')
                        ->requiresConfirmation()
                        ->action(function (Badges $record) {
                            $record->status = 'inactive';
                            $record->save();

                            Notification::make()
                                ->title('De-active Badge')
                                ->success()
                                ->body("Badge {$record->name_en} has been de-activated.")
                                ->send();
                        })
                        ->visible(fn(Badges $record) => $record->status == 'active'),

                    Tables\Actions\Action::make('deactivate_status')
                        ->label('Activate Badge')
                        ->color('success')
                        ->icon('heroicon-o-lock-open')
                        ->action(function (Badges $record) {
                            $record->status = 'active';
                            $record->save();

                            Notification::make()
                                ->title('Activate Badge')
                                ->success()
                                ->body("Badge {$record->name_en} has been activated.")
                                ->send();
                        })
                        ->requiresConfirmation()
                        ->visible(fn(Badges $record) => $record->status != 'active'),

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
            'index' => Pages\ListBadges::route('/'),
            'create' => Pages\CreateBadges::route('/create'),
            'edit' => Pages\EditBadges::route('/{record}/edit'),
        ];
    }
}
