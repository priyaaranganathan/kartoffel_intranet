<?php

namespace App\Models;

use App\Enums\RecordStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Project extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'description','start_date','end_date','client_id','total_cost','status','project_leader_id'];

    protected $casts = [
        'status' =>  RecordStatus::class,
    ];


    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    // public function requirements(): HasMany 
    // {
    //     return $this->hasMany(ProjectRequirement::class);
    // }

    public function requirements(): HasMany 
    {
        return $this->hasMany(Requirement::class);
    }

    public function milestones(): HasMany 
    {
        return $this->hasMany(Milestone::class);
    }

    public function deliverables()
    {
        return $this->hasMany(Deliverable::class);
    }

    public function projectLeader()
    {
        return $this->belongsTo(Employee::class, 'project_leader_id');
    }

    public function getProjectLeaderFullNameAttribute()
    {
        return $this->projectLeader ? $this->projectLeader->full_name : 'N/A';
    }

    public function teamMembers()
    {
        return $this->belongsToMany(Employee::class, 'project_user', 'project_id', 'employee_id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
