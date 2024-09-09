<?php

namespace App\Filament\Resources\RequirementResource\Pages;

use Filament\Actions;
use App\Models\Requirement;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\RequirementResource;

class ListRequirements extends ListRecords
{
    protected static string $resource = RequirementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        $tabs = [];
    
        $tabs[] = Tab::make('All Requirements')
            // Add badge to the tab
            ->badge(Requirement::count());
            // No need to modify the query as we want to show all tasks
        $tabs[] = Tab::make('In Progress')
            // Add badge to the tab
            ->badge(Requirement::where('status', 'in progress')->count())
            // Modify the query only to show completed tasks
            ->modifyQueryUsing(function ($query) {
                return $query->where('status', 'in progress');
            });
         $tabs[] = Tab::make('Review')
            // Add badge to the tab
            ->badge(Requirement::where('status', 'review')->count())
            // Modify the query only to show completed tasks
            ->modifyQueryUsing(function ($query) {
                return $query->where('status', 'review');
            });
        return $tabs;
    }
    
}
