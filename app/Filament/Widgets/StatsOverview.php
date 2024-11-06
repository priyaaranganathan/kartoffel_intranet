<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Support\Carbon;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StatsOverview extends BaseWidget
{
    use InteractsWithPageFilters;

    protected $project_stats;
    protected $task_stats;

    protected $start_date, $end_date;

    protected function get_project_counts(){
        $total_count = Project::whereDate('created_at', '>=', $this->start_date)
                        ->whereDate('created_at', '<=', $this->end_date)
                        ->count();
        $active_count = Project::where('status','active')
                        ->whereDate('created_at', '>=', $this->start_date)
                        ->whereDate('created_at', '<=', $this->end_date)
                        ->count();
        $this->project_stats['total_projects'] = $total_count;
        $this->project_stats['active_projects']= $active_count;
        // return $this->arr_projects;
    }

    protected function get_task_counts(){
        $total_count = Task::whereDate('created_at', '>=', $this->start_date)
                        ->whereDate('created_at', '<=', $this->end_date)
                        ->count();
        // $active_count = Task::where('status','active')
        //                 ->whereDate('created_at', '>=', $this->start_date)
        //                 ->whereDate('created_at', '<=', $this->end_date)
        //                 ->count();
        $this->task_stats['total_projects'] = $total_count;
        // $this->task_stats['active_projects']= $active_count;
    }

    protected function getStats(): array
    {
        $start_date = $this->filters['start_date'];
        $end_date = $this->filters['end_date'];

        $this->start_date = Carbon::parse($start_date)->format('Y-m-d');
        $this->end_date = Carbon::parse($end_date)->format('Y-m-d');
        // get all project related counts
        $this->get_project_counts();

        // get all task related counts
        $this->get_task_counts();
       
        return [
            Stat::make('Total Projects',  $this->project_stats['total_projects']),
            Stat::make('Active Projects', $this->project_stats['active_projects'])
                ->descriptionColor('success')
                ->description('Projects currently active'),
        ];
    }
}
