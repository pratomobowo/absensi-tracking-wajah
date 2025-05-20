<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'name',
        'email',
        'phone',
        'position',
        'department_id',
        'photo',
        'face_data',
        'joined_at',
        'is_active'
    ];

    protected $casts = [
        'joined_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Get the department that the employee belongs to
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get all attendances for the employee
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }
    
    /**
     * Get today's attendance record
     */
    public function todayAttendance()
    {
        return $this->attendances()->whereDate('date', now()->toDateString())->first();
    }
}
