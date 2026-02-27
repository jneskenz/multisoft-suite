param(
    [Parameter(Mandatory = $false)]
    [string]$Root = ".",

    [Parameter(Mandatory = $false)]
    [string[]]$Include = @("*.blade.php"),

    [Parameter(Mandatory = $false)]
    [switch]$WhatIf
)

Set-StrictMode -Version Latest
$ErrorActionPreference = "Stop"

function Test-Utf8Bom {
    param(
        [Parameter(Mandatory = $true)]
        [byte[]]$Bytes
    )

    return $Bytes.Length -ge 3 -and $Bytes[0] -eq 0xEF -and $Bytes[1] -eq 0xBB -and $Bytes[2] -eq 0xBF
}

function Remove-Utf8BomBytes {
    param(
        [Parameter(Mandatory = $true)]
        [byte[]]$Bytes
    )

    if (-not (Test-Utf8Bom -Bytes $Bytes)) {
        return $Bytes
    }

    $result = New-Object byte[] ($Bytes.Length - 3)
    [Array]::Copy($Bytes, 3, $result, 0, $result.Length)

    return $result
}

$resolvedRoot = (Resolve-Path -Path $Root).Path

$files = Get-ChildItem -Path $resolvedRoot -Recurse -File | Where-Object {
    $match = $false

    foreach ($pattern in $Include) {
        if ($_.Name -like $pattern) {
            $match = $true
            break
        }
    }

    return $match
}

$total = 0
$fixed = 0

foreach ($file in $files) {
    $total += 1
    $bytes = [System.IO.File]::ReadAllBytes($file.FullName)

    if (-not (Test-Utf8Bom -Bytes $bytes)) {
        continue
    }

    $fixed += 1
    Write-Host "[BOM] $($file.FullName)"

    if (-not $WhatIf.IsPresent) {
        $newBytes = Remove-Utf8BomBytes -Bytes $bytes
        [System.IO.File]::WriteAllBytes($file.FullName, $newBytes)
    }
}

if ($WhatIf.IsPresent) {
    Write-Host "Escaneo completado (WhatIf). Archivos revisados: $total | Con BOM: $fixed"
} else {
    Write-Host "Proceso completado. Archivos revisados: $total | Corregidos: $fixed"
}
