<table class="table table-responsive" id="approvalRoles-table">
    <thead>
        <tr>
            <th>Rolldescription</th>
        <th>Documentsystemid</th>
        <th>Documentid</th>
        <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Departmentsystemid</th>
        <th>Departmentid</th>
        <th>Servicelinesystemid</th>
        <th>Servicelineid</th>
        <th>Rolllevel</th>
        <th>Approvallevelid</th>
        <th>Approvalgroupid</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($approvalRoles as $approvalRole)
        <tr>
            <td>{!! $approvalRole->rollDescription !!}</td>
            <td>{!! $approvalRole->documentSystemID !!}</td>
            <td>{!! $approvalRole->documentID !!}</td>
            <td>{!! $approvalRole->companySystemID !!}</td>
            <td>{!! $approvalRole->companyID !!}</td>
            <td>{!! $approvalRole->departmentSystemID !!}</td>
            <td>{!! $approvalRole->departmentID !!}</td>
            <td>{!! $approvalRole->serviceLineSystemID !!}</td>
            <td>{!! $approvalRole->serviceLineID !!}</td>
            <td>{!! $approvalRole->rollLevel !!}</td>
            <td>{!! $approvalRole->approvalLevelID !!}</td>
            <td>{!! $approvalRole->approvalGroupID !!}</td>
            <td>{!! $approvalRole->timeStamp !!}</td>
            <td>
                {!! Form::open(['route' => ['approvalRoles.destroy', $approvalRole->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('approvalRoles.show', [$approvalRole->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('approvalRoles.edit', [$approvalRole->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>