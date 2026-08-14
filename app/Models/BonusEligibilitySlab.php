<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BonusEligibilitySlab extends Model
{
    protected $fillable = [
        'bonus_config_id',
        'min_months',
        'max_months',
        'percent_of_bonus',
    ];

    public function bonusConfig(): BelongsTo
    {
        return $this->belongsTo(BonusConfig::class);
    }
}
