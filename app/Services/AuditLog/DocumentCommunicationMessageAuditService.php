<?php

namespace App\Services\AuditLog;

class DocumentCommunicationMessageAuditService
{
    public static function process($auditData)
    {
        $modifiedData = [];

        $crudType = $auditData['crudType'] ?? '';
        $new = $auditData['newValue'] ?? [];
        $old = $auditData['previosValue'] ?? [];

        $newBody = $new['body'] ?? '';
        $oldBody = $old['body'] ?? '';

        if ($crudType === 'C') {
            $modifiedData[] = [
                'amended_field' => 'comment_body',
                'previous_value' => '',
                'new_value' => (string) $newBody,
            ];
        } elseif ($crudType === 'U') {
            if ((string) $oldBody !== (string) $newBody) {
                $modifiedData[] = [
                    'amended_field' => 'comment_body',
                    'previous_value' => (string) $oldBody,
                    'new_value' => (string) $newBody,
                ];
            }

            if (($old['version'] ?? null) !== ($new['version'] ?? null)) {
                $modifiedData[] = [
                    'amended_field' => 'comment_version',
                    'previous_value' => (string) ($old['version'] ?? ''),
                    'new_value' => (string) ($new['version'] ?? ''),
                ];
            }
        } elseif ($crudType === 'D') {
            $modifiedData[] = [
                'amended_field' => 'comment_body',
                'previous_value' => (string) $oldBody,
                'new_value' => '',
            ];

            $deletedByOld = $old['deleted_by'] ?? '';
            $deletedByNew = $new['deleted_by'] ?? '';
            if ((string) $deletedByOld !== (string) $deletedByNew) {
                $modifiedData[] = [
                    'amended_field' => 'deleted_by',
                    'previous_value' => (string) $deletedByOld,
                    'new_value' => (string) $deletedByNew,
                ];
            }
        }

        return $modifiedData;
    }
}

