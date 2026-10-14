<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->boolean('wa_status')->default(false);
            $table->string('wa_twilio_account_sid')->nullable();
            $table->string('wa_twilio_auth_token')->nullable();
            $table->string('wa_twilio_from')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('wa_status');
            $table->dropColumn('wa_twilio_account_sid');
            $table->dropColumn('wa_twilio_auth_token');
            $table->dropColumn('wa_twilio_from');
        });
    }
};
