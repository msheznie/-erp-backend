<table class="table table-responsive" id="suppliernatures-table">
    <thead>
        <tr>
            <th>Naturedescription</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($suppliernatures as $suppliernature)
        <tr>
            <td>{!! $suppliernature->natureDescription !!}</td>
            <td>
                {!! Form::open(['route' => ['suppliernatures.destroy', $suppliernature->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('suppliernatures.show', [$suppliernature->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('suppliernatures.edit', [$suppliernature->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>