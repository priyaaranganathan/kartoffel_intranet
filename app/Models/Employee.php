<?php

namespace App\Models;

use App\Enums\RecordStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Traits\HasRoles;

class Employee extends Model
{
    use HasFactory;
    use HasRoles;

    protected $fillable = ['first_name', 'last_name','role_id','department_id','reporting_manager_id','email','contact','status'];

    protected $casts = [
        'status' =>  RecordStatus::class,
    ];

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }
    
    // // Method to get the full name of the reporting manager
    // public function getReportingManagerFullName()
    // {
    //     if ($this->reportingManager) {
    //         return $this->reportingManager->first_name . ' ' . $this->reportingManager->last_name;
    //     }
        
    //     return null; // or an empty string, or whatever default value you prefer
    // }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function manager(): HasOne
    {
        return $this->hasOne(Employee::class,'first_name');
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_user', 'user_id', 'project_id');
    }

    public function tasks()
    {
        return $this->belongsToMany(Task::class, 'task_assignments')
                    ->withPivot('task_category_id', 'status')
                    ->withTimestamps();
    }
}
