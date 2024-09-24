<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deliverable extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'project_id', 'requirement_id', 'milestone_id'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function requirement()
    {
        return $this->belongsTo(Requirement::class);
    }

    public function milestone()
    {
        return $this->belongsTo(Milestone::class);
    }
    
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
