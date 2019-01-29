<table class="table table-responsive" id="reportTemplateNumbers-table">
    <thead>
        <tr>
            <th>Value</th>
        <th>Timesstamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($reportTemplateNumbers as $reportTemplateNumbers)
        <tr>
            <td>{!! $reportTemplateNumbers->value !!}</td>
            <td>{!! $reportTemplateNumbers->timesStamp !!}</td>
            <td>
                {!! Form::open(['route' => ['reportTemplateNumbers.destroy', $reportTemplateNumbers->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('reportTemplateNumbers.show', [$reportTemplateNumbers->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('reportTemplateNumbers.edit', [$reportTemplateNumbers->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>