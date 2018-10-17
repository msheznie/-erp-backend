<table class="table table-responsive" id="templatesDetails-table">
    <thead>
        <tr>
            <th>Templatesmasterautoid</th>
        <th>Templatedetaildescription</th>
        <th>Controlaccountid</th>
        <th>Controlaccountsubid</th>
        <th>Sortorder</th>
        <th>Cashflowid</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($templatesDetails as $templatesDetails)
        <tr>
            <td>{!! $templatesDetails->templatesMasterAutoID !!}</td>
            <td>{!! $templatesDetails->templateDetailDescription !!}</td>
            <td>{!! $templatesDetails->controlAccountID !!}</td>
            <td>{!! $templatesDetails->controlAccountSubID !!}</td>
            <td>{!! $templatesDetails->sortOrder !!}</td>
            <td>{!! $templatesDetails->cashflowid !!}</td>
            <td>{!! $templatesDetails->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['templatesDetails.destroy', $templatesDetails->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('templatesDetails.show', [$templatesDetails->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('templatesDetails.edit', [$templatesDetails->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>