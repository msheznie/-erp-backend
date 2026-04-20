<?php

namespace App\Services\AuditLog;

use App\Services\VictoriaLogsService;
use Carbon\Carbon;

class EmployeeAuditReportService
{
    public function __construct(
        private VictoriaLogsService $victoriaLogsService
    ) {}

    /**
     * @param  array<string, mixed>  $input  Validated request data (employeeIds, eventTypes, navigationMenuIds, fromDate, toDate, companyId, tenant_uuid, locale, start, length)
     * @return array{total: int, rows: array<int, array<string, mixed>>}
     */
    public function getPaginatedUnifiedRows(array $input): array
    {
        $merged = $this->buildMergedRows($input);
        $total = count($merged);
        $start = (int) ($input['start'] ?? 0);
        $length = (int) ($input['length'] ?? 20);
        $page = array_slice($merged, $start, $length);

        return [
            'total' => $total,
            'rows' => $page,
        ];
    }

    /**
     * @param  array<string, mixed>  $input
     * @return array<int, array<string, mixed>>
     */
    public function buildMergedRows(array $input): array
    {
        $tenantUuid = (string) ($input['tenant_uuid'] ?? 'local');
        $locale = (string) ($input['locale'] ?? 'en');
        $companyId = (int) $input['companyId'];
        $fromDate = (string) $input['fromDate'];
        $toDate = (string) $input['toDate'];
        /** @var array<int, string> $employeeIds */
        $employeeIds = array_map('strval', $input['employeeIds'] ?? []);
        /** @var array<int, string> $eventTypes */
        $eventTypes = $input['eventTypes'] ?? [];
        /** @var array<int, int> $navigationMenuIds */
        $navigationMenuIds = array_map('intval', $input['navigationMenuIds'] ?? []);

        $needsAuth = $this->needsChannel($eventTypes, ['login', 'logout', 'login_failed']);
        $needsNav = $this->needsChannel($eventTypes, ['navigation-read', 'navigation-create', 'navigation-edit']);
        $needsAudit = $this->needsChannel($eventTypes, ['audit-create', 'audit-update', 'audit-delete']);

        $singleEmployee = count($employeeIds) === 1 ? $employeeIds[0] : null;
        $limit = (int) (config('victorialogs.limit', 10000));

        $rows = [];

        if ($needsAuth) {
            $params = [
                'tenant_uuid' => $tenantUuid,
                'locale' => $locale,
                'fromDate' => $fromDate,
                'toDate' => $toDate,
                'employeeId' => $singleEmployee,
                'event' => null,
                'start' => 0,
                'length' => $limit,
                'search' => [],
            ];
            $authData = $this->victoriaLogsService->getUserAuditLogs($params)['data'] ?? [];
            foreach ($authData as $raw) {
                $key = $this->mapAuthEventToKey($raw['event'] ?? '', $locale);
                if ($key === null || ! in_array($key, $eventTypes, true)) {
                    continue;
                }
                if (! $this->employeeMatches($raw, $employeeIds)) {
                    continue;
                }
                $rows[] = $this->normalizeAuthRow($raw, $key, $locale);
            }
        }

        if ($needsNav) {
            $params = [
                'tenant_uuid' => $tenantUuid,
                'locale' => $locale,
                'fromDate' => $fromDate,
                'toDate' => $toDate,
                'companyId' => $companyId,
                'employeeId' => $singleEmployee,
                'accessType' => null,
                'start' => 0,
                'length' => $limit,
                'search' => [],
            ];
            $navData = $this->victoriaLogsService->getNavigationAccessLogs($params)['data'] ?? [];
            foreach ($navData as $raw) {
                $key = $this->mapNavigationEventToKey($raw['accessType'] ?? '', $locale);
                if ($key === null || ! in_array($key, $eventTypes, true)) {
                    continue;
                }
                if (! $this->employeeMatches($raw, $employeeIds)) {
                    continue;
                }
                if (! $this->navigationMenuMatches($raw, $navigationMenuIds)) {
                    continue;
                }
                $rows[] = $this->normalizeNavigationRow($raw, $key, $locale);
            }
        }

        if ($needsAudit) {
            $params = [
                'tenant_uuid' => $tenantUuid,
                'locale' => $locale,
                'companyId' => $companyId,
                'isFromTracking' => true,
                'fromDate' => $fromDate,
                'toDate' => $toDate,
                'employeeId' => $singleEmployee,
                'accessType' => null,
                'start' => 0,
                'length' => $limit,
                'search' => [],
            ];
            $auditData = $this->victoriaLogsService->getAuditLogs($params)['data'] ?? [];
            foreach ($auditData as $raw) {
                $key = $this->mapAuditEventToKey($raw['crudType'] ?? '');
                if ($key === null || ! in_array($key, $eventTypes, true)) {
                    continue;
                }
                if (! $this->employeeMatches($raw, $employeeIds)) {
                    continue;
                }
                $rows[] = $this->normalizeAuditRow($raw, $key);
            }
        }

        usort($rows, function (array $a, array $b): int {
            return ($b['_sortTs'] ?? 0) <=> ($a['_sortTs'] ?? 0);
        });

        foreach ($rows as $i => $row) {
            unset($rows[$i]['_sortTs']);
        }

        return array_values($rows);
    }

    /**
     * @param  array<int, string>  $eventTypes
     * @param  array<int, string>  $keys
     */
    private function needsChannel(array $eventTypes, array $keys): bool
    {
        foreach ($keys as $k) {
            if (in_array($k, $eventTypes, true)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  array<int, string>  $employeeIds
     */
    private function employeeMatches(array $raw, array $employeeIds): bool
    {
        if ($employeeIds === []) {
            return false;
        }
        $eid = (string) ($raw['employeeId'] ?? '');

        return in_array($eid, $employeeIds, true);
    }

    /**
     * @param  array<int, int>  $navigationMenuIds
     */
    private function navigationMenuMatches(array $raw, array $navigationMenuIds): bool
    {
        if ($navigationMenuIds === []) {
            return true;
        }
        $mid = $raw['navigationMenuID'] ?? $raw['navigationMenuId'] ?? null;
        if ($mid === null || $mid === '') {
            return false;
        }

        return in_array((int) $mid, $navigationMenuIds, true);
    }

    private function mapAuthEventToKey(string $eventLabel, string $locale): ?string
    {
        app()->setLocale($locale);
        $normalized = trim($eventLabel);

        $map = [
            trans('audit.login') => 'login',
            trans('audit.logout') => 'logout',
            trans('audit.login_failed') => 'login_failed',
            'Login' => 'login',
            'Logout' => 'logout',
            'Login Failed' => 'login_failed',
            'login' => 'login',
            'logout' => 'logout',
            'login_failed' => 'login_failed',
        ];

        return $map[$normalized] ?? null;
    }

    private function mapNavigationEventToKey(string $accessLabel, string $locale): ?string
    {
        app()->setLocale($locale);
        $normalized = trim($accessLabel);

        $read = trans('audit.read');
        $create = trans('audit.create');
        $edit = trans('audit.edit');

        $map = [
            $read => 'navigation-read',
            $create => 'navigation-create',
            $edit => 'navigation-edit',
            'Read' => 'navigation-read',
            'Create' => 'navigation-create',
            'Edit' => 'navigation-edit',
            'read' => 'navigation-read',
            'create' => 'navigation-create',
            'edit' => 'navigation-edit',
        ];

        return $map[$normalized] ?? null;
    }

    private function mapAuditEventToKey(string $crud): ?string
    {
        return match (strtoupper($crud)) {
            'C' => 'audit-create',
            'U' => 'audit-update',
            'D' => 'audit-delete',
            default => null,
        };
    }

    /**
     * @return array<string, mixed>
     */
    private function normalizeAuthRow(array $raw, string $eventKey, string $locale): array
    {
        app()->setLocale($locale);
        $tsRaw = $raw['date_time'] ?? '';
        $sortTs = $this->resolveSortTimestamp($tsRaw);

        $eventLabel = match ($eventKey) {
            'login' => trans('audit.login'),
            'logout' => trans('audit.logout'),
            'login_failed' => trans('audit.login_failed'),
            default => $eventKey,
        };

        $loginTs = '';
        $logoutTs = '';
        if ($eventKey === 'login') {
            $loginTs = (string) $tsRaw;
        } elseif ($eventKey === 'logout') {
            $logoutTs = (string) $tsRaw;
        }

        return [
            'employeeName' => (string) ($raw['employeeName'] ?? $raw['employeeId'] ?? ''),
            'eventType' => $eventLabel,
            'actionDescription' => (string) ($raw['event'] ?? ''),
            'recordId' => '',
            'loginStatus' => (string) ($raw['status'] ?? ''),
            'loginTs' => $loginTs,
            'logoutTs' => $logoutTs,
            'status' => (string) ($raw['status'] ?? ''),
            'sessionId' => (string) ($raw['session_id'] ?? ''),
            'amendedDateTime' => (string) $tsRaw,
            'previousValue' => '',
            'currentValue' => '',
            'ipAddress' => (string) ($raw['ipAddress'] ?? ''),
            'device' => (string) ($raw['deviceInfo'] ?? ''),
            'navigationPath' => '',
            '_sortTs' => $sortTs,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function normalizeNavigationRow(array $raw, string $eventKey, string $locale): array
    {
        app()->setLocale($locale);
        $tsRaw = $raw['date_time'] ?? '';
        $sortTs = $this->resolveSortTimestamp($tsRaw);

        $eventLabel = match ($eventKey) {
            'navigation-read' => trans('audit.read'),
            'navigation-create' => trans('audit.create'),
            'navigation-edit' => trans('audit.edit'),
            default => $eventKey,
        };

        return [
            'employeeName' => (string) ($raw['employeeName'] ?? $raw['employeeId'] ?? ''),
            'eventType' => $eventLabel,
            'actionDescription' => (string) ($raw['screenAccessed'] ?? ''),
            'recordId' => '',
            'loginStatus' => '',
            'loginTs' => '',
            'logoutTs' => '',
            'status' => '',
            'sessionId' => (string) ($raw['session_id'] ?? ''),
            'amendedDateTime' => (string) $tsRaw,
            'previousValue' => '',
            'currentValue' => '',
            'ipAddress' => (string) ($raw['ipAddress'] ?? ''),
            'device' => (string) ($raw['deviceInfo'] ?? ''),
            'navigationPath' => (string) ($raw['navigationPath'] ?? ''),
            '_sortTs' => $sortTs,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function normalizeAuditRow(array $raw, string $eventKey): array
    {
        $locale = app()->getLocale() ?: 'en';
        app()->setLocale($locale);

        $tsRaw = $raw['date_time'] ?? '';
        $sortTs = $this->resolveSortTimestamp($tsRaw);

        $eventLabel = match ($eventKey) {
            'audit-create' => trans('audit.create'),
            'audit-update' => trans('audit.edit'),
            'audit-delete' => trans('audit.delete'),
            default => $eventKey,
        };

        [$prev, $curr] = $this->extractAuditValuePair($raw);

        return [
            'employeeName' => (string) ($raw['user_name'] ?? $raw['employeeName'] ?? $raw['employeeId'] ?? ''),
            'eventType' => $eventLabel,
            'actionDescription' => (string) ($raw['narration'] ?? ''),
            'recordId' => (string) ($raw['doc_code'] ?? ''),
            'loginStatus' => '',
            'loginTs' => '',
            'logoutTs' => '',
            'status' => (string) ($raw['crudType'] ?? ''),
            'sessionId' => (string) ($raw['session_id'] ?? ''),
            'amendedDateTime' => (string) $tsRaw,
            'previousValue' => $this->stringifyMixed($prev),
            'currentValue' => $this->stringifyMixed($curr),
            'ipAddress' => '',
            'device' => '',
            'navigationPath' => (string) ($raw['navigationPath'] ?? ''),
            '_sortTs' => $sortTs,
        ];
    }

    /**
     * Extract previous/current values from direct fields or nested `data` payloads.
     *
     * @return array{0: mixed, 1: mixed}
     */
    private function extractAuditValuePair(array $raw): array
    {
        $prev = $raw['previosValue'] ?? $raw['previousValue'] ?? null;
        $curr = $raw['newValue'] ?? $raw['currentValue'] ?? null;

        $payload = $raw['data'] ?? null;
        if (is_string($payload)) {
            $decoded = json_decode($payload, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $payload = $decoded;
            }
        }

        if (is_object($payload)) {
            $payload = (array) $payload;
        }

        if (is_array($payload)) {
            // Common Victoria audit shape:
            // data: [{"amended_field":"x","previous_value":"a","new_value":"b"}, ...]
            if ($this->isAmendedFieldArray($payload)) {
                $mappedPrev = [];
                $mappedCurr = [];
                foreach ($payload as $row) {
                    if (! is_array($row)) {
                        continue;
                    }
                    $field = (string) ($row['amended_field'] ?? '');
                    if ($field === '') {
                        continue;
                    }
                    $mappedPrev[$field] = $row['previous_value'] ?? null;
                    $mappedCurr[$field] = $row['new_value'] ?? null;
                }
                if ($prev === null && $mappedPrev !== []) {
                    $prev = $mappedPrev;
                }
                if ($curr === null && $mappedCurr !== []) {
                    $curr = $mappedCurr;
                }
            }

            if ($prev === null) {
                $prev = $payload['previosValue']
                    ?? $payload['previousValue']
                    ?? $payload['oldValue']
                    ?? null;
            }
            if ($curr === null) {
                $curr = $payload['newValue']
                    ?? $payload['currentValue']
                    ?? $payload['newData']
                    ?? null;
            }
        }

        if (is_string($prev)) {
            $decoded = json_decode($prev, true);
            $prev = json_last_error() === JSON_ERROR_NONE ? $decoded : $prev;
        }
        if (is_string($curr)) {
            $decoded = json_decode($curr, true);
            $curr = json_last_error() === JSON_ERROR_NONE ? $decoded : $curr;
        }

        return [$prev, $curr];
    }

    /**
     * Detects array format:
     * [
     *   ['amended_field' => 'x', 'previous_value' => 'a', 'new_value' => 'b'],
     *   ...
     * ]
     */
    private function isAmendedFieldArray(array $payload): bool
    {
        if ($payload === []) {
            return false;
        }

        $first = reset($payload);
        if (! is_array($first)) {
            return false;
        }

        return array_key_exists('amended_field', $first)
            && (array_key_exists('previous_value', $first) || array_key_exists('new_value', $first));
    }

    private function resolveSortTimestamp(mixed $dateTime): int
    {
        if ($dateTime === null || $dateTime === '') {
            return 0;
        }
        try {
            return Carbon::parse($dateTime)->timestamp;
        } catch (\Throwable) {
            return 0;
        }
    }

    private function stringifyMixed(mixed $value): string
    {
        if ($value === null) {
            return '';
        }
        if (is_scalar($value)) {
            return (string) $value;
        }

        return (string) json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    /**
     * @param  array<int, string>  $selectedColumns
     * @return array<int, string>
     */
    public function resolveColumns(array $selectedColumns): array
    {
        $allowed = [
            'employeeName',
            'eventType',
            'actionDescription',
            'recordId',
            'loginStatus',
            'loginTs',
            'logoutTs',
            'status',
            'sessionId',
            'amendedDateTime',
            'previousValue',
            'currentValue',
            'ipAddress',
            'device',
            'navigationPath',
        ];

        $filtered = array_values(array_intersect($selectedColumns, $allowed));

        if ($filtered === []) {
            return ['employeeName', 'eventType', 'amendedDateTime'];
        }

        return $filtered;
    }
}
