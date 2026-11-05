<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar_path')->nullable();
            $table->text('mailing_address')->nullable();
            $table->boolean('courier_same_as_mailing')->default(false);
        });

        Schema::create('courier_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('label');            // contoh: Pejabat, Rumah
            $table->string('recipient_name');
            $table->string('phone');
            $table->text('address');
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courier_addresses');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['avatar_path', 'mailing_address', 'courier_same_as_mailing']);
        });
    }
};