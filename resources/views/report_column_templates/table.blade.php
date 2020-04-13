<div class="table-responsive">
    <table class="table" id="reportColumnTemplates-table">
        <thead>
            <tr>
                <th>Templatename</th>
        <th>Templateimage</th>
                <th colspan="3">Action</th>
            </tr>
        </thead>
        <tbody>
        @foreach($reportColumnTemplates as $reportColumnTemplate)
            <tr>
                <td>{!! $reportColumnTemplate->templateName !!}</td>
            <td>{!! $reportColumnTemplate->templateImage !!}</td>
                <td>
                    {!! Form::open(['route' => ['reportColumnTemplates.destroy', $reportColumnTemplate->id], 'method' => 'delete']) !!}
                    <div class='btn-group'>
                        <a href="{!! route('reportColumnTemplates.show', [$reportColumnTemplate->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                        <a href="{!! route('reportColumnTemplates.edit', [$reportColumnTemplate->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                        {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                    </div>
                    {!! Form::close() !!}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
