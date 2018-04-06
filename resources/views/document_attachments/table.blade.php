<table class="table table-responsive" id="documentAttachments-table">
    <thead>
        <tr>
            <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Documentsystemid</th>
        <th>Documentid</th>
        <th>Documentsystemcode</th>
        <th>Attachmentdescription</th>
        <th>Originalfilename</th>
        <th>Myfilename</th>
        <th>Docexpirtydate</th>
        <th>Attachmenttype</th>
        <th>Sizeinkbs</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($documentAttachments as $documentAttachments)
        <tr>
            <td>{!! $documentAttachments->companySystemID !!}</td>
            <td>{!! $documentAttachments->companyID !!}</td>
            <td>{!! $documentAttachments->documentSystemID !!}</td>
            <td>{!! $documentAttachments->documentID !!}</td>
            <td>{!! $documentAttachments->documentSystemCode !!}</td>
            <td>{!! $documentAttachments->attachmentDescription !!}</td>
            <td>{!! $documentAttachments->originalFileName !!}</td>
            <td>{!! $documentAttachments->myFileName !!}</td>
            <td>{!! $documentAttachments->docExpirtyDate !!}</td>
            <td>{!! $documentAttachments->attachmentType !!}</td>
            <td>{!! $documentAttachments->sizeInKbs !!}</td>
            <td>{!! $documentAttachments->timeStamp !!}</td>
            <td>
                {!! Form::open(['route' => ['documentAttachments.destroy', $documentAttachments->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('documentAttachments.show', [$documentAttachments->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('documentAttachments.edit', [$documentAttachments->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>