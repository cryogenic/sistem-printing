<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RoleAndAdminSeeder extends Seeder
{
    public function run()
    {
        // Cipta senarai peranan (Roles)
        Role::create(['name' => 'super_admin']);
        Role::create(['name' => 'staf_kilang']);
        Role::create(['name' => 'ejen']);
        Role::create(['name' => 'awam']);

        // Cipta akaun Admin Pertama
        $admin = User::create([
            'name' => 'Pemilik Sistem',
            'email' => 'admin@sistem.com', // Anda boleh ubah emel ini
            'password' => Hash::make('password123'), // Anda boleh ubah kata laluan
        ]);

        // Berikan peranan super_admin kepada akaun ini
        $admin->assignRole('super_admin');
    }
}