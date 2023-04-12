<div class="table-responsive">
    <table class="table" id="scheduleBidFormatDetailsLogs-table">
        <thead>
            <tr>
                <th>Bid Format Detail Id</th>
        <th>Bid Master Id</th>
        <th>Company Id</th>
        <th>Master Id</th>
        <th>Modify Type</th>
        <th>Red Log Id</th>
        <th>Schedule Id</th>
        <th>Tender Edit Version Id</th>
        <th>Value</th>
                <th colspan="3">Action</th>
            </tr>
        </thead>
        <tbody>
        @foreach($scheduleBidFormatDetailsLogs as $scheduleBidFormatDetailsLog)
            <tr>
                <td>{{ $scheduleBidFormatDetailsLog->bid_format_detail_id }}</td>
            <td>{{ $scheduleBidFormatDetailsLog->bid_master_id }}</td>
            <td>{{ $scheduleBidFormatDetailsLog->company_id }}</td>
            <td>{{ $scheduleBidFormatDetailsLog->master_id }}</td>
            <td>{{ $scheduleBidFormatDetailsLog->modify_type }}</td>
            <td>{{ $scheduleBidFormatDetailsLog->red_log_id }}</td>
            <td>{{ $scheduleBidFormatDetailsLog->schedule_id }}</td>
            <td>{{ $scheduleBidFormatDetailsLog->tender_edit_version_id }}</td>
            <td>{{ $scheduleBidFormatDetailsLog->value }}</td>
                <td>
                    {!! Form::open(['route' => ['scheduleBidFormatDetailsLogs.destroy', $scheduleBidFormatDetailsLog->id], 'method' => 'delete']) !!}
                    <div class='btn-group'>
                        <a href="{{ route('scheduleBidFormatDetailsLogs.show', [$scheduleBidFormatDetailsLog->id]) }}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                        <a href="{{ route('scheduleBidFormatDetailsLogs.edit', [$scheduleBidFormatDetailsLog->id]) }}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                        {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                    </div>
                    {!! Form::close() !!}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
