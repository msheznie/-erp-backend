<table class="table table-responsive" id="reportTemplateDocuments-table">
    <thead>
        <tr>
            <th>Documentsystemid</th>
        <th>Documentid</th>
        <th>Isactive</th>
        <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Createdusergroup</th>
        <th>Createdusersystemid</th>
        <th>Createduserid</th>
        <th>Createdpcid</th>
        <th>Createddatetime</th>
        <th>Modifiedusersystemid</th>
        <th>Modifieduser</th>
        <th>Modifiedpc</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($reportTemplateDocuments as $reportTemplateDocument)
        <tr>
            <td>{!! $reportTemplateDocument->documentSystemID !!}</td>
            <td>{!! $reportTemplateDocument->documentID !!}</td>
            <td>{!! $reportTemplateDocument->isActive !!}</td>
            <td>{!! $reportTemplateDocument->companySystemID !!}</td>
            <td>{!! $reportTemplateDocument->companyID !!}</td>
            <td>{!! $reportTemplateDocument->createdUserGroup !!}</td>
            <td>{!! $reportTemplateDocument->createdUserSystemID !!}</td>
            <td>{!! $reportTemplateDocument->createdUserID !!}</td>
            <td>{!! $reportTemplateDocument->createdPcID !!}</td>
            <td>{!! $reportTemplateDocument->createdDateTime !!}</td>
            <td>{!! $reportTemplateDocument->modifiedUserSystemID !!}</td>
            <td>{!! $reportTemplateDocument->modifiedUser !!}</td>
            <td>{!! $reportTemplateDocument->modifiedPc !!}</td>
            <td>{!! $reportTemplateDocument->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['reportTemplateDocuments.destroy', $reportTemplateDocument->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('reportTemplateDocuments.show', [$reportTemplateDocument->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('reportTemplateDocuments.edit', [$reportTemplateDocument->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>