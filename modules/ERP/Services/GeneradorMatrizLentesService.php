<?php

namespace Modules\ERP\Services;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\ERP\Models\CombinacionMedida;

final class GeneradorMatrizLentesService
{
    private const TAMANO_LOTE = 500;

    /**
     * @return array{combinacion_medida_id:int,catalogo_id:int,filas_generadas:int,adiciones:int,esfericas:int,cilindricas:int}
     */
    public function generarDesdeCombinacion(int $combinacionMedidaId, ?int $userId = null): array
    {
        $this->validarTablasRequeridas();

        $combinacion = CombinacionMedida::query()
            ->with('catalogo:id,categoria_id,codigo')
            ->find($combinacionMedidaId);

        if (!$combinacion) {
            throw new ModelNotFoundException('Combinacion de medida no encontrada.');
        }

        $catalogo = $combinacion->catalogo;
        if (!$catalogo) {
            throw new ModelNotFoundException('Catalogo asociado a la combinacion no encontrado.');
        }

        $medidasEsfericas = $this->resolverRangoIds(
            'erp_medida_esferica',
            (int) $combinacion->medida_esferica_desde_id,
            (int) $combinacion->medida_esferica_hasta_id
        );

        $medidasCilindricas = $this->resolverRangoIds(
            'erp_medida_cilindrica',
            (int) $combinacion->medida_cilindrica_desde_id,
            (int) $combinacion->medida_cilindrica_hasta_id
        );

        $adiciones = $this->resolverRangoIds(
            'erp_adiciones',
            (int) $combinacion->adicion_desde_id,
            (int) $combinacion->adicion_hasta_id
        );

        if ($medidasEsfericas === [] || $medidasCilindricas === [] || $adiciones === []) {
            throw new \RuntimeException('No se pudo resolver el rango completo de la combinacion.');
        }

        $ahora = now();
        $filasGeneradas = 0;

        DB::transaction(function () use (
            $adiciones,
            $medidasEsfericas,
            $medidasCilindricas,
            $catalogo,
            $combinacion,
            $userId,
            $ahora,
            &$filasGeneradas
        ): void {
            $lote = [];

            foreach ($adiciones as $adicionId) {
                foreach ($medidasEsfericas as $medidaEsfericaId) {
                    foreach ($medidasCilindricas as $medidaCilindricaId) {
                        $lote[] = [
                            'catalogo_id' => (int) $catalogo->id,
                            'combinacion_medida_id' => (int) $combinacion->id,
                            'categoria_id' => $catalogo->categoria_id ? (int) $catalogo->categoria_id : null,
                            'serie_visual_id' => (int) $combinacion->serie_visual_id,
                            'subserie_visual_id' => (int) $combinacion->subserie_visual_id,
                            'adicion_id' => (int) $adicionId,
                            'medida_esferica_id' => (int) $medidaEsfericaId,
                            'medida_cilindrica_id' => (int) $medidaCilindricaId,
                            'codigo_matriz' => $this->construirCodigoMatriz(
                                (string) ($catalogo->codigo ?? ''),
                                (int) $adicionId,
                                (int) $medidaEsfericaId,
                                (int) $medidaCilindricaId
                            ),
                            'estado' => 1,
                            'generado_at' => $ahora,
                            'created_by' => $userId,
                            'updated_by' => $userId,
                            'created_at' => $ahora,
                            'updated_at' => $ahora,
                            'deleted_at' => null,
                        ];

                        if (count($lote) >= self::TAMANO_LOTE) {
                            $this->persistirLote($lote);
                            $filasGeneradas += count($lote);
                            $lote = [];
                        }
                    }
                }
            }

            if ($lote !== []) {
                $this->persistirLote($lote);
                $filasGeneradas += count($lote);
            }
        });

        return [
            'combinacion_medida_id' => (int) $combinacion->id,
            'catalogo_id' => (int) $catalogo->id,
            'filas_generadas' => $filasGeneradas,
            'adiciones' => count($adiciones),
            'esfericas' => count($medidasEsfericas),
            'cilindricas' => count($medidasCilindricas),
        ];
    }

    private function validarTablasRequeridas(): void
    {
        $tablas = [
            'erp_matriz_lentes',
            'erp_catalogos',
            'erp_combinacion_medidas',
            'erp_medida_esferica',
            'erp_medida_cilindrica',
            'erp_adiciones',
        ];

        foreach ($tablas as $tabla) {
            if (!Schema::hasTable($tabla)) {
                throw new \RuntimeException("La tabla {$tabla} no existe.");
            }
        }
    }

    /**
     * @return array<int,int>
     */
    private function resolverRangoIds(string $tabla, int $desdeId, int $hastaId): array
    {
        if ($desdeId <= 0 || $hastaId <= 0) {
            return [];
        }

        $opciones = $this->obtenerOpcionesOrdenadas($tabla);
        $ids = $opciones->pluck('id')->map(fn($id) => (int) $id)->values()->all();

        $indiceDesde = array_search($desdeId, $ids, true);
        $indiceHasta = array_search($hastaId, $ids, true);

        if ($indiceDesde === false || $indiceHasta === false) {
            return [];
        }

        $inicio = min($indiceDesde, $indiceHasta);
        $fin = max($indiceDesde, $indiceHasta);

        return array_values(array_slice($ids, $inicio, ($fin - $inicio) + 1));
    }

    /**
     * @return Collection<int,object>
     */
    private function obtenerOpcionesOrdenadas(string $tabla): Collection
    {
        $rows = DB::table($tabla)
            ->select('id', 'codigo', 'nombre')
            ->when(
                Schema::hasColumn($tabla, 'deleted_at'),
                fn($query) => $query->whereNull('deleted_at')
            )
            ->when(
                Schema::hasColumn($tabla, 'estado'),
                fn($query) => $query->where('estado', 1)
            )
            ->get();

        return $rows
            ->map(function (object $row): object {
                $textoBase = trim((string) ($row->codigo ?? $row->nombre ?? ''));
                $valor = $this->resolverValorOrden($textoBase);

                return (object) [
                    'id' => (int) $row->id,
                    'codigo' => (string) ($row->codigo ?? ''),
                    'nombre' => (string) ($row->nombre ?? ''),
                    'valor_orden' => $valor,
                    'texto_orden' => mb_strtoupper(trim((string) ($row->nombre ?? $row->codigo ?? ''))),
                ];
            })
            ->sort(function (object $a, object $b): int {
                if ($a->valor_orden === $b->valor_orden) {
                    return strcmp($a->texto_orden, $b->texto_orden);
                }

                return $a->valor_orden <=> $b->valor_orden;
            })
            ->values();
    }

    private function resolverValorOrden(string $texto): float
    {
        $textoNormalizado = mb_strtoupper(trim(str_replace(',', '.', $texto)));
        if ($textoNormalizado === '') {
            return 0.0;
        }

        if (str_contains($textoNormalizado, 'NINGUN') || str_contains($textoNormalizado, 'NINGUNA')) {
            return 0.0;
        }

        if (preg_match('/[-+]?\d+(?:\.\d+)?/', $textoNormalizado, $matches) === 1) {
            return (float) $matches[0];
        }

        return 999999.0;
    }

    private function construirCodigoMatriz(
        string $codigoCatalogo,
        int $adicionId,
        int $medidaEsfericaId,
        int $medidaCilindricaId
    ): string {
        $base = trim($codigoCatalogo) !== '' ? trim($codigoCatalogo) : 'CAT';

        return implode('-', [
            $base,
            'A' . $adicionId,
            'E' . $medidaEsfericaId,
            'C' . $medidaCilindricaId,
        ]);
    }

    /**
     * @param array<int,array<string,mixed>> $lote
     */
    private function persistirLote(array $lote): void
    {
        DB::table('erp_matriz_lentes')->upsert(
            $lote,
            [
                'catalogo_id',
                'combinacion_medida_id',
                'adicion_id',
                'medida_esferica_id',
                'medida_cilindrica_id',
            ],
            [
                'categoria_id',
                'serie_visual_id',
                'subserie_visual_id',
                'codigo_matriz',
                'estado',
                'generado_at',
                'updated_by',
                'updated_at',
                'deleted_at',
            ]
        );
    }
}
