<html>
    <table>
        <thead>
            <tr>
                <td colspan="9"><h1 style="text-align: center;">{{ trans('custom.navigation_access_logs') }}</h1></td>
                
            </tr>
            @if(isset($fromDate) && isset($toDate))
            <tr>
                <td colspan="9"><h4 style="text-align: center;">{{trans('custom.from')}} {{ \App\helper\Helper::dateFormat($fromDate) }} {{trans('custom.to')}} {{ \App\helper\Helper::dateFormat($toDate) }}</h4></td>
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
                <th>{{ trans('custom.screen_accessed') }}</th>
                <th>{{ trans('custom.navigation_path') }}</th>
                <th>{{ trans('custom.session_id') }}</th>
                <th>{{ trans('custom.access_type') }}</th>
                <th>{{ trans('custom.time_stamp') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $log)
                <tr>
                    <td>{{ $log['employeeId'] ?? '' }}</td>
                    <td>{{ $log['employeeName'] ?? '' }}</td>
                    <td>{{ $log['role'] ?? '' }}</td>
                    <td>{{ $log['screenAccessed'] ?? '' }}</td>
                    <td>{{ $log['navigationPath'] ?? '' }}</td>
                    <td>{{ $log['session_id'] ?? '' }}</td>
                    <td>{{ $log['accessType'] ?? '' }}</td>
                    <td>{{ $log['date_time'] ?? '' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</html>

