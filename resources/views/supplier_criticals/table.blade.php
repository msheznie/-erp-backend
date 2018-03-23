<table class="table table-responsive" id="supplierCriticals-table">
    <thead>
        <tr>
            <th>Description</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($supplierCriticals as $supplierCritical)
        <tr>
            <td>{!! $supplierCritical->description !!}</td>
            <td>{!! $supplierCritical->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['supplierCriticals.destroy', $supplierCritical->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('supplierCriticals.show', [$supplierCritical->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('supplierCriticals.edit', [$supplierCritical->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>