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
            $table->longText('template_order_created')->nullable();
            $table->longText('template_order_updated')->nullable();
            $table->longText('template_order_assigned')->nullable();
            $table->longText('template_order_alert_to_admin')->nullable();
            $table->longText('template_order_dispatch')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('template_order_created');
            $table->dropColumn('template_order_updated');
            $table->dropColumn('template_order_assigned');
            $table->dropColumn('template_order_alert_to_admin');
            $table->dropColumn('template_order_dispatch');
        });
    }
};
