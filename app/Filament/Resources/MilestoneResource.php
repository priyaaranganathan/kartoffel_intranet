<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Milestone;
use Filament\Tables\Table;
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
                Forms\Components\TextInput::make('project_id')
                    ->numeric(),
                Forms\Components\TextInput::make('requirement_id')
                    ->numeric(),
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
                // Tables\Columns\TextColumn::make('project.name')
                // ->sortable(),
            Tables\Columns\TextColumn::make('requirement.title')
                ->sortable(),
                Tables\Columns\TextColumn::make('title')
                ->sortable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('due_date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_date')
                    ->dateTime()
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
            ->defaultGroup('project.name')
            ->filters([
                //
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
            'index' => Pages\ListMilestones::route('/'),
            'create' => Pages\CreateMilestone::route('/create'),
            'edit' => Pages\EditMilestone::route('/{record}/edit'),
        ];
    }

    // public static function store(Milestone $milestone): void
    // {
    //     Log::info('Creating milestone with data:', request()->all());
    //     dd(request()->all());

    //     $milestone->project_id = request()->input('project_id', $milestone->project_id);
    //     $milestone->requirement_id = request()->input('requirement_id', $milestone->requirement_id);
        
    //     $milestone->save();  // Ensure you save the model
    // }

   

}
