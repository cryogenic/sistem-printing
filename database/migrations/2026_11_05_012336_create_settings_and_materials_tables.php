<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();      // contoh: tarpaulin_380
            $table->string('name');
            $table->decimal('price_per_sqft', 8, 2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Nilai lalai (sama seperti yang ada sekarang)
        $now = now();
        DB::table('materials')->insert([
            ['code' => 'tarpaulin_380',   'name' => 'Tarpaulin 380gsm', 'price_per_sqft' => 1.50, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'synthetic_paper', 'name' => 'Synthetic Paper',  'price_per_sqft' => 2.50, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['code' => 'sticker_white',   'name' => 'Sticker White PP', 'price_per_sqft' => 3.00, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);

        foreach ([
            'shop_name'          => 'PrintFlow',
            'shop_tagline'       => 'Printing Management',
            'courier_fee'        => '10',
            'allow_registration' => '1',
        ] as $k => $v) {
            DB::table('settings')->insert(['key' => $k, 'value' => $v, 'created_at' => $now, 'updated_at' => $now]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('materials');
        Schema::dropIfExists('settings');
    }
};