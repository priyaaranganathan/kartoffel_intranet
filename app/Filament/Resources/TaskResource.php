<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Task;
use Filament\Tables;
use App\Models\Employee;
use Filament\Forms\Form;
use Filament\Tables\Table;
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

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('total_efforts')
                    ->label('Total Efforts')
                    ->default(fn ($record) => $record ? $record->total_efforts : 0)
                    ->disabled() // Make it read-only
                    ->numeric(),
                    // ->columnSpanFull(),
                Forms\Components\TextInput::make('code')
                    ->required()
                    // ->hidden()
                    ->disabled() // Disable it to prevent manual editing, only auto-generated
                    ->default(fn ($record) => $record->code ?? 'T-' . strtoupper(Str::random(12))),
                Forms\Components\TextInput::make('title')->required(),
                Forms\Components\Textarea::make('description')->nullable(),
                // Forms\Components\Select::make('project_id')
                //     ->label('Project')
                //     ->relationship('project', 'name')
                //     ->preload()
                //     ->required()
                //     ->reactive() // Make this field reactive
                //     ->afterStateUpdated(function ($state, callable $set) {
                //         $requirements = Requirement::where('project_id', $state)->pluck('title', 'id');
                //         $set('requirement_id', null); // Reset the requirement_id field
                //         $set('requirement', $requirements); // Update the requirements list
                //     })
                //     ->default(fn ($record) => $record->project_id ?? request()->get('project_id')),

                // Forms\Components\Select::make('requirement_id')
                //     ->label('Requirement')
                //     ->options(fn (callable $get) => $get('requirement') ?? [])
                //     ->preload()
                //     ->required()
                //     ->default(fn ($record) => $record->requirement_id ?? request()->get('requirement_id')),
                Forms\Components\Select::make('project_id')
                    ->label('Project')
                    ->relationship('project', 'name')
                    ->preload()
                    ->required()
                    ->reactive() // Make this field reactive
                    ->afterStateUpdated(function ($state, callable $set) {
                        // Fetch the requirements based on the selected project when project_id is changed
                        $requirements = Requirement::where('project_id', $state)->pluck('title', 'id');

                        // Reset requirement field
                        $set('requirement_id', null);

                        // Update the requirement options list dynamically
                        $set('requirement_options', $requirements);
                    })
                    ->default(fn ($record) => $record->project_id ?? request()->get('project_id')),

                Forms\Components\Select::make('requirement_id')
                    ->label('Requirement')
                    ->options(function (callable $get, $state, $set) {
                        // Preload requirements in edit mode when the form is loaded
                        $projectId = $get('project_id');
                        if ($projectId) {
                            $requirements = Requirement::where('project_id', $projectId)->pluck('title', 'id');
                            $set('requirement_options', $requirements); // Ensure the options are set
                        }

                        // Return the dynamically populated requirement options
                        return $get('requirement_options') ?? [];
                    })
                    ->preload()
                    ->required()
                    ->default(fn ($record) => $record->requirement_id ?? request()->get('requirement_id')),
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
                            ->required()
                            ->numeric()
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
                Tables\Columns\TextColumn::make('type')
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
                Tables\Columns\TextColumn::make('type')->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge(),
                Tables\Columns\TextColumn::make('TotalEfforts')
                    ->Label('Estimates'),
                Tables\Columns\TextColumn::make('TotalAssignments')
                    ->Label('Sub Tasks'),
                    // ProgressColumn::make('progress'), 
                // Tables\Columns\TextColumn::make('employees.name')->label('Assigned Employees'),
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
            'index' => Pages\ListTasks::route('/'),
            'create' => Pages\CreateTask::route('/create'),
            'edit' => Pages\EditTask::route('/{record}/edit'),
        ];
    }
}
