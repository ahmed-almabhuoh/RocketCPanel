<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ApiIntegrationResource\Pages;
use App\Filament\Resources\ApiIntegrationResource\RelationManagers;
use App\Models\ApiIntegration;
use Filament\Forms;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ApiIntegrationResource extends Resource
{
    protected static ?string $model = ApiIntegration::class;

    protected static ?string $navigationIcon = 'heroicon-o-code-bracket';

    protected static ?string $navigationGroup = 'Integrations & 3TH Packages';

    protected static ?string $navigationLabel = 'API Keys & Applications';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Group::make()->schema([
                    Section::make('General')->schema([

                        TextInput::make('project_name')
                            ->label('Project name')
                            ->required()
                            ->unique()
                            ->columnSpan(1),

                        TextInput::make('project_version')
                            ->label('Project version')
                            ->nullable()
                            ->columnSpan(1),

                        MarkdownEditor::make('project_description')
                            ->label('Project description')
                            ->nullable()
                            ->columnSpan('full'),

                    ])->columns(2),
                ]),


                Group::make()->schema([

                    Section::make('API Key')->schema([

                        TextInput::make('public_key')
                            ->label('Public key')
                            ->required()
                            ->unique()
                            ->default(fn() => (string) Str::uuid()),

                        TextInput::make('secret_key')
                            ->label('Secret key')
                            ->required()
                            ->dehydrateStateUsing(fn($state) => Hash::make($state)) // Hash the key before saving
                            ->visibleOn(['create'])
                            ->helperText('The secret key will be hashed before saving.')
                            ->default(fn() => Str::random(64)),

                    ]),

                    Section::make('API Key Configuration')->schema([

                        Toggle::make('is_limited_usage')
                            ->label('Limited usage?')
                            ->helperText('Make API key is limited to use')
                            ->reactive(),

                        TextInput::make('limited_usage_times')
                            ->label('Usage times')
                            ->nullable()
                            ->numeric()
                            ->minValue(100)
                            ->visible(fn($get) => $get('is_limited_usage')),

                    ]),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                TextColumn::make('project_name')
                    ->label('Project name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('project_version')
                    ->label('Project version')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('project_description')
                    ->markdown()
                    ->label('Project description')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('public_key')
                    ->label('Public key')
                    ->searchable()
                    ->sortable(),

                IconColumn::make('is_limited_usage')
                    ->label('Limited?')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('limited_usage_times')
                    ->label('Limited usages')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable()
                    ->sortable(),

                TextColumn::make('usage_times')
                    ->label('Used times')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Established at')
                    ->date()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable()
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('Last usage at')
                    ->dateTime()
                    ->toggleable()
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //

                Filter::make('is_limited_usage')
                    ->label('Limited APIs')
                    ->toggle()
                    ->default(false)
                    ->query(fn($query) => $query->where('is_limited_usage', true)),


                Filter::make('expired')
                    ->label('Expired APIs')
                    ->toggle()
                    ->default(false)
                    ->query(function ($query) {
                        return $query->where('is_limited_usage', true)
                            ->whereColumn('limited_usage_times', '<=', 'usage_times');
                    }),
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
            'index' => Pages\ListApiIntegrations::route('/'),
            'create' => Pages\CreateApiIntegration::route('/create'),
            'edit' => Pages\EditApiIntegration::route('/{record}/edit'),
        ];
    }
}
