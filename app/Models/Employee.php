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
        'status_id',
        'grade_id',
        'photo',
        'face_data',
        'nik',
        'birth_date',
        'gender',
        'birth_place',
        'address',
        'joined_at',
        'contract_start_date',
        'contract_end_date',
        'salary',
        'bank_name',
        'bank_account_number',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relationship',
        'education_background',
        'skills',
        'notes',
        'is_active'
    ];

    protected $casts = [
        'joined_at' => 'datetime',
        'birth_date' => 'date',
        'contract_start_date' => 'date',
        'contract_end_date' => 'date',
        'salary' => 'decimal:2',
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
     * Get the employment status of the employee
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(EmployeeStatus::class, 'status_id');
    }
    
    /**
     * Get the grade/classification of the employee
     */
    public function grade(): BelongsTo
    {
        return $this->belongsTo(EmployeeGrade::class, 'grade_id');
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
    
    /**
     * Determine if an employee is on contract
     */
    public function isOnContract(): bool
    {
        return !is_null($this->contract_end_date);
    }
    
    /**
     * Determine if an employee's contract is near expiry (within 30 days)
     */
    public function isContractNearExpiry(): bool
    {
        if (is_null($this->contract_end_date)) {
            return false;
        }
        
        return now()->diffInDays($this->contract_end_date, false) <= 30 && 
               now()->diffInDays($this->contract_end_date, false) >= 0;
    }
    
    /**
     * Determine if the employee's contract has expired
     */
    public function isContractExpired(): bool
    {
        if (is_null($this->contract_end_date)) {
            return false;
        }
        
        return now()->greaterThan($this->contract_end_date);
    }
}
