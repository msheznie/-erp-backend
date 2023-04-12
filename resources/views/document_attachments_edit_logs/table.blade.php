<div class="table-responsive">
    <table class="table" id="documentAttachmentsEditLogs-table">
        <thead>
            <tr>
                <th>Approvallevelorder</th>
        <th>Attachmentdescription</th>
        <th>Attachmenttype</th>
        <th>Companysystemid</th>
        <th>Docexpirtydate</th>
        <th>Documentid</th>
        <th>Documentsystemcode</th>
        <th>Documentsystemid</th>
        <th>Enveloptype</th>
        <th>Isuploaded</th>
        <th>Master Id</th>
        <th>Modify Type</th>
        <th>Myfilename</th>
        <th>Originalfilename</th>
        <th>Parent Id</th>
        <th>Path</th>
        <th>Pullfromanotherdocument</th>
        <th>Ref Log Id</th>
        <th>Sizeinkbs</th>
                <th colspan="3">Action</th>
            </tr>
        </thead>
        <tbody>
        @foreach($documentAttachmentsEditLogs as $documentAttachmentsEditLog)
            <tr>
                <td>{{ $documentAttachmentsEditLog->approvalLevelOrder }}</td>
            <td>{{ $documentAttachmentsEditLog->attachmentDescription }}</td>
            <td>{{ $documentAttachmentsEditLog->attachmentType }}</td>
            <td>{{ $documentAttachmentsEditLog->companySystemID }}</td>
            <td>{{ $documentAttachmentsEditLog->docExpirtyDate }}</td>
            <td>{{ $documentAttachmentsEditLog->documentID }}</td>
            <td>{{ $documentAttachmentsEditLog->documentSystemCode }}</td>
            <td>{{ $documentAttachmentsEditLog->documentSystemID }}</td>
            <td>{{ $documentAttachmentsEditLog->envelopType }}</td>
            <td>{{ $documentAttachmentsEditLog->isUploaded }}</td>
            <td>{{ $documentAttachmentsEditLog->master_id }}</td>
            <td>{{ $documentAttachmentsEditLog->modify_type }}</td>
            <td>{{ $documentAttachmentsEditLog->myFileName }}</td>
            <td>{{ $documentAttachmentsEditLog->originalFileName }}</td>
            <td>{{ $documentAttachmentsEditLog->parent_id }}</td>
            <td>{{ $documentAttachmentsEditLog->path }}</td>
            <td>{{ $documentAttachmentsEditLog->pullFromAnotherDocument }}</td>
            <td>{{ $documentAttachmentsEditLog->ref_log_id }}</td>
            <td>{{ $documentAttachmentsEditLog->sizeInKbs }}</td>
                <td>
                    {!! Form::open(['route' => ['documentAttachmentsEditLogs.destroy', $documentAttachmentsEditLog->id], 'method' => 'delete']) !!}
                    <div class='btn-group'>
                        <a href="{{ route('documentAttachmentsEditLogs.show', [$documentAttachmentsEditLog->id]) }}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                        <a href="{{ route('documentAttachmentsEditLogs.edit', [$documentAttachmentsEditLog->id]) }}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                        {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                    </div>
                    {!! Form::close() !!}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
