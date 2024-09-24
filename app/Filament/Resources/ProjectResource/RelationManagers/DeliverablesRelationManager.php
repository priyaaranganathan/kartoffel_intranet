<?php

namespace App\Filament\Resources\ProjectResource\RelationManagers;

use App\Filament\Resources\DeliverableResource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DeliverablesRelationManager extends RelationManager
{
    protected static string $relationship = 'deliverables';

    public function form(Form $form): Form
    {
        // return $form
        //     ->schema([
        //         Forms\Components\TextInput::make('name')
        //             ->required()
        //             ->maxLength(255),
        //     ]);
        return DeliverableResource::form($form);
    }

    public function table(Table $table): Table
    {
        return DeliverableResource::table($table);
    }
}
