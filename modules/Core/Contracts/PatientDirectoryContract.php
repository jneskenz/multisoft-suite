<?php

namespace Modules\Core\Contracts;

interface PatientDirectoryContract
{
    /**
     * Buscar pacientes del contexto actual.
     *
     * @return array<int,array<string,mixed>>
     */
    public function search(int $tenantId, ?int $groupCompanyId, string $term = '', int $limit = 20): array;

    /**
     * Obtener un paciente por id dentro del contexto actual.
     *
     * @return array<string,mixed>|null
     */
    public function findById(int $tenantId, ?int $groupCompanyId, int $patientId): ?array;
}
