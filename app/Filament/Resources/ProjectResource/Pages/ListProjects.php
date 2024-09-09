<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use Filament\Resources\Components\Tab;
use App\Filament\Resources\ProjectResource;
use App\Models\Project;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProjects extends ListRecords
{
    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        $tabs = [];
    
        $tabs[] = Tab::make('All Projects')
            // Add badge to the tab
            ->badge(Project::count());
            // No need to modify the query as we want to show all tasks
    
        return $tabs;
    }
    
}
