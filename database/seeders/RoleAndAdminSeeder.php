<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class RoleAndAdminSeeder extends Seeder
{
    /**
     * Cipta peranan asas dan akaun admin pertama.
     *
     * Selamat dijalankan berulang kali (idempotent) — guna firstOrCreate,
     * bukan create, supaya `db:seed` tidak gagal dengan RoleAlreadyExists.
     */
    public function run(): void
    {
        // Cipta senarai peranan (Roles) — langkau jika sudah wujud
        foreach (['super_admin', 'staf_kilang', 'ejen', 'awam'] as $roleName) {
            Role::firstOrCreate([
                'name'       => $roleName,
                'guard_name' => 'web',
            ]);
        }

        // Cipta akaun Admin Pertama jika belum ada.
        // Nilai lalai boleh ditukar melalui .env (ADMIN_EMAIL / ADMIN_PASSWORD)
        // supaya kata laluan lalai tidak digunakan dalam production.
        $email    = env('ADMIN_EMAIL', 'admin@sistem.com');
        $password = env('ADMIN_PASSWORD', 'password123');

        $admin = User::firstOrCreate(
            ['email' => $email],
            [
                'name'     => 'Pemilik Sistem',
                'password' => Hash::make($password),
                // Middleware 'admin' menyemak kolum role, bukan peranan Spatie.
                'role'     => 'admin',
            ]
        );

        // Berikan peranan Spatie juga, untuk penggunaan permission kelak
        if (! $admin->hasRole('super_admin')) {
            $admin->assignRole('super_admin');
        }

        $this->command?->info('Akaun admin: ' . $admin->email);
        if ($admin->wasRecentlyCreated) {
            $this->command?->warn('Kata laluan lalai: ' . $password . ' — SILA TUKAR SELEPAS LOG MASUK.');
        }
    }
}
