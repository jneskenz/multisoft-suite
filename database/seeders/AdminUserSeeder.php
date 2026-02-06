<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Modules\Core\Models\GroupCompany;
use Modules\Core\Models\Role;
use Modules\Core\Models\Tenant;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenant = Tenant::where('code', 'TNT-001')->first();

        $admin = User::updateOrCreate(
            ['email' => 'admin@multisoft.test'],
            [
                'tenant_id' => $tenant?->id, // Super-admin puede tener tenant o ser null para acceso global
                'name' => 'Administrador',
                'email' => 'admin@multisoft.test',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'estado' => User::ESTADO_ACTIVO,
            ]
        );

        // Asignar rol usando Spatie
        $admin->assignRole('superadmin');

        // Asignar acceso a todos los grupos del tenant
        if ($tenant) {
            $groupIds = $tenant->groupCompanies()->pluck('id')->toArray();
            $admin->syncGroupAccess($groupIds);
            
            $this->command->info('  Grupos asignados: ' . implode(', ', $tenant->groupCompanies()->pluck('code')->toArray()));
        } else {
            // Si no hay tenant, asignar todos los grupos activos
            $groupIds = GroupCompany::active()->pluck('id')->toArray();
            $admin->syncGroupAccess($groupIds);
            
            $this->command->info('  Grupos asignados: Todos (admin global)');
        }

        $this->command->info('Usuario administrador creado:');
        $this->command->info('  Email: admin@multisoft.test');
        $this->command->info('  Password: password');
        $this->command->info('  Rol: Super Administrador');
        $this->command->info('  Tenant: ' . ($tenant ? $tenant->name : 'Global'));
    }
}
