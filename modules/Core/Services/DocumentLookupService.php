<?php

namespace Modules\Core\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DocumentLookupService
{
    protected string $baseUrl;
    protected string $token;
    protected int $timeout;
    protected int $cacheTtl;

    public function __construct()
    {
        $config = config('core.document_lookup');

        $this->baseUrl  = rtrim($config['base_url'], '/');
        $this->token    = $config['token'] ?? '';
        $this->timeout  = $config['timeout'] ?? 10;
        $this->cacheTtl = $config['cache_ttl'] ?? 3600;
    }

    /**
     * Consultar datos de persona por DNI.
     *
     * @return array{success: bool, data: array|null, message: string|null}
     */
    public function lookupDni(string $dni): array
    {
        $dni = trim($dni);

        if (! preg_match('/^\d{8}$/', $dni)) {
            return $this->error('El DNI debe tener exactamente 8 dígitos numéricos.');
        }

        return Cache::remember("document_lookup.dni.{$dni}", $this->cacheTtl, function () use ($dni) {
            return $this->request('dni', $dni);
        });
    }

    /**
     * Consultar datos de empresa por RUC.
     *
     * @return array{success: bool, data: array|null, message: string|null}
     */
    public function lookupRuc(string $ruc): array
    {
        $ruc = trim($ruc);

        if (! preg_match('/^\d{11}$/', $ruc)) {
            return $this->error('El RUC debe tener exactamente 11 dígitos numéricos.');
        }

        return Cache::remember("document_lookup.ruc.{$ruc}", $this->cacheTtl, function () use ($ruc) {
            return $this->request('ruc', $ruc);
        });
    }

    /**
     * Limpiar caché de un documento específico.
     */
    public function clearCache(string $type, string $number): void
    {
        Cache::forget("document_lookup.{$type}.{$number}");
    }

    /**
     * Resolver el endpoint correcto según el tipo de documento.
     * DNI  → /reniec/dni?numero=...
     * RUC  → /sunat/ruc?numero=...
     */
    protected function endpoint(string $type): string
    {
        return match ($type) {
            'dni' => "{$this->baseUrl}/reniec/dni",
            'ruc' => "{$this->baseUrl}/sunat/ruc",
            default => "{$this->baseUrl}/{$type}",
        };
    }

    /**
     * Realizar la petición HTTP a la API.
     *
     * @return array{success: bool, data: array|null, message: string|null}
     */
    protected function request(string $type, string $number): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Authorization' => "Bearer {$this->token}",
                    'Accept'        => 'application/json',
                ])
                ->get($this->endpoint($type), ['numero' => $number]);

            if ($response->successful()) {
                $data = $response->json();

                return $this->success($this->normalizeResponse($type, $data));
            }

            if ($response->status() === 404) {
                return $this->error("No se encontraron datos para el {$this->typeLabel($type)}: {$number}");
            }

            if ($response->status() === 422) {
                return $this->error("Número de documento inválido: {$number}");
            }

            Log::warning("DocumentLookup: respuesta inesperada de API", [
                'type'   => $type,
                'number' => $number,
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            return $this->error('Error al consultar la API. Intente nuevamente.');
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error("DocumentLookup: error de conexión", [
                'type'    => $type,
                'number'  => $number,
                'message' => $e->getMessage(),
            ]);

            return $this->error('No se pudo conectar con el servicio de consulta. Verifique su conexión.');
        }
    }

    /**
     * Normalizar la respuesta de la API a un formato estándar.
     */
    protected function normalizeResponse(string $type, array $data): array
    {
        // Decolecta RENIEC - DNI
        // Respuesta: {first_name, first_last_name, second_last_name, full_name, document_number}
        if ($type === 'dni') {
            return [
                'numero'           => $data['document_number'] ?? null,
                'nombre_completo'  => $data['full_name'] ?? null,
                'nombres'          => $data['first_name'] ?? null,
                'apellido_paterno' => $data['first_last_name'] ?? null,
                'apellido_materno' => $data['second_last_name'] ?? null,
            ];
        }

        // Decolecta SUNAT - RUC
        // Respuesta: {razon_social, numero_documento, estado, condicion, direccion, ubigeo, distrito, provincia, departamento, ...}
        return [
            'ruc'                => $data['numero_documento'] ?? null,
            'razon_social'       => $data['razon_social'] ?? null,
            'nombre_comercial'   => $data['nombre_comercial'] ?? null,
            'direccion'          => $data['direccion'] ?? null,
            'estado'             => $data['estado'] ?? null,
            'condicion'          => $data['condicion'] ?? null,
            'departamento'       => $data['departamento'] ?? null,
            'provincia'          => $data['provincia'] ?? null,
            'distrito'           => $data['distrito'] ?? null,
            'ubigeo'             => $data['ubigeo'] ?? null,
            'tipo_contribuyente' => $data['tipo'] ?? null,
        ];
    }

    protected function typeLabel(string $type): string
    {
        return match ($type) {
            'dni' => 'DNI',
            'ruc' => 'RUC',
            default => strtoupper($type),
        };
    }

    /**
     * @return array{success: true, data: array, message: null}
     */
    protected function success(array $data): array
    {
        return ['success' => true, 'data' => $data, 'message' => null];
    }

    /**
     * @return array{success: false, data: null, message: string}
     */
    protected function error(string $message): array
    {
        return ['success' => false, 'data' => null, 'message' => $message];
    }
}
