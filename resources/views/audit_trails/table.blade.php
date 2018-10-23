<table class="table table-responsive" id="auditTrails-table">
    <thead>
        <tr>
            <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Servicelinesystemid</th>
        <th>Servicelinecode</th>
        <th>Documentsystemid</th>
        <th>Documentid</th>
        <th>Documentsystemcode</th>
        <th>Valuefrom</th>
        <th>Valueto</th>
        <th>Valuefromsystemid</th>
        <th>Valuefromtext</th>
        <th>Valuetosystemid</th>
        <th>Valuetotext</th>
        <th>Description</th>
        <th>Modifiedusersystemid</th>
        <th>Modifieduserid</th>
        <th>Modifieddate</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($auditTrails as $auditTrail)
        <tr>
            <td>{!! $auditTrail->companySystemID !!}</td>
            <td>{!! $auditTrail->companyID !!}</td>
            <td>{!! $auditTrail->serviceLineSystemID !!}</td>
            <td>{!! $auditTrail->serviceLineCode !!}</td>
            <td>{!! $auditTrail->documentSystemID !!}</td>
            <td>{!! $auditTrail->documentID !!}</td>
            <td>{!! $auditTrail->documentSystemCode !!}</td>
            <td>{!! $auditTrail->valueFrom !!}</td>
            <td>{!! $auditTrail->valueTo !!}</td>
            <td>{!! $auditTrail->valueFromSystemID !!}</td>
            <td>{!! $auditTrail->valueFromText !!}</td>
            <td>{!! $auditTrail->valueToSystemID !!}</td>
            <td>{!! $auditTrail->valueToText !!}</td>
            <td>{!! $auditTrail->description !!}</td>
            <td>{!! $auditTrail->modifiedUserSystemID !!}</td>
            <td>{!! $auditTrail->modifiedUserID !!}</td>
            <td>{!! $auditTrail->modifiedDate !!}</td>
            <td>{!! $auditTrail->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['auditTrails.destroy', $auditTrail->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('auditTrails.show', [$auditTrail->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('auditTrails.edit', [$auditTrail->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>