<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdminRole = Role::where('name', 'super-admin')->first();

        User::updateOrCreate(
            ['email' => 'admin@multisoft.test'],
            [
                'name' => 'Administrador',
                'email' => 'admin@multisoft.test',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role_id' => $superAdminRole?->id,
            ]
        );

        $this->command->info('Usuario administrador creado:');
        $this->command->info('  Email: admin@multisoft.test');
        $this->command->info('  Password: password');
        $this->command->info('  Rol: Super Administrador');
    }
}
