<?php

namespace Modules\ERP\Services;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\ERP\Models\Catalogo;

final class CatalogoService
{
    /**
     * @return Collection<int,object>
     */
    public function listByCategoria(int $categoriaId, int $limit = 100): Collection
    {
        if ($categoriaId <= 0 || !Schema::hasTable('erp_catalogos')) {
            return collect();
        }

        $query = DB::table('erp_catalogos as c')
            ->leftJoin('erp_subcategorias as s', 's.id', '=', 'c.subcategoria_id')
            ->leftJoin('erp_categorias as cat', 'cat.id', '=', 'c.categoria_id')
            ->select([
                'c.id',
                'c.codigo',
                'c.descripcion',
                'c.estado',
                'c.categoria_id',
                'cat.codigo as categoria_codigo',
                'c.subcategoria_id',
                's.nombre as subcategoria_nombre',
                'c.created_at',
            ])
            ->where('c.categoria_id', $categoriaId)
            ->whereNull('c.deleted_at')
            ->orderByDesc('c.id');

        if ($limit > 0) {
            $query->limit($limit);
        }

        return $query->get();
    }

    public function findById(int $registroId): ?Catalogo
    {
        if ($registroId <= 0) {
            return null;
        }

        return Catalogo::query()->find($registroId);
    }

    public function deleteById(int $registroId, ?int $categoriaId = null): bool
    {
        if ($registroId <= 0) {
            return false;
        }

        $query = Catalogo::query()->whereKey($registroId);
        if ($categoriaId !== null && $categoriaId > 0) {
            $query->where('categoria_id', $categoriaId);
        }

        $catalogo = $query->first();
        if (!$catalogo) {
            return false;
        }

        return (bool) $catalogo->delete();
    }

    /**
     * Crea o actualiza un registro de erp_catalogos usando el payload del modal.
     *
     * @param array<string,mixed> $values
     */
    public function saveFromModal(
        ?int $registroId,
        int $categoriaId,
        mixed $subcategoria,
        string $codigo,
        string $descripcion,
        int $estado,
        array $values = [],
        ?string $imagen = null,
        ?int $userId = null
    ): Catalogo {
        $payload = CatalogoFieldMap::payloadFromModal(
            categoriaId: $categoriaId,
            subcategoria: $subcategoria,
            codigo: $codigo,
            descripcion: $descripcion,
            estado: $estado,
            values: $values,
            imagen: $imagen
        );

        return DB::transaction(function () use ($registroId, $payload, $userId): Catalogo {
            if ($registroId !== null && $registroId > 0) {
                $catalogo = Catalogo::query()->find($registroId);
                if (!$catalogo) {
                    throw new ModelNotFoundException('Registro de catalogo no encontrado.');
                }
            } else {
                $catalogo = new Catalogo();
                if ($userId !== null) {
                    $payload['created_by'] = $userId;
                }
            }

            if ($userId !== null) {
                $payload['updated_by'] = $userId;
            }

            $catalogo->fill($payload);
            $catalogo->save();

            return $catalogo->fresh() ?? $catalogo;
        });
    }
}
