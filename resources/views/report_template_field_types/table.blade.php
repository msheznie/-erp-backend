<table class="table table-responsive" id="reportTemplateFieldTypes-table">
    <thead>
        <tr>
            <th>Fieldtype</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($reportTemplateFieldTypes as $reportTemplateFieldType)
        <tr>
            <td>{!! $reportTemplateFieldType->fieldType !!}</td>
            <td>
                {!! Form::open(['route' => ['reportTemplateFieldTypes.destroy', $reportTemplateFieldType->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('reportTemplateFieldTypes.show', [$reportTemplateFieldType->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('reportTemplateFieldTypes.edit', [$reportTemplateFieldType->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>