<html>
    <table>
        <thead>
            <tr>
                <td colspan="2"><h1 style="text-align: center;">{{ trans('custom.user_audit_logs') }}</h1></td>
                
            </tr>
            @if(isset($fromDate) && isset($toDate))
            <tr>
                <td colspan="2"><h4 style="text-align: center;">{{trans('custom.from')}} {{ \App\helper\Helper::dateFormat($fromDate) }} {{trans('custom.to')}} {{ \App\helper\Helper::dateFormat($toDate) }}</h4></td>
            </tr>
            @endif
        </thead>
        <thead>
            <tr></tr>
            <tr></tr>
            <tr>
                <th>{{ trans('custom.emp_id') }}</th>
                <th>{{ trans('custom.employee_name') }}</th>
                <th>{{ trans('custom.role') }}</th>
                <th>{{ trans('custom.event') }}</th>
                <th>{{ trans('custom.time_stamp') }}</th>
                <th>{{ trans('custom.session_id') }}</th>
                <th>{{ trans('custom.ip_address') }}</th>
                <th>{{ trans('custom.device') }}</th>
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

