<?php

namespace App\Models;

use App\Enums\RecordStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Department extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'description','status','organisation_id'];

    protected $casts = [
        'status' =>  RecordStatus::class,
    ];

    public function organisation(): BelongsTo
    {
        return $this->belongsTo(Organisation::class);
    }

    // Many-to-many relationship with Designation
    public function designations()
    {
        return $this->belongsToMany(Designation::class, 'department_designation');
    }

    // Relationship with employees
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
}
