<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Designation extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'level','department_id'];


   // Many-to-many relationship with Department
   public function departments()
   {
       return $this->belongsToMany(Department::class, 'department_designation');
   }

   // Relationship with employees
   public function employees()
   {
       return $this->hasMany(Employee::class);
   }
}
