<?php

namespace Modules\Partners\Services;

use Illuminate\Support\Facades\Schema;
use Modules\Core\Contracts\PatientDirectoryContract;
use Modules\Partners\Models\Persona;

class PatientDirectoryService implements PatientDirectoryContract
{
    public function search(int $tenantId, ?int $groupCompanyId, string $term = '', int $limit = 20): array
    {
        if (!Schema::hasTable('partners_personas') || !Schema::hasTable('partners_tipo_personas')) {
            return [];
        }

        $query = Persona::query()
            ->select([
                'id',
                'tipo_documento',
                'numero_documento',
                'nombres',
                'apellido_paterno',
                'apellido_materno',
                'nombre_completo',
                'email',
                'telefono',
            ])
            ->where('tenant_id', $tenantId)
            ->where('estado', true)
            ->conTipo('paciente');

        if ($groupCompanyId !== null) {
            $query->where(function ($scope) use ($groupCompanyId): void {
                $scope->where('group_company_id', $groupCompanyId)
                    ->orWhereNull('group_company_id');
            });
        }

        $normalizedTerm = trim($term);
        if ($normalizedTerm !== '') {
            $query->where(function ($scope) use ($normalizedTerm): void {
                $like = '%' . str_replace(['\\', '%', '_'], ['\\\\', '\%', '\_'], $normalizedTerm) . '%';

                $scope->where('nombre_completo', 'like', $like)
                    ->orWhere('nombres', 'like', $like)
                    ->orWhere('numero_documento', 'like', $like);
            });
        }

        $patients = $query
            ->orderBy('nombres')
            ->orderBy('apellido_paterno')
            ->limit(max(1, min(50, $limit)))
            ->get();

        return $patients->map(function (Persona $patient): array {
            $fullName = trim((string) ($patient->nombre_completo ?: implode(' ', array_filter([
                $patient->nombres,
                $patient->apellido_paterno,
                $patient->apellido_materno,
            ]))));

            $document = trim(implode(' ', array_filter([
                $patient->tipo_documento,
                $patient->numero_documento,
            ])));

            return [
                'id' => $patient->id,
                'full_name' => $fullName,
                'document' => $document,
                'email' => $patient->email,
                'phone' => $patient->telefono,
                'label' => trim($fullName . ($document !== '' ? " ({$document})" : '')),
            ];
        })->values()->all();
    }

    public function findById(int $tenantId, ?int $groupCompanyId, int $patientId): ?array
    {
        if (!Schema::hasTable('partners_personas') || !Schema::hasTable('partners_tipo_personas')) {
            return null;
        }

        $query = Persona::query()
            ->whereKey($patientId)
            ->where('tenant_id', $tenantId)
            ->where('estado', true)
            ->conTipo('paciente');

        if ($groupCompanyId !== null) {
            $query->where(function ($scope) use ($groupCompanyId): void {
                $scope->where('group_company_id', $groupCompanyId)
                    ->orWhereNull('group_company_id');
            });
        }

        $patient = $query->first();
        if (!$patient) {
            return null;
        }

        $fullName = trim((string) ($patient->nombre_completo ?: implode(' ', array_filter([
            $patient->nombres,
            $patient->apellido_paterno,
            $patient->apellido_materno,
        ]))));

        $document = trim(implode(' ', array_filter([
            $patient->tipo_documento,
            $patient->numero_documento,
        ])));

        return [
            'id' => $patient->id,
            'full_name' => $fullName,
            'document' => $document,
            'email' => $patient->email,
            'phone' => $patient->telefono,
            'label' => trim($fullName . ($document !== '' ? " ({$document})" : '')),
        ];
    }
}
