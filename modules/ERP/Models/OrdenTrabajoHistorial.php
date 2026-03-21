<?php

namespace Modules\ERP\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrdenTrabajoHistorial extends Model
{
    public $timestamps = false;

    protected $table = 'erp_orden_trabajo_historial';

    /**
     * @var array<int,string>
     */
    protected $fillable = [
        'orden_trabajo_id',
        'estado_anterior',
        'estado_nuevo',
        'observacion',
        'changed_by',
        'created_at',
    ];

    /**
     * @var array<string,string>
     */
    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function ordenTrabajo(): BelongsTo
    {
        return $this->belongsTo(OrdenTrabajo::class, 'orden_trabajo_id');
    }

    public function cambiadoPor(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'changed_by');
    }
}
