<?php

namespace App\Services\AuditLog;

use Carbon\Carbon;
use Illuminate\Support\Collection;

class EmployeeAuditReportService
{
    public function generate(
        array $authLogs,
        array $navLogs,
        array $auditLogs,
        array $filters
    ): Collection {

        $allowedEvents = [
            'login', 'logout', 'login_failed',
            'navigation-read', 'navigation-create', 'navigation-edit',
            'audit-create', 'audit-update', 'audit-delete'
        ];

        $combined = [];

        foreach (array_merge($authLogs, $navLogs, $auditLogs) as $log) {
               
            $sid = $log['session_id'] ?? 'NO_SESSION';
            

            if (!isset($combined[$sid])) {
                $combined[$sid] = [
                    'company' => null,
                    'employeeId' => $log['employeeId'] ?? null,
                    'employeeName' => $log['employeeName'] ?? null,
                    'sessionId' => $sid,
                    'loginTs' => null,
                    'logoutTs' => null,
                    'loginStatus' => null,
                    'eventType' => [],
                    'actionDescription' => [],
                    'recordId' => [],
                    'navigationPath' => [],
                    'screenAccessed' => [],
                    'previousValue' => [],
                    'currentValue' => [],
                    'status' => 'Success',
                    'ipAddress' => $log['ipAddress'] ?? $log['ip_address'] ?? null,
                    'device' => $log['deviceInfo'] ?? $log['device'] ?? null,
                    'amendedDateTime' => $log['amended_at'] ?? $log['date_time'] ?? null,
                ];
            }
            

            $eventType = $this->resolveEventType($log, $combined[$sid]);

            if (!in_array($eventType, $allowedEvents)) {
                $eventType = 'login_failed';
            }
            
            if (($log['channel'] ?? '') === 'navigation' && !empty($log['navigationPath'])) {
                $combined[$sid]['screenAccessed'][] = $log['navigationPath'];
            }

            $combined[$sid]['eventType'][] = $eventType;

            if (($log['channel'] ?? '') === 'audit') {

                $this->push(
                    $combined[$sid]['actionDescription'],
                    $log['narration'] ?? null
                );

                $this->push(
                    $combined[$sid]['recordId'],
                    $log['doc_code'] ?? null
                );

                foreach ($log['data'] ?? [] as $change) {
                    $this->push(
                        $combined[$sid]['previousValue'],
                        $change->previous_value ?? null
                    );

                    $this->push(
                        $combined[$sid]['currentValue'],
                        $change->new_value ?? null
                    );
                }
            }


            $this->push($combined[$sid]['navigationPath'], $log['navigationPath'] ?? null);


            if (($log['status'] ?? null) === 'Failure') {
                $combined[$sid]['status'] = 'Failure';
            } elseif (($log['status'] ?? null) === 'Warning'
                && $combined[$sid]['status'] !== 'Failure') {
                $combined[$sid]['status'] = 'Warning';
            }
            
        }
        

        $filtered = $this->applyFilters(collect($combined), $filters);

        $filtered = $filtered->map(function ($row) {
            if (!empty($row['screenAccessed'])) {
                foreach ($row['screenAccessed'] as $screen) {
                    $row['actionDescription'][] = $screen;
                }
            }
            return $row;
        });

        
        if (!empty($filters['selectedColumns'])) {
            $filtered = $filtered->map(function ($row) use ($filters) {
                return collect($row)
                    ->only($filters['selectedColumns'])
                    ->all();
            });
        }
        

        return $filtered->values();
    }

    private function resolveEventType(array $log, array &$session): string
    {
        $channel = strtolower($log['channel'] ?? '');

        if ($channel === 'auth') {
            $event = strtolower($log['event'] ?? '');

            if ($event === 'login') {
                $session['loginTs'] = $log['date_time'] ?? null;
                $session['loginStatus'] = ucfirst($log['status'] ?? 'success');
                return 'login';
            }

            if ($event === 'logout') {
                $session['logoutTs'] = $log['date_time'] ?? null;
                return 'logout';
            }

            if ($event === 'login_failed') {
                return 'login_failed';
            }

            return 'login_failed';
        }

        if ($channel === 'audit') {
            return [
                'C' => 'audit-create',
                'U' => 'audit-update',
                'D' => 'audit-delete',
            ][$log['crudType'] ?? ''] ?? 'audit-delete';
        }


        if ($channel === 'navigation') {
            if (!empty($log['company']) && $session['company'] === null) {
                $session['company'] = $log['company'];
            }
            $accessType = strtolower($log['accessType'] ?? $log['access_type'] ?? 'read');
            $accessMap = [
                'read' => 'navigation-read',
                'create' => 'navigation-create',
                'edit' => 'navigation-edit',
                'update' => 'navigation-edit',
            ];
            return $accessMap[$accessType] ?? 'navigation-read';
        }

        return 'login_failed';
    }

    private function applyFilters(Collection $rows, array $filters): Collection
    {   
        
        return $rows->filter(function ($row) use ($filters) {
            
           
            if (!empty($filters['employees']) &&
                !in_array($row['employeeId'], $filters['employees'])) {
                return false;
            }

            
            if (!empty($filters['eventTypes'])) {

                $sessionEvents = collect($row['eventType']);

                $filterEvents = $filters['eventTypes'];

                if (!$sessionEvents->intersect($filterEvents)->count()) {
                    return false;
                }
            }

            if (!empty($filters['screens']) && !empty($row['screenAccessed'])) {
                if (!collect($row['screenAccessed'])
                    ->intersect($filters['screens'])
                    ->count()) {
                    return false;
                }
            }
            
            return true;
        });
    }

    private function push(array &$target, $value): void
    {
        if (!empty($value)) {
            $target[] = $value;
        }
    }

}
