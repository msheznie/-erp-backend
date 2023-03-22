<div class="table-responsive">
    <table class="table" id="documentModifyRequests-table">
        <thead>
            <tr>
                <th>Approved</th>
        <th>Approved By User System Id</th>
        <th>Approved Date</th>
        <th>Companysystemid</th>
        <th>Document Master Id</th>
        <th>Documentsystemcode</th>
        <th>Rejected</th>
        <th>Rejected By User System Id</th>
        <th>Rejected Date</th>
        <th>Requested Date</th>
        <th>Requested Document Master Id</th>
        <th>Requested Employeesystemid</th>
        <th>Rolllevforapp Curr</th>
        <th>Status</th>
        <th>Type</th>
        <th>Version</th>
                <th colspan="3">Action</th>
            </tr>
        </thead>
        <tbody>
        @foreach($documentModifyRequests as $documentModifyRequest)
            <tr>
                <td>{{ $documentModifyRequest->approved }}</td>
            <td>{{ $documentModifyRequest->approved_by_user_system_id }}</td>
            <td>{{ $documentModifyRequest->approved_date }}</td>
            <td>{{ $documentModifyRequest->companySystemID }}</td>
            <td>{{ $documentModifyRequest->document_master_id }}</td>
            <td>{{ $documentModifyRequest->documentSystemCode }}</td>
            <td>{{ $documentModifyRequest->rejected }}</td>
            <td>{{ $documentModifyRequest->rejected_by_user_system_id }}</td>
            <td>{{ $documentModifyRequest->rejected_date }}</td>
            <td>{{ $documentModifyRequest->requested_date }}</td>
            <td>{{ $documentModifyRequest->requested_document_master_id }}</td>
            <td>{{ $documentModifyRequest->requested_employeeSystemID }}</td>
            <td>{{ $documentModifyRequest->RollLevForApp_curr }}</td>
            <td>{{ $documentModifyRequest->status }}</td>
            <td>{{ $documentModifyRequest->type }}</td>
            <td>{{ $documentModifyRequest->version }}</td>
                <td>
                    {!! Form::open(['route' => ['documentModifyRequests.destroy', $documentModifyRequest->id], 'method' => 'delete']) !!}
                    <div class='btn-group'>
                        <a href="{{ route('documentModifyRequests.show', [$documentModifyRequest->id]) }}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                        <a href="{{ route('documentModifyRequests.edit', [$documentModifyRequest->id]) }}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                        {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                    </div>
                    {!! Form::close() !!}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
