<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TaskAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_category_id',
        'employee_id',
        'efforts',
        'start_date',
        'due_date',
        'status',
        'code'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($assignment) {
            if (empty($assignment->code)) {
                $assignment->code = $assignment->generateUniqueCode();
            }
        });
    }

    protected function generateUniqueCode()
    {
        do {
            $code = 'TA-' . strtoupper(Str::random(12)); // Adjust length as needed
        } while (self::where('code', $code)->exists());

        return $code;
    }

    protected $table = 'task_assignments';

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function taskCategory()
    {
        return $this->belongsTo(TaskCategory::class);
    }
}
