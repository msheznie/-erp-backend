<table class="table table-responsive" id="employeeNavigations-table">
    <thead>
        <tr>
            <th>Empid</th>
        <th>Usergroupid</th>
        <th>Companyid</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($employeeNavigations as $employeeNavigation)
        <tr>
            <td>{!! $employeeNavigation->empID !!}</td>
            <td>{!! $employeeNavigation->userGroupID !!}</td>
            <td>{!! $employeeNavigation->companyID !!}</td>
            <td>{!! $employeeNavigation->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['employeeNavigations.destroy', $employeeNavigation->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('employeeNavigations.show', [$employeeNavigation->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('employeeNavigations.edit', [$employeeNavigation->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>