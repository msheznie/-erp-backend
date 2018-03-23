<table class="table table-responsive" id="supplierImportances-table">
    <thead>
        <tr>
            <th>Importancedescription</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($supplierImportances as $supplierImportance)
        <tr>
            <td>{!! $supplierImportance->importanceDescription !!}</td>
            <td>
                {!! Form::open(['route' => ['supplierImportances.destroy', $supplierImportance->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('supplierImportances.show', [$supplierImportance->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('supplierImportances.edit', [$supplierImportance->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>