<div class="table-responsive">
    <table class="table" id="lptPermissions-table">
        <thead>
            <tr>
                <th>Empid</th>
        <th>Employeesystemid</th>
        <th>Companyid</th>
        <th>Islptreview</th>
        <th>Islptclose</th>
        <th>Createdby</th>
        <th>Timestamp</th>
                <th colspan="3">Action</th>
            </tr>
        </thead>
        <tbody>
        @foreach($lptPermissions as $lptPermission)
            <tr>
                <td>{!! $lptPermission->empID !!}</td>
            <td>{!! $lptPermission->employeeSystemID !!}</td>
            <td>{!! $lptPermission->companyID !!}</td>
            <td>{!! $lptPermission->isLPTReview !!}</td>
            <td>{!! $lptPermission->isLPTClose !!}</td>
            <td>{!! $lptPermission->createdBy !!}</td>
            <td>{!! $lptPermission->timestamp !!}</td>
                <td>
                    {!! Form::open(['route' => ['lptPermissions.destroy', $lptPermission->id], 'method' => 'delete']) !!}
                    <div class='btn-group'>
                        <a href="{!! route('lptPermissions.show', [$lptPermission->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                        <a href="{!! route('lptPermissions.edit', [$lptPermission->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                        {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                    </div>
                    {!! Form::close() !!}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
