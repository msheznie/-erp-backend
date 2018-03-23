<table class="table table-responsive" id="accountsTypes-table">
    <thead>
        <tr>
            <th>Description</th>
        <th>Code</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($accountsTypes as $accountsType)
        <tr>
            <td>{!! $accountsType->description !!}</td>
            <td>{!! $accountsType->code !!}</td>
            <td>
                {!! Form::open(['route' => ['accountsTypes.destroy', $accountsType->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('accountsTypes.show', [$accountsType->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('accountsTypes.edit', [$accountsType->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>