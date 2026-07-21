<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gateway_configs', function (Blueprint $table) {
            $table->id();
            // SMS Gateway
            $table->string('sms_provider')->default('greenweb');
            $table->string('sms_api_key')->nullable();
            $table->string('sms_sender_id')->nullable();

            // Mail SMTP Server
            $table->string('mail_host')->default('smtp.mailtrap.io');
            $table->integer('mail_port')->default(2525);
            $table->string('mail_username')->nullable();
            $table->string('mail_password')->nullable();
            $table->string('mail_encryption')->default('tls');
            $table->string('mail_from_address')->default('noreply@amds.test');
            $table->string('mail_from_name')->default('ZKTeco ADMS SaaS');

            // SSLCommerz Gateway
            $table->string('sslcommerz_store_id')->nullable();
            $table->string('sslcommerz_store_passwd')->nullable();
            $table->boolean('sslcommerz_is_sandbox')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gateway_configs');
    }
};
