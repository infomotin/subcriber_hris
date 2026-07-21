<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeviceCommand extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_id',
        'command',
        'type',
        'status',
        'return_code',
        'response',
        'executed_at',
    ];

    protected $casts = [
        'return_code' => 'integer',
        'executed_at' => 'datetime',
    ];

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    public function getFormattedCommandAttribute(): string
    {
        return "C:{$this->id}:{$this->command}";
    }

    public function markAsSent(): void
    {
        $this->update(['status' => 'sent']);
    }

    public function markAsExecuted(?int $returnCode = 0, ?string $response = null): void
    {
        $this->update([
            'status' => $returnCode === 0 ? 'executed' : 'failed',
            'return_code' => $returnCode,
            'response' => $response,
            'executed_at' => now(),
        ]);
    }
}
