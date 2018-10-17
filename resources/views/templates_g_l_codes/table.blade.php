<table class="table table-responsive" id="templatesGLCodes-table">
    <thead>
        <tr>
            <th>Templatemasterid</th>
        <th>Templatesdetailsautoid</th>
        <th>Chartofaccountsystemid</th>
        <th>Glcode</th>
        <th>Gldescription</th>
        <th>Timestamp</th>
        <th>Erp Templatesglcodecol</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($templatesGLCodes as $templatesGLCode)
        <tr>
            <td>{!! $templatesGLCode->templateMasterID !!}</td>
            <td>{!! $templatesGLCode->templatesDetailsAutoID !!}</td>
            <td>{!! $templatesGLCode->chartOfAccountSystemID !!}</td>
            <td>{!! $templatesGLCode->glCode !!}</td>
            <td>{!! $templatesGLCode->glDescription !!}</td>
            <td>{!! $templatesGLCode->timestamp !!}</td>
            <td>{!! $templatesGLCode->erp_templatesglcodecol !!}</td>
            <td>
                {!! Form::open(['route' => ['templatesGLCodes.destroy', $templatesGLCode->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('templatesGLCodes.show', [$templatesGLCode->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('templatesGLCodes.edit', [$templatesGLCode->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>