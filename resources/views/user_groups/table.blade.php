<table class="table table-responsive" id="userGroups-table">
    <thead>
        <tr>
            <th>Companyid</th>
        <th>Description</th>
        <th>Isactive</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($userGroups as $userGroup)
        <tr>
            <td>{!! $userGroup->companyID !!}</td>
            <td>{!! $userGroup->description !!}</td>
            <td>{!! $userGroup->isActive !!}</td>
            <td>{!! $userGroup->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['userGroups.destroy', $userGroup->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('userGroups.show', [$userGroup->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('userGroups.edit', [$userGroup->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>