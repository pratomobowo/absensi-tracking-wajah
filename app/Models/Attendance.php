<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'date',
        'clock_in',
        'clock_out',
        'clock_in_photo',
        'clock_out_photo',
        'status',
        'notes',
        'ip_address',
        'location',
        'device'
    ];

    protected $casts = [
        'date' => 'date',
        'clock_in' => 'datetime',
        'clock_out' => 'datetime',
    ];

    /**
     * Get the employee that the attendance belongs to
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Calculate work hours for this attendance record
     */
    public function getWorkHoursAttribute()
    {
        if ($this->clock_in && $this->clock_out) {
            $clockIn = \Carbon\Carbon::parse($this->clock_in);
            $clockOut = \Carbon\Carbon::parse($this->clock_out);
            
            return $clockOut->diffInHours($clockIn);
        }
        
        return 0;
    }
}
