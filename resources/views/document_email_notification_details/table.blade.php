<table class="table table-responsive" id="documentEmailNotificationDetails-table">
    <thead>
        <tr>
            <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Employeesystemid</th>
        <th>Empid</th>
        <th>Sendyn</th>
        <th>Emailnotificationid</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($documentEmailNotificationDetails as $documentEmailNotificationDetail)
        <tr>
            <td>{!! $documentEmailNotificationDetail->companySystemID !!}</td>
            <td>{!! $documentEmailNotificationDetail->companyID !!}</td>
            <td>{!! $documentEmailNotificationDetail->employeeSystemID !!}</td>
            <td>{!! $documentEmailNotificationDetail->empID !!}</td>
            <td>{!! $documentEmailNotificationDetail->sendYN !!}</td>
            <td>{!! $documentEmailNotificationDetail->emailNotificationID !!}</td>
            <td>{!! $documentEmailNotificationDetail->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['documentEmailNotificationDetails.destroy', $documentEmailNotificationDetail->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('documentEmailNotificationDetails.show', [$documentEmailNotificationDetail->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('documentEmailNotificationDetails.edit', [$documentEmailNotificationDetail->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>