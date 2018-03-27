<table class="table table-responsive" id="months-table">
    <thead>
        <tr>
            <th>Monthdes</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($months as $months)
        <tr>
            <td>{!! $months->monthDes !!}</td>
            <td>
                {!! Form::open(['route' => ['months.destroy', $months->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('months.show', [$months->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('months.edit', [$months->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>