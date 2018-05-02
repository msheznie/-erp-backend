<table class="table table-responsive" id="usersLogHistories-table">
    <thead>
        <tr>
            <th>Employee Id</th>
        <th>Empid</th>
        <th>Loginpcid</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($usersLogHistories as $usersLogHistory)
        <tr>
            <td>{!! $usersLogHistory->employee_id !!}</td>
            <td>{!! $usersLogHistory->empID !!}</td>
            <td>{!! $usersLogHistory->loginPCId !!}</td>
            <td>
                {!! Form::open(['route' => ['usersLogHistories.destroy', $usersLogHistory->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('usersLogHistories.show', [$usersLogHistory->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('usersLogHistories.edit', [$usersLogHistory->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>