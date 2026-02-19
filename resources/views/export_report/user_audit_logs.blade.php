<html>
    <table>
        <thead>
            <tr>
                <td colspan="9" align="center" style="font-size: 16pt; font-weight: bold; text-align: center;">{{ trans('custom.user_audit_logs') }}</td>
            </tr>
            @if(isset($fromDate) && isset($toDate))
            <tr>
                <td colspan="9" align="center" style="font-size: 12pt; text-align: center;">{{ trans('custom.from') }} {{ \App\helper\Helper::dateFormat($fromDate) }} {{ trans('custom.to') }} {{ \App\helper\Helper::dateFormat($toDate) }}</td>
            </tr>
            @endif
        </thead>
        <thead>
            <tr></tr>
            <tr></tr>
            <tr>
                <th align="left" style="font-weight: bold;">{{ trans('custom.emp_id') }}</th>
                <th align="left" style="font-weight: bold;">{{ trans('custom.employee_name') }}</th>
                <th align="left" style="font-weight: bold;">{{ trans('custom.role') }}</th>
                <th align="left" style="font-weight: bold;">{{ trans('custom.event') }}</th>
                <th align="left" style="font-weight: bold;">{{ trans('custom.time_stamp') }}</th>
                <th align="left" style="font-weight: bold;">{{ trans('custom.session_id') }}</th>
                <th align="left" style="font-weight: bold;">{{ trans('custom.ip_address') }}</th>
                <th align="left" style="font-weight: bold;">{{ trans('custom.device') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $log)
                <tr>
                    <td>{{ $log['employeeId'] ?? '' }}</td>
                    <td>{{ $log['employeeName'] ?? '' }}</td>
                    <td>{{ $log['role'] ?? '' }}</td>
                    <td>{{ $log['event'] ?? '' }}</td>
                    <td>{{ $log['date_time'] ?? '' }}</td>
                    <td>{{ $log['session_id'] ?? '' }}</td>
                    <td>{{ $log['ipAddress'] ?? '' }}</td>
                    <td>{{ $log['deviceInfo'] ?? '' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</html>

