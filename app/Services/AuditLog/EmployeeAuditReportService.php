<?php

namespace App\Services\AuditLog;

use Carbon\Carbon;
use Illuminate\Support\Collection;

class EmployeeAuditReportService
{
     /**
     * Map merged audit log data to selected column format
     *
     * @param array $data Merged data from auth, navigation, and audit logs
     * @param array $selectedColumns Array of selected column keys
     * @return array Mapped data with standardized column names
     */
    public function mapToSelectedColumns(array $data, array $selectedColumns): array
    {
        if (empty($selectedColumns)) {
            // If no columns selected, return all available columns
            $selectedColumns = [
                'employeeName', 'eventType', 'actionDescription', 'recordId',
                'loginStatus', 'loginTs', 'logoutTs', 'status', 'sessionId',
                'amendedDateTime', 'currentValue', 'device', 'navigationPath',
                'previousValue', 'ipAddress'
            ];
        }

        $mappedData = [];
        $sessionData = []; // Store session-level data for reference

        foreach ($data as $log) {
            $channel = strtolower($log['channel'] ?? '');
            $sessionId = $log['session_id'] ?? $log['sessionId'] ?? 'NO_SESSION';
            
            // Initialize session data if not exists
            if (!isset($sessionData[$sessionId])) {
                $sessionData[$sessionId] = [
                    'loginTs' => null,
                    'logoutTs' => null,
                    'loginStatus' => null,
                    'ipAddress' => null,
                    'device' => null,
                ];
            }

            // Extract employee information
            $employeeName = $log['employeeName'] ?? $log['user_name'] ?? $log['employee_name'] ?? '-';
            $employeeId = $log['employeeId'] ?? $log['employee_id'] ?? $log['user_id'] ?? null;

            // Determine event type based on channel
            $eventType = $this->determineEventType($log, $channel);

            // Update session data for auth logs
            if ($channel === 'auth' || $channel === 'auth_audit') {
                $event = strtolower($log['event'] ?? '');
                if ($event === 'login') {
                    $sessionData[$sessionId]['loginTs'] = $log['date_time'] ?? null;
                    $sessionData[$sessionId]['loginStatus'] = ucfirst($log['status'] ?? 'success');
                } elseif ($event === 'logout') {
                    $sessionData[$sessionId]['logoutTs'] = $log['date_time'] ?? null;
                }
                $sessionData[$sessionId]['ipAddress'] = $log['ipAddress'] ?? $log['ip_address'] ?? null;
                $sessionData[$sessionId]['device'] = $log['deviceInfo'] ?? $log['device'] ?? $log['user_agent'] ?? null;
            }

            // Build mapped row
            $mappedRow = [];

            foreach ($selectedColumns as $column) {
                switch ($column) {
                    case 'employeeName':
                        $mappedRow['employeeName'] = $employeeName;
                        break;

                    case 'eventType':
                        $mappedRow['eventType'] = $eventType;
                        break;

                    case 'actionDescription':
                        if ($channel === 'audit' || $channel === 'audit_audit') {
                            $mappedRow['actionDescription'] = $log['narration'] ?? $log['message'] ?? '-';
                        } elseif ($channel === 'navigation' || $channel === 'navigation_access') {
                            $mappedRow['actionDescription'] = $log['screenAccessed'] ?? $log['navigationPath'] ?? $log['description'] ?? '-';
                        } else {
                            $mappedRow['actionDescription'] = $log['event'] ?? $log['message'] ?? '-';
                        }
                        break;

                    case 'recordId':
                        $mappedRow['recordId'] = $log['doc_code'] ?? $log['transaction_id'] ?? '-';
                        break;

                    case 'loginStatus':
                        $mappedRow['loginStatus'] = $sessionData[$sessionId]['loginStatus'] ?? ($log['status'] ?? 'Success');
                        break;

                    case 'loginTs':
                        $mappedRow['loginTs'] = $sessionData[$sessionId]['loginTs'] ?? '-';
                        break;

                    case 'logoutTs':
                        $mappedRow['logoutTs'] = $sessionData[$sessionId]['logoutTs'] ?? '-';
                        break;

                    case 'status':
                        $mappedRow['status'] = $log['status'] ?? 'Success';
                        break;

                    case 'sessionId':
                        $mappedRow['sessionId'] = $sessionId !== 'NO_SESSION' ? $sessionId : '-';
                        break;

                    case 'amendedDateTime':
                        $mappedRow['amendedDateTime'] = $log['date_time'] ?? $log['amended_at'] ?? '-';
                        break;

                    case 'currentValue':
                        if ($channel === 'audit' || $channel === 'audit_audit') {
                            $dataField = is_string($log['data'] ?? null) ? json_decode($log['data'], true) : ($log['data'] ?? null);
                            $mappedRow['currentValue'] = $dataField ?? '-';
                        } else {
                            $mappedRow['currentValue'] = '-';
                        }
                        break;

                    case 'previousValue':
                        if ($channel === 'audit' || $channel === 'audit_audit') {
                            $dataField = is_string($log['data'] ?? null) ? json_decode($log['data'], true) : ($log['data'] ?? null);
                            $mappedRow['previousValue'] = $dataField ?? '-';
                        } else {
                            $mappedRow['previousValue'] = '-';
                        }
                        break;

                    case 'device':
                        $mappedRow['device'] = $sessionData[$sessionId]['device'] ?? $log['deviceInfo'] ?? $log['device'] ?? $log['user_agent'] ?? '-';
                        break;

                    case 'navigationPath':
                        $mappedRow['navigationPath'] = $log['navigationPath'] ?? $log['screenAccessed'] ?? '-';
                        break;

                    case 'ipAddress':
                        $mappedRow['ipAddress'] = $sessionData[$sessionId]['ipAddress'] ?? $log['ipAddress'] ?? $log['ip_address'] ?? '-';
                        break;

                    default:
                        // For any other column, try to get from log directly
                        $mappedRow[$column] = $log[$column] ?? $log[lcfirst($column)] ?? '-';
                        break;
                }
            }

            $mappedData[] = $mappedRow;
        }

        return $mappedData;
    }

    /**
     * Determine event type from log data
     *
     * @param array $log Log entry
     * @param string $channel Log channel (auth, navigation, audit)
     * @return string Event type
     */
    private function determineEventType(array $log, string $channel): string
    {
        if ($channel === 'auth' || $channel === 'auth_audit') {
            $event = strtolower($log['event'] ?? '');
            if ($event === 'login') {
                return 'login';
            } elseif ($event === 'logout') {
                return 'logout';
            } elseif ($event === 'login_failed') {
                return 'login_failed';
            }
            return 'login_failed';
        }

        if ($channel === 'audit' || $channel === 'audit_audit') {
            $crudType = strtoupper($log['crudType'] ?? '');
            if ($crudType === 'C') {
                return 'audit-create';
            } elseif ($crudType === 'U') {
                return 'audit-update';
            } elseif ($crudType === 'D') {
                return 'audit-delete';
            }
            return 'audit-update';
        }

        if ($channel === 'navigation' || $channel === 'navigation_access') {
            $accessType = strtolower($log['accessType'] ?? $log['access_type'] ?? 'read');
            if ($accessType === 'read' || $accessType === '1') {
                return 'navigation-read';
            } elseif ($accessType === 'create' || $accessType === '2') {
                return 'navigation-create';
            } elseif ($accessType === 'edit' || $accessType === 'update' || $accessType === '3') {
                return 'navigation-edit';
            }
            return 'navigation-read';
        }

        return 'unknown';
    }    
}
