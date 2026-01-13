@php
    $selectedColumns = $selectedColumns ?? [];
    $includeAll = empty($selectedColumns);
    $getLabel = function($key) {
        $trans = trans('custom.' . $key);
        if (strpos($trans, 'custom.') === 0) {
            return ucwords(str_replace('_', ' ', $key));
        }
        return $trans;
    };

    $columnMap = [
        'employeeName' => ['label' => $getLabel('employee_name'), 'field' => 'employeeName'],
        'sessionId' => ['label' => $getLabel('session_id'), 'field' => 'sessionId'],
        'eventType' => ['label' => $getLabel('event_type'), 'field' => 'eventType'],
        'actionDescription' => ['label' => $getLabel('action_description'), 'field' => 'actionDescription'],
        'navigationPath' => ['label' => $getLabel('navigation_path'), 'field' => 'navigationPath'],
        'recordId' => ['label' => $getLabel('record_id'), 'field' => 'recordId'],
        'loginStatus' => ['label' => $getLabel('login_status'), 'field' => 'loginStatus'],
        'loginTs' => ['label' => $getLabel('login_timestamp'), 'field' => 'loginTs'],
        'logoutTs' => ['label' => $getLabel('logout_timestamp'), 'field' => 'logoutTs'],
        'status' => ['label' => $getLabel('status'), 'field' => 'status'],
        'amendedDateTime' => ['label' => $getLabel('amended_date_time'), 'field' => 'amendedDateTime'],
        'previousValue' => ['label' => $getLabel('previous_value'), 'field' => 'previousValue'],
        'currentValue' => ['label' => $getLabel('current_value'), 'field' => 'currentValue'],
        'ipAddress' => ['label' => $getLabel('ip_address'), 'field' => 'ipAddress'],
        'device' => ['label' => $getLabel('device'), 'field' => 'device'],
    ];

    
    $columnsMultiLine = ['previousValue', 'currentValue']; 

    $columnsToShow = $includeAll ? array_keys($columnMap) : array_filter($selectedColumns, function($col) {
        return $col !== 'company';
    });

    $colspan = count($columnsToShow);
@endphp

<html>
    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
            <tr>
                @php
                    $reportTitle = trans('custom.employee_activity_audit_report');
                    if (strpos($reportTitle, 'custom.') === 0) {
                        $reportTitle = 'Employee Activity Audit Report';
                    }
                    $fromLabel = trans('custom.from');
                    if (strpos($fromLabel, 'custom.') === 0) {
                        $fromLabel = 'From';
                    }
                    $toLabel = trans('custom.to');
                    if (strpos($toLabel, 'custom.') === 0) {
                        $toLabel = 'To';
                    }
                @endphp
                <td colspan="{{ $colspan }}" align="center"><b>{{ $reportTitle }}</b></td>
            </tr>
            @if(isset($fromDate) && isset($toDate))
            <tr>
                <td colspan="{{ $colspan }}" align="center">
                    <b>{{ $fromLabel }} {{ \App\helper\Helper::dateFormat($fromDate) }} {{ $toLabel }} {{ \App\helper\Helper::dateFormat($toDate) }}</b>
                </td>
            </tr>
            @endif
            <tr>
                @foreach($columnsToShow as $colKey)
                    @if(isset($columnMap[$colKey]))
                        <th><b>{{ $columnMap[$colKey]['label'] }}</b></th>
                    @endif
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($data as $record)
                <tr>
                    @foreach($columnsToShow as $colKey)
                        @if(isset($columnMap[$colKey]))
                            @php
                                $fieldName = $columnMap[$colKey]['field'];
                                $defaultValue = in_array($fieldName, ['employee', 'sessionId']) ? '' : ($fieldName === 'loginTs' || $fieldName === 'logoutTs' ? 'N/A' : '-');
                                $fieldValue = $record[$fieldName] ?? null;

                                if (is_array($fieldValue)) {
                                    $filteredValues = array_filter(array_map('strval', $fieldValue), function($val) {
                                        return trim($val) !== '' && trim($val) !== '-' && trim($val) !== '[]';
                                    });

                                    if (!empty($filteredValues)) {
                                        if (in_array($fieldName, $columnsMultiLine)) {
                                            
                                            $fieldValue = implode('<br>', $filteredValues);
                                        } else {
                                            
                                            $fieldValue = implode(', ', $filteredValues);
                                        }
                                    } else {
                                        $fieldValue = $defaultValue;
                                    }
                                } else {
                                    $fieldValue = (string)($fieldValue ?? $defaultValue);
                                    if (trim($fieldValue) === '' || trim($fieldValue) === '[]' || trim($fieldValue) === 'null') {
                                        $fieldValue = $defaultValue;
                                    }
                                }
                            @endphp
                            <td>{!! $fieldValue !!}</td>
                        @endif
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</html>
