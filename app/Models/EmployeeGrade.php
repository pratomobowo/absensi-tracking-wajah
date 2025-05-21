<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmployeeGrade extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'level',
        'salary_min',
        'salary_max',
        'is_active'
    ];

    protected $casts = [
        'level' => 'integer',
        'salary_min' => 'decimal:2',
        'salary_max' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get all employees with this grade
     */
    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class, 'grade_id');
    }
}
