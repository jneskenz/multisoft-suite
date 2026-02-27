<?php

namespace Modules\Core\Services;

use Modules\Core\Contracts\PatientDirectoryContract;

class NullPatientDirectoryService implements PatientDirectoryContract
{
    public function search(int $tenantId, ?int $groupCompanyId, string $term = '', int $limit = 20): array
    {
        return [];
    }

    public function findById(int $tenantId, ?int $groupCompanyId, int $patientId): ?array
    {
        return null;
    }
}
