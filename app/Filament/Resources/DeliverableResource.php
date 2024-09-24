<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Project;
use Filament\Forms\Form;
use App\Models\Milestone;
use Filament\Tables\Table;
use App\Models\Deliverable;
use App\Models\Requirement;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\DeliverableResource\Pages;
use App\Filament\Resources\DeliverableResource\RelationManagers;

class DeliverableResource extends Resource
{
    protected static ?string $model = Deliverable::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Projects';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required(),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
                // Forms\Components\TextInput::make('project_id')
                //     ->required()
                //     ->numeric(),
                // Forms\Components\TextInput::make('requirement_id')
                //     ->numeric(),
                // Forms\Components\TextInput::make('milestone_id')
                //     ->numeric()
                // Project selection dropdown
                Forms\Components\Select::make('project_id')
                    ->label('Project')
                    ->options(Project::all()->pluck('name', 'id'))
                    ->required()
                    ->reactive(), // Triggers updates to dependent fields

                // Requirement dropdown (depends on selected project)
                Forms\Components\Select::make('requirement_id')
                    ->label('Requirement')
                    ->options(function (callable $get) {
                        $projectId = $get('project_id');
                        if ($projectId) {
                            return Requirement::where('project_id', $projectId)->pluck('title', 'id');
                        }
                        return [];
                    })
                    ->reactive() // Triggers update based on project_id
                    ->nullable(),

                // Milestone dropdown (depends on selected project)
                Forms\Components\Select::make('milestone_id')
                    ->label('Milestone')
                    ->options(function (callable $get) {
                        $projectId = $get('project_id');
                        $requirementId = $get('requirement_id');

                        if ($requirementId) {
                            // If a requirement is selected, fetch milestones linked to the requirement
                            return Milestone::where('requirement_id', $requirementId)->pluck('title', 'id');
                        } elseif ($projectId) {
                            // If only the project is selected, fetch milestones linked directly to the project
                            return Milestone::where('project_id', $projectId)
                                    ->whereNull('requirement_id')
                                    ->pluck('title', 'id');
                        }

                        return [];
                    })
                    ->reactive()
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('description'),

                Tables\Columns\TextColumn::make('project.name')->label('Project'),
                Tables\Columns\TextColumn::make('requirement.title')->label('Requirement')->sortable(),
                Tables\Columns\TextColumn::make('milestone.title')->label('Milestone')->sortable(),
                Tables\Columns\TextColumn::make('tasks_count')->counts('tasks')
            ])
            ->defaultSort('created_at', 'desc')
            ->defaultGroup('project.name')
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->filters([
                //
            ])
            ->actions([
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
            'index' => Pages\ListDeliverables::route('/'),
            'create' => Pages\CreateDeliverable::route('/create'),
            'edit' => Pages\EditDeliverable::route('/{record}/edit'),
        ];
    }
}
