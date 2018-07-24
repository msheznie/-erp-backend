<table class="table table-responsive" id="employeeProfiles-table">
    <thead>
        <tr>
            <th>Employeesystemid</th>
        <th>Empid</th>
        <th>Profileimage</th>
        <th>Modifieddate</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($employeeProfiles as $employeeProfile)
        <tr>
            <td>{!! $employeeProfile->employeeSystemID !!}</td>
            <td>{!! $employeeProfile->empID !!}</td>
            <td>{!! $employeeProfile->profileImage !!}</td>
            <td>{!! $employeeProfile->modifiedDate !!}</td>
            <td>{!! $employeeProfile->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['employeeProfiles.destroy', $employeeProfile->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('employeeProfiles.show', [$employeeProfile->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('employeeProfiles.edit', [$employeeProfile->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>