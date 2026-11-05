<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('job_title')->nullable()->after('order_number');
            $table->text('remarks')->nullable()->after('job_title');
            $table->string('fulfillment_type')->default('pickup')->after('remarks'); // 'pickup' atau 'courier'
            $table->text('shipping_address')->nullable()->after('fulfillment_type');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['job_title', 'remarks', 'fulfillment_type', 'shipping_address']);
        });
    }
};