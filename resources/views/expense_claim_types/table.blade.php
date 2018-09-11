<table class="table table-responsive" id="expenseClaimTypes-table">
    <thead>
        <tr>
            <th>Expenseclaimtypedescription</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($expenseClaimTypes as $expenseClaimType)
        <tr>
            <td>{!! $expenseClaimType->expenseClaimTypeDescription !!}</td>
            <td>{!! $expenseClaimType->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['expenseClaimTypes.destroy', $expenseClaimType->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('expenseClaimTypes.show', [$expenseClaimType->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('expenseClaimTypes.edit', [$expenseClaimType->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>