<?php

namespace App\Filament\AssetSurvey\Resources;

use App\Filament\AssetSurvey\Resources\AssetSurveyResource\Pages;
use App\Models\Asset;
use App\Models\AssetSurvey;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class AssetSurveyResource extends Resource
{
    protected static ?string $model = AssetSurvey::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationLabel = 'Asset Surveys';

    protected static ?int $navigationSort = 2;

    public static function getEloquentQuery(): Builder
    {
        // Show all asset surveys without tenant filtering
        return parent::getEloquentQuery();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Survey Information')
                    ->schema([
                        Forms\Components\Select::make('asset_id')
                            ->label('Asset')
                            ->required()
                            ->relationship('asset', 'name')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->required(),
                                Forms\Components\TextInput::make('asset_code')
                                    ->required(),
                            ]),
                        Forms\Components\DatePicker::make('survey_date')
                            ->required()
                            ->default(now()),
                        Forms\Components\Select::make('survey_type')
                            ->required()
                            ->options([
                                'routine' => 'Routine Inspection',
                                'maintenance' => 'Maintenance Check',
                                'condition_assessment' => 'Condition Assessment',
                                'location_verification' => 'Location Verification',
                                'disposal_assessment' => 'Disposal Assessment',
                            ]),
                        Forms\Components\Select::make('status')
                            ->required()
                            ->options([
                                'pending' => 'Pending',
                                'in_progress' => 'In Progress',
                                'completed' => 'Completed',
                                'cancelled' => 'Cancelled',
                            ])
                            ->default('pending'),
                    ])->columns(2),

                Forms\Components\Section::make('Assessment')
                    ->schema([
                        Forms\Components\Select::make('condition_assessment')
                            ->required()
                            ->options([
                                'excellent' => 'Excellent',
                                'good' => 'Good',
                                'fair' => 'Fair',
                                'poor' => 'Poor',
                                'critical' => 'Critical',
                            ]),
                        Forms\Components\Select::make('physical_condition')
                            ->required()
                            ->options([
                                'intact' => 'Intact',
                                'minor_wear' => 'Minor Wear',
                                'moderate_wear' => 'Moderate Wear',
                                'significant_wear' => 'Significant Wear',
                                'damaged' => 'Damaged',
                            ]),
                        Forms\Components\Select::make('functional_status')
                            ->required()
                            ->options([
                                'fully_functional' => 'Fully Functional',
                                'partially_functional' => 'Partially Functional',
                                'non_functional' => 'Non-Functional',
                                'needs_repair' => 'Needs Repair',
                            ]),
                    ])->columns(3),

                Forms\Components\Section::make('Maintenance')
                    ->schema([
                        Forms\Components\Toggle::make('maintenance_required')
                            ->label('Maintenance Required')
                            ->reactive(),
                        Forms\Components\Select::make('maintenance_priority')
                            ->options([
                                'low' => 'Low',
                                'medium' => 'Medium',
                                'high' => 'High',
                                'urgent' => 'Urgent',
                            ])
                            ->visible(fn (Forms\Get $get) => $get('maintenance_required')),
                        Forms\Components\TextInput::make('estimated_repair_cost')
                            ->numeric()
                            ->prefix('Rp')
                            ->visible(fn (Forms\Get $get) => $get('maintenance_required')),
                        Forms\Components\DatePicker::make('next_survey_date'),
                    ])->columns(2),

                Forms\Components\Section::make('REKANUSA Specific Assessment')
                    ->schema([
                        Forms\Components\Toggle::make('is_available')
                            ->label('Available for Use')
                            ->default(true),
                        Forms\Components\Toggle::make('is_calibrated')
                            ->label('Calibrated (if applicable)')
                            ->default(false),
                        Forms\Components\Toggle::make('has_documentation')
                            ->label('Has Complete Documentation')
                            ->default(true),
                        Forms\Components\Toggle::make('safety_compliant')
                            ->label('Safety Compliant')
                            ->default(true),
                        Forms\Components\Select::make('usage_frequency')
                            ->label('Usage Frequency')
                            ->options([
                                'daily' => 'Daily',
                                'weekly' => 'Weekly',
                                'monthly' => 'Monthly',
                                'quarterly' => 'Quarterly',
                                'rarely' => 'Rarely Used',
                                'not_used' => 'Not Used',
                            ]),
                        Forms\Components\TextInput::make('current_location_detail')
                            ->label('Current Location Detail')
                            ->maxLength(255),
                    ])->columns(2),

                Forms\Components\Section::make('Location & Notes')
                    ->schema([
                        Forms\Components\Toggle::make('location_verified')
                            ->label('Location Verified')
                            ->default(true),
                        Forms\Components\Textarea::make('location_notes')
                            ->rows(2),
                        Forms\Components\Textarea::make('surveyor_notes')
                            ->rows(3),
                        Forms\Components\Repeater::make('recommendations')
                            ->schema([
                                Forms\Components\TextInput::make('recommendation')
                                    ->required(),
                                Forms\Components\Select::make('priority')
                                    ->options([
                                        'low' => 'Low',
                                        'medium' => 'Medium',
                                        'high' => 'High',
                                    ])
                                    ->required(),
                            ])
                            ->columns(2)
                            ->collapsible(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('asset.name')
                    ->label('Asset')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('asset.asset_code')
                    ->label('Asset Code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('asset.tool_category')
                    ->label('Tool Category')
                    ->badge()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('asset.tool_name')
                    ->label('Tool Name')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('survey_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('survey_type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'routine' => 'primary',
                        'maintenance' => 'warning',
                        'condition_assessment' => 'info',
                        'location_verification' => 'success',
                        'disposal_assessment' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('condition_assessment')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'excellent' => 'success',
                        'good' => 'primary',
                        'fair' => 'warning',
                        'poor' => 'danger',
                        'critical' => 'danger',
                    }),
                Tables\Columns\IconColumn::make('maintenance_required')
                    ->boolean(),
                Tables\Columns\TextColumn::make('maintenance_priority')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'low' => 'success',
                        'medium' => 'warning',
                        'high' => 'danger',
                        'urgent' => 'danger',
                    })
                    ->visible(fn ($record) => $record && $record->maintenance_required),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'completed' => 'success',
                        'in_progress' => 'warning',
                        'pending' => 'info',
                        'cancelled' => 'danger',
                    }),
                Tables\Columns\IconColumn::make('is_available')
                    ->label('Available')
                    ->boolean()
                    ->toggleable(),
                Tables\Columns\IconColumn::make('safety_compliant')
                    ->label('Safety Compliant')
                    ->boolean()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('usage_frequency')
                    ->label('Usage Frequency')
                    ->badge()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Surveyor')
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('survey_type'),
                Tables\Filters\SelectFilter::make('condition_assessment'),
                Tables\Filters\SelectFilter::make('status'),
                Tables\Filters\SelectFilter::make('asset.tool_category')
                    ->label('Tool Category (REKANUSA)')
                    ->relationship('asset', 'tool_category')
                    ->options([
                        'safety' => 'Alat Pelindung Diri/Safety First',
                        'documentation' => 'Alat Dokumentasi',
                        'support' => 'Support Tools',
                        'architecture' => 'Alat Survey Arsitektur',
                        'civil' => 'Alat Sipil',
                        'mep' => 'Alat MEP & Utility',
                    ]),
                Tables\Filters\Filter::make('maintenance_required')
                    ->query(fn (Builder $query): Builder => $query->where('maintenance_required', true)),
                Tables\Filters\Filter::make('available_only')
                    ->label('Available Only')
                    ->query(fn (Builder $query): Builder => $query->where('is_available', true)),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListAssetSurveys::route('/'),
            'create' => Pages\CreateAssetSurvey::route('/create'),
            'view' => Pages\ViewAssetSurvey::route('/{record}'),
            'edit' => Pages\EditAssetSurvey::route('/{record}/edit'),
        ];
    }
}