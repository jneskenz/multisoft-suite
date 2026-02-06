<?php

namespace Modules\Core\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Core\Models\Setting;
use Modules\Core\Models\Tenant;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenant = Tenant::where('code', 'TNT-001')->first();

        if (!$tenant) {
            $this->command->error('Tenant no encontrado. Ejecuta TenantSeeder primero.');
            return;
        }

        // Configuraciones de seguridad (nivel tenant)
        $securitySettings = [
            [
                'key' => 'password_min_length',
                'value' => '8',
                'type' => 'integer',
                'display_name' => 'Longitud mínima de contraseña',
                'description' => 'Número mínimo de caracteres para las contraseñas',
            ],
            [
                'key' => 'password_require_uppercase',
                'value' => 'true',
                'type' => 'boolean',
                'display_name' => 'Requerir mayúsculas',
                'description' => 'La contraseña debe incluir al menos una letra mayúscula',
            ],
            [
                'key' => 'password_require_number',
                'value' => 'true',
                'type' => 'boolean',
                'display_name' => 'Requerir números',
                'description' => 'La contraseña debe incluir al menos un número',
            ],
            [
                'key' => 'password_require_special',
                'value' => 'false',
                'type' => 'boolean',
                'display_name' => 'Requerir caracteres especiales',
                'description' => 'La contraseña debe incluir al menos un carácter especial',
            ],
            [
                'key' => 'session_lifetime',
                'value' => '120',
                'type' => 'integer',
                'display_name' => 'Duración de sesión (minutos)',
                'description' => 'Tiempo en minutos antes de que la sesión expire por inactividad',
            ],
            [
                'key' => 'max_login_attempts',
                'value' => '5',
                'type' => 'integer',
                'display_name' => 'Intentos máximos de login',
                'description' => 'Número de intentos fallidos antes de bloquear la cuenta',
            ],
            [
                'key' => 'lockout_duration',
                'value' => '15',
                'type' => 'integer',
                'display_name' => 'Duración del bloqueo (minutos)',
                'description' => 'Tiempo en minutos que la cuenta permanece bloqueada',
            ],
            [
                'key' => 'two_factor_enabled',
                'value' => 'true',
                'type' => 'boolean',
                'display_name' => 'Habilitar 2FA',
                'description' => 'Permitir autenticación de dos factores',
            ],
            [
                'key' => 'two_factor_required',
                'value' => 'false',
                'type' => 'boolean',
                'display_name' => 'Requerir 2FA',
                'description' => 'Hacer obligatoria la autenticación de dos factores',
            ],
        ];

        foreach ($securitySettings as $setting) {
            Setting::updateOrCreate(
                [
                    'tenant_id' => $tenant->id,
                    'group_company_id' => null,
                    'company_id' => null,
                    'location_id' => null,
                    'category' => 'security',
                    'key' => $setting['key'],
                ],
                [
                    'value' => $setting['value'],
                    'type' => $setting['type'],
                    'display_name' => $setting['display_name'],
                    'description' => $setting['description'],
                ]
            );
        }

        $this->command->info('Configuraciones de seguridad creadas.');

        // Configuraciones de apariencia (nivel tenant)
        $appearanceSettings = [
            [
                'key' => 'theme',
                'value' => 'light',
                'type' => 'string',
                'display_name' => 'Tema',
                'description' => 'Tema de la interfaz: light, dark, system',
            ],
            [
                'key' => 'sidebar_collapsed',
                'value' => 'false',
                'type' => 'boolean',
                'display_name' => 'Sidebar colapsado',
                'description' => 'Mostrar sidebar colapsado por defecto',
            ],
            [
                'key' => 'items_per_page',
                'value' => '15',
                'type' => 'integer',
                'display_name' => 'Items por página',
                'description' => 'Número de items por página en tablas',
            ],
            [
                'key' => 'compact_mode',
                'value' => 'false',
                'type' => 'boolean',
                'display_name' => 'Modo compacto',
                'description' => 'Usar modo compacto en tablas y listas',
            ],
        ];

        foreach ($appearanceSettings as $setting) {
            Setting::updateOrCreate(
                [
                    'tenant_id' => $tenant->id,
                    'group_company_id' => null,
                    'company_id' => null,
                    'location_id' => null,
                    'category' => 'appearance',
                    'key' => $setting['key'],
                ],
                [
                    'value' => $setting['value'],
                    'type' => $setting['type'],
                    'display_name' => $setting['display_name'],
                    'description' => $setting['description'],
                ]
            );
        }

        $this->command->info('Configuraciones de apariencia creadas.');

        // Configuraciones de notificaciones (nivel tenant)
        $notificationSettings = [
            [
                'key' => 'email_enabled',
                'value' => 'true',
                'type' => 'boolean',
                'display_name' => 'Notificaciones por email',
                'description' => 'Habilitar notificaciones por correo electrónico',
            ],
            [
                'key' => 'in_app_enabled',
                'value' => 'true',
                'type' => 'boolean',
                'display_name' => 'Notificaciones in-app',
                'description' => 'Habilitar notificaciones dentro de la aplicación',
            ],
            [
                'key' => 'digest_frequency',
                'value' => 'instant',
                'type' => 'string',
                'display_name' => 'Frecuencia de resumen',
                'description' => 'Frecuencia de resumen de notificaciones: instant, daily, weekly',
            ],
        ];

        foreach ($notificationSettings as $setting) {
            Setting::updateOrCreate(
                [
                    'tenant_id' => $tenant->id,
                    'group_company_id' => null,
                    'company_id' => null,
                    'location_id' => null,
                    'category' => 'notifications',
                    'key' => $setting['key'],
                ],
                [
                    'value' => $setting['value'],
                    'type' => $setting['type'],
                    'display_name' => $setting['display_name'],
                    'description' => $setting['description'],
                ]
            );
        }

        $this->command->info('Configuraciones de notificaciones creadas.');
    }
}
