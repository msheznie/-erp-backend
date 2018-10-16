<table class="table table-responsive" id="templatesMasters-table">
    <thead>
        <tr>
            <th>Templatedescription</th>
        <th>Templatetype</th>
        <th>Templatereportname</th>
        <th>Isactive</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($templatesMasters as $templatesMaster)
        <tr>
            <td>{!! $templatesMaster->templateDescription !!}</td>
            <td>{!! $templatesMaster->templateType !!}</td>
            <td>{!! $templatesMaster->templateReportName !!}</td>
            <td>{!! $templatesMaster->isActive !!}</td>
            <td>{!! $templatesMaster->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['templatesMasters.destroy', $templatesMaster->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('templatesMasters.show', [$templatesMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('templatesMasters.edit', [$templatesMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>