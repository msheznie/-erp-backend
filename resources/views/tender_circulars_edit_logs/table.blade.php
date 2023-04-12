<div class="table-responsive">
    <table class="table" id="tenderCircularsEditLogs-table">
        <thead>
            <tr>
                <th>Attachment Id</th>
        <th>Circular Name</th>
        <th>Company Id</th>
        <th>Description</th>
        <th>Master Id</th>
        <th>Modify Type</th>
        <th>Ref Log Id</th>
        <th>Status</th>
        <th>Tender Id</th>
        <th>Vesion Id</th>
                <th colspan="3">Action</th>
            </tr>
        </thead>
        <tbody>
        @foreach($tenderCircularsEditLogs as $tenderCircularsEditLog)
            <tr>
                <td>{{ $tenderCircularsEditLog->attachment_id }}</td>
            <td>{{ $tenderCircularsEditLog->circular_name }}</td>
            <td>{{ $tenderCircularsEditLog->company_id }}</td>
            <td>{{ $tenderCircularsEditLog->description }}</td>
            <td>{{ $tenderCircularsEditLog->master_id }}</td>
            <td>{{ $tenderCircularsEditLog->modify_type }}</td>
            <td>{{ $tenderCircularsEditLog->ref_log_id }}</td>
            <td>{{ $tenderCircularsEditLog->status }}</td>
            <td>{{ $tenderCircularsEditLog->tender_id }}</td>
            <td>{{ $tenderCircularsEditLog->vesion_id }}</td>
                <td>
                    {!! Form::open(['route' => ['tenderCircularsEditLogs.destroy', $tenderCircularsEditLog->id], 'method' => 'delete']) !!}
                    <div class='btn-group'>
                        <a href="{{ route('tenderCircularsEditLogs.show', [$tenderCircularsEditLog->id]) }}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                        <a href="{{ route('tenderCircularsEditLogs.edit', [$tenderCircularsEditLog->id]) }}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                        {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                    </div>
                    {!! Form::close() !!}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
