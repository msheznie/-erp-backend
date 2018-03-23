<table class="table table-responsive" id="supplierContactTypes-table">
    <thead>
        <tr>
            <th>Suppliercontactdescription</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($supplierContactTypes as $supplierContactType)
        <tr>
            <td>{!! $supplierContactType->supplierContactDescription !!}</td>
            <td>{!! $supplierContactType->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['supplierContactTypes.destroy', $supplierContactType->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('supplierContactTypes.show', [$supplierContactType->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('supplierContactTypes.edit', [$supplierContactType->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>