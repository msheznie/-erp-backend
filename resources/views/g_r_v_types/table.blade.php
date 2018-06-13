<table class="table table-responsive" id="gRVTypes-table">
    <thead>
        <tr>
            <th>Iderp Grvtpes</th>
        <th>Des</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($gRVTypes as $gRVTypes)
        <tr>
            <td>{!! $gRVTypes->idERP_GrvTpes !!}</td>
            <td>{!! $gRVTypes->des !!}</td>
            <td>
                {!! Form::open(['route' => ['gRVTypes.destroy', $gRVTypes->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('gRVTypes.show', [$gRVTypes->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('gRVTypes.edit', [$gRVTypes->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>