<?php

namespace App\Filament\Resources\RequirementResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Milestone;
use Filament\Tables\Table;
use App\Models\Requirement;
use App\Enums\ActivityStatus;
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
                // Forms\Components\Select::make('project_id')
                //     ->relationship('project', 'name')
                //     ->reactive()
                //     ->default(fn ($record) => $record?->project_id ?? static::getOwnerRecord()->project_id)
                //     ->disabled()
                //     ->afterStateUpdated(fn ($state, callable $set) => $set('requirement_id', null)) // Reset requirement_id when project_id changes
                //     ->required(),
                // Forms\Components\Select::make('requirement_id') 
                //     ->relationship('requirement', 'title')
                //     ->default(fn ($record) => $record?->requirement_id ?? static::getOwnerRecord()->id)
                //     ->disabled()
                //     ->required(),
                Forms\Components\Select::make('project_id')
                    ->relationship('project', 'name')
                    ->reactive()
                    ->default(fn ($record) => $record?->project_id ?? static::getOwnerRecord()->project_id ) // Set default value
                    ->disabled() // Make the field non-editable
                    ->required(), // Ensure the field is required

                // Requirement ID Field
                Forms\Components\Select::make('requirement_id')
                    ->options(fn (callable $get) => 
                        Requirement::where('project_id', $get('project_id'))->pluck('title', 'id')
                    )
                    ->default(static::getOwnerRecord()->id)
                    ->disabled() // Disable if no project is selected
                    ->required(), // Ensure the field is required
                Forms\Components\Textarea::make('title')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\DateTimePicker::make('start_date')
                    ->required(),
                Forms\Components\DateTimePicker::make('due_date')
                    ->required(),
                Forms\Components\DateTimePicker::make('payment_date')
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
}
