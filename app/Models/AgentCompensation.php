<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgentCompensation extends Model
{
    /** @use HasFactory<\Database\Factories\AgentCompensationFactory> */
    use HasFactory;

    public const TYPE_SALARY = 'salary';
    public const TYPE_COMMISSION = 'commission';

    public const TYPES = [
        self::TYPE_SALARY,
        self::TYPE_COMMISSION,
    ];

    protected $fillable = [
        'tenant_id',
        'agent_id',
        'type',
        'base_salary',
        'commission_rate',
        'incentive_details',
    ];

    protected function casts(): array
    {
        return [
            'base_salary' => 'decimal:2',
            'commission_rate' => 'decimal:2',
            'incentive_details' => 'array',
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

    public function isSalary(): bool
    {
        return $this->type === self::TYPE_SALARY;
    }

    public function isCommission(): bool
    {
        return $this->type === self::TYPE_COMMISSION;
    }

    public function typeLabel(): string
    {
        return str($this->type)->title()->toString();
    }
}
