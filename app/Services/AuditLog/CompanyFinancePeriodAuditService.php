<?php

namespace App\Services\AuditLog;

/**
 * Audit trail for company finance period — only Is Active, Is Current, Is Closed.
 * Stored flags use -1 = Yes (checked), 0 = No (unchecked).
 */
class CompanyFinancePeriodAuditService
{
    /**
     * @param array<string, mixed> $auditData
     * @return array<int, array{amended_field: string, previous_value: string, new_value: string}>
     */
    public static function process($auditData): array
    {
        $modifiedData = [];
        if (($auditData['crudType'] ?? '') !== 'U') {
            return $modifiedData;
        }

        $prev = $auditData['previosValue'] ?? [];
        $new = $auditData['newValue'] ?? [];

        $fields = [
            'isCurrent' => 'finance_period_is_current',
            'isActive' => 'finance_period_is_active',
            'isClosed' => 'finance_period_is_closed',
        ];

        foreach ($fields as $key => $labelKey) {
            $p = $prev[$key] ?? null;
            $n = $new[$key] ?? null;
            $prevStr = self::yn($p);
            $newStr = self::yn($n);
            if ($prevStr !== $newStr) {
                $modifiedData[] = [
                    'amended_field' => $labelKey,
                    'previous_value' => $prevStr,
                    'new_value' => $newStr,
                ];
            }
        }

        return $modifiedData;
    }

    /**
     * -1 = checked (Yes). Anything else (null/0) = No (unopened / unchecked).
     */
    private static function yn($value): string
    {
        return ((int) $value === -1) ? 'yes' : 'no';
    }
}
