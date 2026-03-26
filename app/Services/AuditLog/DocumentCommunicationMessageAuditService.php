<?php

namespace App\Services\AuditLog;

use App\Models\Employee;

class DocumentCommunicationMessageAuditService
{
    private static array $employeeNameCache = [];

    private static function resolveEmployeeName($employeeSystemID): string
    {
        $id = $employeeSystemID !== null && $employeeSystemID !== '' ? (int)$employeeSystemID : 0;
        if ($id <= 0) {
            return '';
        }

        if (array_key_exists($id, self::$employeeNameCache)) {
            return (string) self::$employeeNameCache[$id];
        }

        // Most usages store `employeeSystemID` in the audit payload.
        $empName = Employee::where('employeeSystemID', $id)->value('empName');
        $name = $empName !== null && $empName !== '' ? (string) $empName : (string) $id;

        self::$employeeNameCache[$id] = $name;
        return $name;
    }

    public static function process($auditData)
    {
        $modifiedData = [];

        $crudType = $auditData['crudType'] ?? '';
        $new = $auditData['newValue'] ?? [];
        $old = $auditData['previosValue'] ?? [];

        $newBody = $new['body'] ?? '';
        $oldBody = $old['body'] ?? '';

        if ($crudType === 'C') {
            $createdByName = self::resolveEmployeeName($new['authorId'] ?? null);

            if ($createdByName !== '') {
                $modifiedData[] = [
                    'amended_field' => 'Created By',
                    'previous_value' => '',
                    'new_value' => $createdByName,
                ];
            }

            $modifiedData[] = [
                'amended_field' => 'Comment Body',
                'previous_value' => '',
                'new_value' => (string) $newBody,
            ];
        } elseif ($crudType === 'U') {
            if ((string) $oldBody !== (string) $newBody) {
                $modifiedData[] = [
                    'amended_field' => 'Comment Body',
                    'previous_value' => (string) $oldBody,
                    'new_value' => (string) $newBody,
                ];
            }

            $editedByName = self::resolveEmployeeName($new['authorId'] ?? null);
            if ($editedByName !== '') {
                $modifiedData[] = [
                    'amended_field' => 'Edited By',
                    'previous_value' => '',
                    'new_value' => $editedByName,
                ];
            }
        } elseif ($crudType === 'D') {
            $modifiedData[] = [
                'amended_field' => 'Comment Body',
                'previous_value' => (string) $oldBody,
                'new_value' => '',
            ];

            $deletedByOldId = $old['deleted_by'] ?? '';
            $deletedByNewId = $new['deleted_by'] ?? '';
            if ((string) $deletedByOldId !== (string) $deletedByNewId) {
                $modifiedData[] = [
                    'amended_field' => 'Deleted By',
                    'previous_value' => self::resolveEmployeeName($deletedByOldId),
                    'new_value' => self::resolveEmployeeName($deletedByNewId),
                ];
            }
        }

        return $modifiedData;
    }
}

