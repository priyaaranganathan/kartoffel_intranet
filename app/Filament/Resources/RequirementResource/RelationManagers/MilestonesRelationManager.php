<?php

namespace App\Filament\Resources\RequirementResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use App\Models\Project;
use Filament\Forms\Form;
use App\Models\Milestone;
use Filament\Tables\Table;
use App\Models\Requirement;
use App\Enums\ActivityStatus;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class MilestonesRelationManager extends RelationManager
{
    protected static string $relationship = 'milestones';

    public function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\Select::make('project_id')
                ->options(Project::all()->pluck('name', 'id')->toArray())
                ->default(fn (?Milestone $record) => $record ? $record->project_id : static::getOwnerRecord()->project_id)
                ->disabled(),

                Forms\Components\Select::make('requirement_id')
                ->options(Requirement::all()->pluck('title', 'id')->toArray())
                ->default(fn (?Milestone $record) => $record ? $record->requirement_id : static::getOwnerRecord()->id)
                ->disabled(),
                Forms\Components\Hidden::make('project_id')
                ->default(fn (?Milestone $record) => $record ? $record->project_id : static::getOwnerRecord()->project_id),

                Forms\Components\Hidden::make('requirement_id')
                ->default(fn (?Milestone $record) => $record ? $record->requirement_id : static::getOwnerRecord()->id),


                Forms\Components\Textarea::make('title')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\DatePicker::make('start_date')
                    ->required(),
                Forms\Components\DatePicker::make('due_date')
                    ->required(),
                Forms\Components\DatePicker::make('payment_date')
                    ->required(),
                Forms\Components\TextInput::make('payment_amount')
                    ->required()
                    ->numeric(),
                // Forms\Components\TextInput::make('requirement_id')
                //     ->numeric(),
                Forms\Components\Select::make('status')
                    ->required()
                    ->preload()
                    ->options(ActivityStatus::class)->default('not started')
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
               
                Tables\Columns\TextColumn::make('title')
                ->sortable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->date("d/m/y")
                    ->sortable(),
                Tables\Columns\TextColumn::make('due_date')
                    ->since()
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_date')
                    ->since()
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_amount')
                    ->numeric()
                    ->sortable()
                    ->summarize(Tables\Columns\Summarizers\Sum::make()
                        ->formatStateUsing(fn ($state) => 'INR ' . number_format($state))
                    ),
                Tables\Columns\TextColumn::make('status')
                    ->badge(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([                
                Tables\Actions\ActionGroup::make([ 
                    Tables\Actions\Action::make('Mark Completed')
                        ->requiresConfirmation()
                        ->icon('heroicon-o-check-badge')
                        ->hidden(fn (Milestone $record) => ($record->status === 'Completed'))
                        ->action(fn (Milestone $record) => $record->update(['status' => 'completed'])),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    // public static function store(Milestone $milestone): void
    // {
    //     Log::info('Creating milestone with data:', request()->all());
    //     dd(request()->all());
        
    //     $milestone->project_id = request()->input('project_id', $milestone->project_id);
    //     $milestone->requirement_id = request()->input('requirement_id', $milestone->requirement_id);
    // }
}
