<table class="table table-responsive" id="rigMasters-table">
    <thead>
        <tr>
            <th>Rigdescription</th>
        <th>Companyid</th>
        <th>Oldid</th>
        <th>Isrig</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($rigMasters as $rigMaster)
        <tr>
            <td>{!! $rigMaster->RigDescription !!}</td>
            <td>{!! $rigMaster->companyID !!}</td>
            <td>{!! $rigMaster->oldID !!}</td>
            <td>{!! $rigMaster->isRig !!}</td>
            <td>
                {!! Form::open(['route' => ['rigMasters.destroy', $rigMaster->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('rigMasters.show', [$rigMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('rigMasters.edit', [$rigMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>