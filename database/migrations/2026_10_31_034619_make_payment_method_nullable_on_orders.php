<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::table('orders', function (Blueprint $table) {
        if (!Schema::hasColumn('orders', 'job_title')) {
            $table->string('job_title')->nullable()->after('order_number');
        }
        if (!Schema::hasColumn('orders', 'remarks')) {
            $table->text('remarks')->nullable();
        }
        if (!Schema::hasColumn('orders', 'fulfillment_type')) {
            $table->string('fulfillment_type')->nullable();
        }
        if (!Schema::hasColumn('orders', 'shipping_address')) {
            $table->text('shipping_address')->nullable();
        }
    });
}

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('payment_method', ['fpx', 'wallet'])->nullable(false)->change();
        });
    }
};