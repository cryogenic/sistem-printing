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
    Schema::table('orders', function (Blueprint $table) {
        $table->string('selected_material')->nullable()->after('job_title');
        $table->decimal('length', 8, 2)->nullable()->after('selected_material');
        $table->decimal('width', 8, 2)->nullable()->after('length');
    });
}

public function down(): void
{
    Schema::table('orders', function (Blueprint $table) {
        $table->dropColumn(['selected_material', 'length', 'width']);
    });
}
};
