<table class="table table-responsive" id="companyDocumentAttachments-table">
    <thead>
        <tr>
            <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Documentsystemid</th>
        <th>Documentid</th>
        <th>Docrefnumber</th>
        <th>Isattachmentyn</th>
        <th>Sendemailyn</th>
        <th>Codegeneratorformat</th>
        <th>Isamountapproval</th>
        <th>Isservicelineapproval</th>
        <th>Blockyn</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($companyDocumentAttachments as $companyDocumentAttachment)
        <tr>
            <td>{!! $companyDocumentAttachment->companySystemID !!}</td>
            <td>{!! $companyDocumentAttachment->companyID !!}</td>
            <td>{!! $companyDocumentAttachment->documentSystemID !!}</td>
            <td>{!! $companyDocumentAttachment->documentID !!}</td>
            <td>{!! $companyDocumentAttachment->docRefNumber !!}</td>
            <td>{!! $companyDocumentAttachment->isAttachmentYN !!}</td>
            <td>{!! $companyDocumentAttachment->sendEmailYN !!}</td>
            <td>{!! $companyDocumentAttachment->codeGeneratorFormat !!}</td>
            <td>{!! $companyDocumentAttachment->isAmountApproval !!}</td>
            <td>{!! $companyDocumentAttachment->isServiceLineApproval !!}</td>
            <td>{!! $companyDocumentAttachment->blockYN !!}</td>
            <td>{!! $companyDocumentAttachment->timeStamp !!}</td>
            <td>
                {!! Form::open(['route' => ['companyDocumentAttachments.destroy', $companyDocumentAttachment->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('companyDocumentAttachments.show', [$companyDocumentAttachment->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('companyDocumentAttachments.edit', [$companyDocumentAttachment->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>