<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Project;
use Filament\Forms\Form;
use App\Models\Milestone;
use Filament\Tables\Table;
use App\Models\Requirement;
use App\Enums\ActivityStatus;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\MilestoneResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\MilestoneResource\RelationManagers;

class MilestoneResource extends Resource
{
    protected static ?string $model = Milestone::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Projects';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
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
                Forms\Components\Select::make('project_id')
                    ->label('Project')
                    ->relationship('project', 'name')
                    ->preload()
                    ->required()
                    ->reactive() // Make this field reactive
                    ->afterStateUpdated(function ($state, callable $set) {
                        $requirements = Requirement::where('project_id', $state)->pluck('title', 'id');
                        $set('requirement_id', null); // Reset the requirement_id field
                        $set('requirements', $requirements); // Update the requirements list
                    })
                    ->default(fn ($record) => $record->project_id ?? request()->get('project_id')),

                Forms\Components\Select::make('requirement_id')
                    ->label('Requirement')
                    ->options(fn (callable $get) => $get('requirements') ?? [])
                    ->preload()
                    ->required()
                    ->default(fn ($record) => $record->requirement_id ?? request()->get('requirement_id')),
                Forms\Components\Select::make('status')
                    ->required()
                    ->preload()
                    ->options(ActivityStatus::class)->default('review')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
            Tables\Columns\TextColumn::make('project.name')
                ->sortable(),
            // Tables\Columns\TextColumn::make('requirement.title')
            //     ->sortable(),
                Tables\Columns\TextColumn::make('title')
                ->sortable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('due_date')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_date')
                    ->date('d/m/Y')
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
            ->defaultSort('created_at', 'desc')
            ->defaultGroup('requirement.title')
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([ 
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\Action::make('Mark Completed')
                        ->requiresConfirmation()
                        ->icon('heroicon-o-check-badge')
                        ->hidden(fn (Milestone $record) => ($record->status === 'Completed'))
                        ->action(fn (Milestone $record) => $record->update(['status' => 'completed'])),
                ])
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
            'index' => Pages\ListMilestones::route('/'),
            'create' => Pages\CreateMilestone::route('/create'),
            'edit' => Pages\EditMilestone::route('/{record}/edit'),
        ];
    }
}
