<table class="table table-responsive" id="taxTypes-table">
    <thead>
        <tr>
            <th>Typedescription</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($taxTypes as $taxType)
        <tr>
            <td>{!! $taxType->typeDescription !!}</td>
            <td>
                {!! Form::open(['route' => ['taxTypes.destroy', $taxType->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('taxTypes.show', [$taxType->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('taxTypes.edit', [$taxType->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>