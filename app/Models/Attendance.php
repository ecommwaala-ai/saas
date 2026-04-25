<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    /** @use HasFactory<\Database\Factories\AttendanceFactory> */
    use HasFactory;

    public const TIMEZONE = 'Asia/Kolkata';

    protected $fillable = [
        'tenant_id',
        'agent_id',
        'shift_date',
        'clock_in',
        'clock_out',
        'total_hours',
    ];

    protected function casts(): array
    {
        return [
            'shift_date' => 'date',
            'clock_in' => 'datetime',
            'clock_out' => 'datetime',
            'total_hours' => 'decimal:2',
        ];
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function isActive(): bool
    {
        return $this->clock_out === null;
    }
}
