<table class="table table-responsive" id="documentAttachmentTypes-table">
    <thead>
        <tr>
            <th>Documentid</th>
        <th>Description</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($documentAttachmentTypes as $documentAttachmentType)
        <tr>
            <td>{!! $documentAttachmentType->documentID !!}</td>
            <td>{!! $documentAttachmentType->description !!}</td>
            <td>{!! $documentAttachmentType->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['documentAttachmentTypes.destroy', $documentAttachmentType->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('documentAttachmentTypes.show', [$documentAttachmentType->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('documentAttachmentTypes.edit', [$documentAttachmentType->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>