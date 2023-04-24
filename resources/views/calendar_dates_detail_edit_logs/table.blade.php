<div class="table-responsive">
    <table class="table" id="calendarDatesDetailEditLogs-table">
        <thead>
            <tr>
                <th>Calendar Date Id</th>
        <th>Company Id</th>
        <th>From Date</th>
        <th>Master Id</th>
        <th>Modify Type</th>
        <th>Ref Log Id</th>
        <th>Tender Id</th>
        <th>To Date</th>
        <th>Version Id</th>
                <th colspan="3">Action</th>
            </tr>
        </thead>
        <tbody>
        @foreach($calendarDatesDetailEditLogs as $calendarDatesDetailEditLog)
            <tr>
                <td>{{ $calendarDatesDetailEditLog->calendar_date_id }}</td>
            <td>{{ $calendarDatesDetailEditLog->company_id }}</td>
            <td>{{ $calendarDatesDetailEditLog->from_date }}</td>
            <td>{{ $calendarDatesDetailEditLog->master_id }}</td>
            <td>{{ $calendarDatesDetailEditLog->modify_type }}</td>
            <td>{{ $calendarDatesDetailEditLog->ref_log_id }}</td>
            <td>{{ $calendarDatesDetailEditLog->tender_id }}</td>
            <td>{{ $calendarDatesDetailEditLog->to_date }}</td>
            <td>{{ $calendarDatesDetailEditLog->version_id }}</td>
                <td>
                    {!! Form::open(['route' => ['calendarDatesDetailEditLogs.destroy', $calendarDatesDetailEditLog->id], 'method' => 'delete']) !!}
                    <div class='btn-group'>
                        <a href="{{ route('calendarDatesDetailEditLogs.show', [$calendarDatesDetailEditLog->id]) }}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                        <a href="{{ route('calendarDatesDetailEditLogs.edit', [$calendarDatesDetailEditLog->id]) }}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                        {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                    </div>
                    {!! Form::close() !!}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
