<?php

namespace App\Filament\Resources\ProjectResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Enums\ActivityStatus;
use App\Filament\Resources\RequirementResource;
use App\Models\Requirement;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class RequirementRelationManager extends RelationManager
{
    protected static string $relationship = 'requirements';

    public function form(Form $form): Form
    {
        return RequirementResource::form($form);
    }

    public function table(Table $table): Table
    {
        return RequirementResource::table($table);
    }
}
