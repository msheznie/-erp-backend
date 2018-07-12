<table class="table table-responsive" id="years-table">
    <thead>
        <tr>
            <th>Year</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($years as $year)
        <tr>
            <td>{!! $year->year !!}</td>
            <td>{!! $year->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['years.destroy', $year->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('years.show', [$year->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('years.edit', [$year->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>