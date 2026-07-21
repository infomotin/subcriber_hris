<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebsiteSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'hero_title',
        'hero_subtitle',
        'hero_cta_text',
        'features_json',
        'contact_email',
        'contact_phone',
    ];

    protected $casts = [
        'features_json' => 'array',
    ];
}
