<table class="table table-responsive" id="erpLocations-table">
    <thead>
        <tr>
            <th>Locationname</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($erpLocations as $erpLocation)
        <tr>
            <td>{!! $erpLocation->locationName !!}</td>
            <td>
                {!! Form::open(['route' => ['erpLocations.destroy', $erpLocation->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('erpLocations.show', [$erpLocation->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('erpLocations.edit', [$erpLocation->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>