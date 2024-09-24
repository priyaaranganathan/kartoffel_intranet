<?php

namespace App\Models;

use App\Enums\ActivityStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Requirement extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description','start_date','end_date','project_id','status'];

    protected $casts = [
        'status' =>  ActivityStatus::class,
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function milestones(): HasMany 
    {
        return $this->hasMany(Milestone::class);
    }

    public function deliverables()
    {
        return $this->hasMany(Deliverable::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
