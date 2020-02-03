<div class="table-responsive">
    <table class="table" id="userRights-table">
        <thead>
            <tr>
                <th>Employeeid</th>
        <th>Groupmasterid</th>
        <th>Pagemasterid</th>
        <th>Modulemasterid</th>
        <th>Companyid</th>
        <th>V</th>
        <th>A</th>
        <th>E</th>
        <th>D</th>
        <th>P</th>
        <th>Timestamp</th>
                <th colspan="3">Action</th>
            </tr>
        </thead>
        <tbody>
        @foreach($userRights as $userRights)
            <tr>
                <td>{!! $userRights->employeeID !!}</td>
            <td>{!! $userRights->groupMasterID !!}</td>
            <td>{!! $userRights->pageMasterID !!}</td>
            <td>{!! $userRights->moduleMasterID !!}</td>
            <td>{!! $userRights->companyID !!}</td>
            <td>{!! $userRights->V !!}</td>
            <td>{!! $userRights->A !!}</td>
            <td>{!! $userRights->E !!}</td>
            <td>{!! $userRights->D !!}</td>
            <td>{!! $userRights->P !!}</td>
            <td>{!! $userRights->timestamp !!}</td>
                <td>
                    {!! Form::open(['route' => ['userRights.destroy', $userRights->id], 'method' => 'delete']) !!}
                    <div class='btn-group'>
                        <a href="{!! route('userRights.show', [$userRights->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                        <a href="{!! route('userRights.edit', [$userRights->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                        {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                    </div>
                    {!! Form::close() !!}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
