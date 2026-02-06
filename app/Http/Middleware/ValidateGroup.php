<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Core\Models\GroupCompany;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware para validar y establecer el grupo de empresas activo.
 *
 * Este middleware verifica que:
 * 1. El código de grupo en la URL sea válido
 * 2. El grupo pertenezca al tenant del usuario autenticado
 * 3. El usuario tenga acceso al grupo
 *
 * Si todo es válido, almacena el grupo en sesión y lo hace
 * disponible a través del helper current_group().
 */
class ValidateGroup
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $groupCode = $request->route('group');

        // Si no hay código de grupo en la ruta, continuar sin validación
        if (! $groupCode) {
            return $next($request);
        }

        // Buscar el grupo por código
        $group = GroupCompany::where('code', strtoupper($groupCode))
            ->where('status', 'active')
            ->first();

        // Validar que el grupo exista
        if (! $group) {
            abort(404, __('Grupo no encontrado'));
        }

        // Si el usuario está autenticado, validar acceso
        if (auth()->check()) {
            $user = auth()->user();

            // Superadmin tiene acceso a todo
            if (method_exists($user, 'hasRole') && $user->hasRole('superadmin')) {
                // Permitir acceso
            }
            // Validar acceso solo si el usuario tiene tenant asignado
            elseif ($user->tenant_id) {
                // Validar que el grupo pertenezca al tenant del usuario
                if ($group->tenant_id !== $user->tenant_id) {
                    abort(403, __('No tienes acceso a este grupo'));
                }

                // Validar que el usuario tenga acceso al grupo
                if (! $user->hasAccessToGroup($group->code)) {
                    abort(403, __('No tienes acceso a este grupo'));
                }
            }
            // Si no tiene tenant, permitir acceso (desarrollo/migración)
        }

        // Almacenar el grupo en sesión y como singleton
        session(['current_group_id' => $group->id]);
        session(['current_group_code' => $group->code]);
        session(['last_group_code' => $group->code]);

        // Registrar el grupo actual como singleton para acceso rápido
        app()->instance('current_group', $group);

        // Establecer defaults para URLs
        \Illuminate\Support\Facades\URL::defaults([
            'group' => $group->code,
        ]);

        return $next($request);
    }
}
