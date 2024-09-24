<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Task;
use Filament\Tables;
use App\Models\Employee;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Deliverable;
use App\Models\Requirement;
use Illuminate\Support\Str;
use App\Models\TaskCategory;
use App\Enums\ActivityStatus;
use Filament\Resources\Resource;
use Forms\Components\ViewComponent;
use Filament\Forms\Components\Select;
use App\Tables\Columns\ProgressColumn;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\TaskResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\TaskResource\RelationManagers;

class TaskResource extends Resource
{
    protected static ?string $model = Task::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Projects';
protected static ?int $navigationSort = 4;

    // protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('code')
                    ->required()
                    ->hidden()
                    ->disabled() // Disable it to prevent manual editing, only auto-generated
                    ->default(fn ($record) => $record->code ?? 'T-' . strtoupper(Str::random(12))),
                Forms\Components\TextInput::make('title')->required(),
                Forms\Components\Textarea::make('description')->nullable(),
                Forms\Components\Select::make('deliverable_id')
                ->label('Deliverable')
                ->options(Deliverable::pluck('name', 'id'))
                ->reactive()
                ->afterStateUpdated(function (callable $set, $state) {
                    $deliverable = Deliverable::find($state);
                    if ($deliverable) {
                        $set('project_id', $deliverable->project_id);
                        $set('requirement_id', $deliverable->requirement_id);
                        $set('milestone_id', $deliverable->milestone_id);
                    } else {
                        $set('project_id', null);
                        $set('requirement_id', null);
                        $set('milestone_id', null);
                    }
                }),
            Forms\Components\Select::make('project_id')
                ->label('Project')
                ->relationship('project', 'name')
                ->disabled()
                ->required(),
            Forms\Components\Hidden::make('project_id'),
            Forms\Components\Select::make('requirement_id')
                ->relationship('requirement', 'title')
                ->disabled()
                ->nullable(),
                Forms\Components\Hidden::make('requirement_id'),
            Forms\Components\Select::make('milestone_id')
                ->relationship('milestone', 'title')
                ->disabled()
                ->nullable(),
            Forms\Components\Hidden::make('milestone_id'),
                Forms\Components\Select::make('status')
                    ->required()
                    ->preload()
                    ->options(ActivityStatus::class)->default('not started'),
                Forms\Components\DatePicker::make('start_date')->nullable(),
                Forms\Components\DatePicker::make('end_date')->nullable(),
                Repeater::make('tasks')
                    ->relationship('assignments')
                    ->schema([
                        Forms\Components\Select::make('task_category_id')
                            ->options(TaskCategory::all()->pluck('name', 'id')->toArray())
                            ->searchable()
                            ->required(),
                        Forms\Components\Select::make('employee_id')
                            ->options(Employee::all()->pluck('first_name', 'id')->toArray())
                            ->searchable()
                            ->required(),
                        Forms\Components\TextInput::make('efforts')
                            // ->required()
                            ->numeric()
                            ->default(0)
                            ->minValue(0),
                        Forms\Components\DatePicker::make('start_date')
                            ->required(),
                        Forms\Components\DatePicker::make('due_date')
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->required()
                            ->preload()
                            ->options(ActivityStatus::class)->default('not started')
                    ])
                    ->columns(3) // Adjust the number of columns as needed
                    ->required()
                    ->orderColumn('due_date')
                    ->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('project.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('requirement.title')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->date("d/m/Y")
                    ->sortable(),
                Tables\Columns\TextColumn::make('due_date')
                    ->date("d/m/Y")
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')->sortable()->searchable(),
                // Tables\Columns\TextColumn::make('type')->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge(),
                Tables\Columns\TextColumn::make('TotalEfforts')
                    ->Label('Estimates'),
                Tables\Columns\TextColumn::make('TotalAssignments')
                    ->Label('Sub Tasks'),
                    // ProgressColumn::make('progress'), 
                // Tables\Columns\TextColumn::make('employees.name')->label('Assigned Employees'),
            ])
            ->defaultSort('created_at', 'desc')
            ->defaultGroup(group: 'deliverable.name')
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListTasks::route('/'),
            'create' => Pages\CreateTask::route('/create'),
            'edit' => Pages\EditTask::route('/{record}/edit'),
        ];
    }
}
