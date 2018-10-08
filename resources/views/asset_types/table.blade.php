<table class="table table-responsive" id="assetTypes-table">
    <thead>
        <tr>
            <th>Typedes</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($assetTypes as $assetType)
        <tr>
            <td>{!! $assetType->typeDes !!}</td>
            <td>{!! $assetType->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['assetTypes.destroy', $assetType->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('assetTypes.show', [$assetType->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('assetTypes.edit', [$assetType->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>