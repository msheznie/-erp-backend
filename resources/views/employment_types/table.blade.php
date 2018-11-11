<table class="table table-responsive" id="employmentTypes-table">
    <thead>
        <tr>
            <th>Description</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($employmentTypes as $employmentType)
        <tr>
            <td>{!! $employmentType->description !!}</td>
            <td>
                {!! Form::open(['route' => ['employmentTypes.destroy', $employmentType->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('employmentTypes.show', [$employmentType->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('employmentTypes.edit', [$employmentType->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>