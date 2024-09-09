<?php

namespace App\Filament\Resources\TaskResource\Pages;

use Filament\Actions;
use App\Filament\Resources\TaskResource;
use App\Models\Task;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
class ListTasks extends ListRecords
{
    protected static string $resource = TaskResource::class;

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
            ->badge(Task::count());
        $tabs[] = Tab::make('In Progress')
            ->badge(Task::where('status', 'in progress')->count())
            ->modifyQueryUsing(function ($query) {
                return $query->where('status', 'in progress');
            });
         $tabs[] = Tab::make('Not Started')
            ->badge(Task::where('status', 'not started')->count())
            ->modifyQueryUsing(function ($query) {
                return $query->where('status', 'not started');
            });
        $tabs[] = Tab::make('Completed')
            ->badge(Task::where('status', 'completed')->count())
            ->modifyQueryUsing(function ($query) {
                return $query->where('status', 'not started');
            });
        return $tabs;
    }
}
