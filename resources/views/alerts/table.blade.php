<table class="table table-responsive" id="alerts-table">
    <thead>
        <tr>
            <th>Companyid</th>
        <th>Empid</th>
        <th>Docid</th>
        <th>Docapprovedyn</th>
        <th>Docsystemcode</th>
        <th>Doccode</th>
        <th>Alertmessage</th>
        <th>Alertdatetime</th>
        <th>Alertviewedyn</th>
        <th>Alertvieweddatetime</th>
        <th>Empname</th>
        <th>Empemail</th>
        <th>Ccemailid</th>
        <th>Emailalertmessage</th>
        <th>Isemailsend</th>
        <th>Attachmentfilename</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($alerts as $alert)
        <tr>
            <td>{!! $alert->companyID !!}</td>
            <td>{!! $alert->empID !!}</td>
            <td>{!! $alert->docID !!}</td>
            <td>{!! $alert->docApprovedYN !!}</td>
            <td>{!! $alert->docSystemCode !!}</td>
            <td>{!! $alert->docCode !!}</td>
            <td>{!! $alert->alertMessage !!}</td>
            <td>{!! $alert->alertDateTime !!}</td>
            <td>{!! $alert->alertViewedYN !!}</td>
            <td>{!! $alert->alertViewedDateTime !!}</td>
            <td>{!! $alert->empName !!}</td>
            <td>{!! $alert->empEmail !!}</td>
            <td>{!! $alert->ccEmailID !!}</td>
            <td>{!! $alert->emailAlertMessage !!}</td>
            <td>{!! $alert->isEmailSend !!}</td>
            <td>{!! $alert->attachmentFileName !!}</td>
            <td>{!! $alert->timeStamp !!}</td>
            <td>
                {!! Form::open(['route' => ['alerts.destroy', $alert->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('alerts.show', [$alert->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('alerts.edit', [$alert->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>