<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityLogResource\Pages;
use App\Filament\Resources\ActivityLogResource\RelationManagers;
use App\Models\ActivityLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ActivityLogResource extends Resource
{
    protected static ?string $model = ActivityLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-circle-stack';

    protected static ?string $navigationGroup = 'Monitoring & Management';

    protected static ?string $navigationLabel = 'Activity LOGs';

    protected static ?string $pluralModelLabel = 'Activity Logs';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                TextColumn::make('id')->sortable()->toggleable(),

                TextColumn::make('event')->label('Event')->searchable()->sortable()->toggleable(),

                TextColumn::make('log_name')->label('Log Name')->sortable()->searchable(),

                TextColumn::make('description')->label('Description')->sortable()->searchable(),

                TextColumn::make('subject_type')->label('Subject Type')->searchable(),

                TextColumn::make('properties')
                    ->label('Changes')
                    ->formatStateUsing(function ($state) {
                        $properties = json_decode($state, true);

                        $changes = [];
                        if (isset($properties['attributes']) && isset($properties['old'])) {
                            foreach ($properties['attributes'] as $key => $newValue) {
                                $oldValue = $properties['old'][$key] ?? 'N/A';
                                $changes[] = "$key: $oldValue -> $newValue";
                            }
                        }

                        // Join changes with line breaks
                        return implode('<br>', $changes);
                    })
                    ->html() // This enables HTML rendering for the column
                    ->searchable()
                    ->sortable(),

                TextColumn::make('subject_id')->label('Subject ID')->toggleable(),

                TextColumn::make('created_at')->label('Created At')->dateTime()->sortable()->toggleable()->searchable(),
            ])
            ->filters([
                //
            ])->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListActivityLogs::route('/'),
        ];
    }
}
