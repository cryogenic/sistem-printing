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
    Schema::create('wallet_topups', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        $table->decimal('amount', 10, 2);
        $table->string('status')->default('pending'); // pending | paid
        $table->string('bill_code')->nullable()->index();
        $table->string('payment_reference')->nullable();
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('wallet_topups');
}
};
