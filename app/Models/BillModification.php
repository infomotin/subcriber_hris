<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BillModification extends Model
{
    protected $fillable = ['bill_id', 'original_amount', 'new_amount', 'reason', 'modified_by'];

    protected $casts = [
        'original_amount' => 'decimal:2',
        'new_amount' => 'decimal:2',
    ];

    public function bill(): BelongsTo
    {
        return $this->belongsTo(Bill::class);
    }

    public function modifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'modified_by');
    }
}
