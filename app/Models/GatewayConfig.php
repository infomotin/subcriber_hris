<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GatewayConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'sms_provider',
        'sms_api_key',
        'sms_sender_id',
        'mail_host',
        'mail_port',
        'mail_username',
        'mail_password',
        'mail_encryption',
        'mail_from_address',
        'mail_from_name',
        'sslcommerz_store_id',
        'sslcommerz_store_passwd',
        'sslcommerz_is_sandbox',
    ];

    protected $casts = [
        'mail_port' => 'integer',
        'sslcommerz_is_sandbox' => 'boolean',
    ];
}
