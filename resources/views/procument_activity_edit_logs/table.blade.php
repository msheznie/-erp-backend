<div class="table-responsive">
    <table class="table" id="procumentActivityEditLogs-table">
        <thead>
            <tr>
                <th>Tender Id</th>
        <th>Category Id</th>
        <th>Company Id</th>
        <th>Version Id</th>
        <th>Modify Type</th>
        <th>Master Id</th>
        <th>Ref Log Id</th>
                <th colspan="3">Action</th>
            </tr>
        </thead>
        <tbody>
        @foreach($procumentActivityEditLogs as $procumentActivityEditLog)
            <tr>
                <td>{{ $procumentActivityEditLog->tender_id }}</td>
            <td>{{ $procumentActivityEditLog->category_id }}</td>
            <td>{{ $procumentActivityEditLog->company_id }}</td>
            <td>{{ $procumentActivityEditLog->version_id }}</td>
            <td>{{ $procumentActivityEditLog->modify_type }}</td>
            <td>{{ $procumentActivityEditLog->master_id }}</td>
            <td>{{ $procumentActivityEditLog->ref_log_id }}</td>
                <td>
                    {!! Form::open(['route' => ['procumentActivityEditLogs.destroy', $procumentActivityEditLog->id], 'method' => 'delete']) !!}
                    <div class='btn-group'>
                        <a href="{{ route('procumentActivityEditLogs.show', [$procumentActivityEditLog->id]) }}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                        <a href="{{ route('procumentActivityEditLogs.edit', [$procumentActivityEditLog->id]) }}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                        {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                    </div>
                    {!! Form::close() !!}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
