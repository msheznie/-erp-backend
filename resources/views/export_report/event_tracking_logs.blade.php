<html>
    <table>
        <thead>
            <tr>
                <td colspan="9"><h1 style="text-align: center;">{{ trans('custom.event_tracking_logs') }}</h1></td>
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
                <th>{{ trans('custom.session_id') }}</th>
                <th>{{ trans('custom.access_type') }}</th>
                <th>{{ trans('custom.doc_code') }}</th>
                <th>{{ trans('custom.amended_date_time') }}</th>
                <th>{{ trans('custom.action_description') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $log)
                <tr>
                    <td>{{ $log['employeeId'] ?? '' }}</td>
                    <td>{{ $log['user_name'] ?? '' }}</td>
                    <td>{{ $log['session_id'] ?? '' }}</td>
                    <td>
                        @if(isset($log['crudType']))
                            @if($log['crudType'] == 'C')
                                {{ trans('custom.create') }}
                            @elseif($log['crudType'] == 'U')
                                {{ trans('custom.updated') }}
                            @elseif($log['crudType'] == 'D')
                                {{ trans('custom.delete') }}
                            @else
                                {{ $log['crudType'] }}
                            @endif
                        @else
                            ''
                        @endif
                    </td>
                    <td>{{ $log['doc_code'] ?? '' }}</td>
                    <td>{{ $log['date_time'] ?? '' }}</td>
                    <td>{{ $log['narration'] ?? '' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</html>
