<table class="table table-responsive" id="documentMasters-table">
    <thead>
        <tr>
            <th>Documentid</th>
        <th>Documentdescription</th>
        <th>Departmentid</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($documentMasters as $documentMaster)
        <tr>
            <td>{!! $documentMaster->documentID !!}</td>
            <td>{!! $documentMaster->documentDescription !!}</td>
            <td>{!! $documentMaster->departmentID !!}</td>
            <td>{!! $documentMaster->timeStamp !!}</td>
            <td>
                {!! Form::open(['route' => ['documentMasters.destroy', $documentMaster->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('documentMasters.show', [$documentMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('documentMasters.edit', [$documentMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>