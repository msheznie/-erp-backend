<?php

namespace App\Exports;

/**
 * User audit logs Excel export. Uses the common BladeViewExcelExport with
 * view 'export_report.user_audit_logs' and standard title/header layout.
 */
class UserAuditLogsExport extends BladeViewExcelExport
{
    /**
     * @param  array{data: array, fromDate: mixed, toDate: mixed}  $reportData
     */
    public function __construct(array $reportData, string $fontFamily = 'Calibri', bool $isRtl = false)
    {
        parent::__construct(
            'export_report.user_audit_logs',
            $reportData,
            $fontFamily,
            $isRtl,
            'I',
            4,
            5,
            11
        );
    }
}
