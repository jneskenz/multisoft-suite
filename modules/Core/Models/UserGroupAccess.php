<?php

namespace Modules\Core\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo pivot para controlar acceso de usuarios a grupos.
 * 
 * Permite definir a qué grupos (países) tiene acceso cada usuario.
 * Un usuario puede tener acceso a múltiples grupos del mismo tenant.
 */
class UserGroupAccess extends Model
{
    /**
     * La tabla asociada al modelo.
     */
    protected $table = 'core_user_group_access';

    /**
     * Los atributos que son asignables masivamente.
     */
    protected $fillable = [
        'user_id',
        'group_company_id',
    ];

    /**
     * Obtener el usuario.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtener el grupo.
     */
    public function groupCompany(): BelongsTo
    {
        return $this->belongsTo(GroupCompany::class);
    }
}
