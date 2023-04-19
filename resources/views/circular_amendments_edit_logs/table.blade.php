<div class="table-responsive">
    <table class="table" id="circularAmendmentsEditLogs-table">
        <thead>
            <tr>
                <th>Amendment Id</th>
        <th>Circular Id</th>
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
        @foreach($circularAmendmentsEditLogs as $circularAmendmentsEditLog)
            <tr>
                <td>{{ $circularAmendmentsEditLog->amendment_id }}</td>
            <td>{{ $circularAmendmentsEditLog->circular_id }}</td>
            <td>{{ $circularAmendmentsEditLog->master_id }}</td>
            <td>{{ $circularAmendmentsEditLog->modify_type }}</td>
            <td>{{ $circularAmendmentsEditLog->ref_log_id }}</td>
            <td>{{ $circularAmendmentsEditLog->status }}</td>
            <td>{{ $circularAmendmentsEditLog->tender_id }}</td>
            <td>{{ $circularAmendmentsEditLog->vesion_id }}</td>
                <td>
                    {!! Form::open(['route' => ['circularAmendmentsEditLogs.destroy', $circularAmendmentsEditLog->id], 'method' => 'delete']) !!}
                    <div class='btn-group'>
                        <a href="{{ route('circularAmendmentsEditLogs.show', [$circularAmendmentsEditLog->id]) }}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                        <a href="{{ route('circularAmendmentsEditLogs.edit', [$circularAmendmentsEditLog->id]) }}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                        {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                    </div>
                    {!! Form::close() !!}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
