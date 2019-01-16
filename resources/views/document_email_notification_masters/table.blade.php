<table class="table table-responsive" id="documentEmailNotificationMasters-table">
    <thead>
        <tr>
            <th>Description</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($documentEmailNotificationMasters as $documentEmailNotificationMaster)
        <tr>
            <td>{!! $documentEmailNotificationMaster->description !!}</td>
            <td>
                {!! Form::open(['route' => ['documentEmailNotificationMasters.destroy', $documentEmailNotificationMaster->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('documentEmailNotificationMasters.show', [$documentEmailNotificationMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('documentEmailNotificationMasters.edit', [$documentEmailNotificationMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>