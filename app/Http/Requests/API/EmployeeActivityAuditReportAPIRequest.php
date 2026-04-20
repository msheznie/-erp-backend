<?php

namespace App\Http\Requests\API;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EmployeeActivityAuditReportAPIRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $employeeIds = [];
        if ($this->has('employeeIds') && is_array($this->input('employeeIds'))) {
            foreach ($this->input('employeeIds', []) as $item) {
                if (is_array($item) && isset($item['id'])) {
                    $employeeIds[] = (string) $item['id'];
                } elseif (is_string($item) || is_numeric($item)) {
                    $employeeIds[] = (string) $item;
                }
            }
        }
        if ($this->has('employees') && empty($employeeIds)) {
            foreach ((array) $this->input('employees', []) as $item) {
                if (is_array($item) && isset($item['id'])) {
                    $employeeIds[] = (string) $item['id'];
                } elseif (is_string($item) || is_numeric($item)) {
                    $employeeIds[] = (string) $item;
                }
            }
        }

        $eventTypes = [];
        if ($this->has('eventTypes') && is_array($this->input('eventTypes'))) {
            foreach ($this->input('eventTypes', []) as $item) {
                if (is_array($item) && isset($item['id'])) {
                    $eventTypes[] = (string) $item['id'];
                } elseif (is_string($item)) {
                    $eventTypes[] = $item;
                }
            }
        }

        $navigationMenuIds = [];
        if ($this->has('navigationMenuIds') && is_array($this->input('navigationMenuIds'))) {
            foreach ($this->input('navigationMenuIds', []) as $item) {
                if (is_array($item) && isset($item['id'])) {
                    $navigationMenuIds[] = (int) $item['id'];
                } elseif (is_numeric($item)) {
                    $navigationMenuIds[] = (int) $item;
                }
            }
        }
        if ($this->has('modules') && empty($navigationMenuIds)) {
            foreach ((array) $this->input('modules', []) as $item) {
                if (is_array($item) && isset($item['id'])) {
                    $navigationMenuIds[] = (int) $item['id'];
                } elseif (is_numeric($item)) {
                    $navigationMenuIds[] = (int) $item;
                }
            }
        }

        $columns = $this->input('columns');
        if (is_string($columns)) {
            $decoded = json_decode($columns, true);
            $columns = is_array($decoded) ? $decoded : [];
        }
        if (! is_array($columns)) {
            $columns = [];
        }

        $this->merge([
            'employeeIds' => $employeeIds,
            'eventTypes' => $eventTypes,
            'navigationMenuIds' => $navigationMenuIds,
            'columns' => $columns,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $allowedEvents = [
            'login',
            'logout',
            'login_failed',
            'navigation-read',
            'navigation-create',
            'navigation-edit',
            'audit-create',
            'audit-update',
            'audit-delete',
        ];

        $allowedColumns = [
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

        $employeeRules = ['required', 'array', 'min:1'];
        if ($this->input('reportType') === 'generate') {
            $employeeRules[] = 'max:1';
        }

        return [
            'companyId' => ['required', 'integer', 'min:1'],
            'fromDate' => ['required', 'date'],
            'toDate' => ['required', 'date', 'after_or_equal:fromDate'],
            'reportType' => ['nullable', 'string', Rule::in(['generate', 'export'])],
            'tenant_uuid' => ['nullable', 'string', 'max:128'],
            'employeeIds' => $employeeRules,
            'employeeIds.*' => ['string', 'max:64'],
            'eventTypes' => ['required', 'array', 'min:1'],
            'eventTypes.*' => ['string', Rule::in($allowedEvents)],
            'navigationMenuIds' => ['nullable', 'array'],
            'navigationMenuIds.*' => ['integer', 'min:1'],
            'columns' => ['nullable', 'array'],
            'columns.*' => ['string', Rule::in($allowedColumns)],
            'start' => ['sometimes', 'integer', 'min:0'],
            'length' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ];
    }
}
