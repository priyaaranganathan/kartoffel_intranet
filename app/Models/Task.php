<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Task extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'description',
        'requirement_id',
        'project_id',
        'due_date',
        'start_date',
        'status',
        'code'
    ];

    protected static function boot()
    {
        parent::boot();

        // Generate code only for new records
        static::creating(function ($task) {
            if (empty($task->code)) {
                $task->code = $task->generateUniqueCode();
            }
        });
    }

    protected function generateUniqueCode()
    {
        do {
            $code = 'T-' . strtoupper(Str::random(12)); // Adjust length as needed
        } while (self::where('code', $code)->exists());

        return $code;
    }

    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'task_assignments')
                    ->withPivot('task_category_id', 'status')
                    ->withTimestamps();
    }

    public function taskCategories()
    {
        return $this->belongsTo(TaskCategory::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function requirement()
    {
        return $this->belongsTo(Requirement::class);
    }

    public function assignments()
    {
        return $this->hasMany(TaskAssignment::class);
    }

    public function getTotalEffortsAttribute()
    {
        return $this->assignments()->sum('efforts');
    }

    public function getTotalAssignmentsAttribute()
    {
        return $this->assignments()->count();
    }
}
