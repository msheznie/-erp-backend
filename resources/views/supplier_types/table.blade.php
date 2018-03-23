<table class="table table-responsive" id="supplierTypes-table">
    <thead>
        <tr>
            <th>Typedescription</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($supplierTypes as $supplierType)
        <tr>
            <td>{!! $supplierType->typeDescription !!}</td>
            <td>
                {!! Form::open(['route' => ['supplierTypes.destroy', $supplierType->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('supplierTypes.show', [$supplierType->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('supplierTypes.edit', [$supplierType->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>