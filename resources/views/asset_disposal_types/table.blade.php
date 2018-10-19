<table class="table table-responsive" id="assetDisposalTypes-table">
    <thead>
        <tr>
            <th>Typedescription</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($assetDisposalTypes as $assetDisposalType)
        <tr>
            <td>{!! $assetDisposalType->typeDescription !!}</td>
            <td>
                {!! Form::open(['route' => ['assetDisposalTypes.destroy', $assetDisposalType->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('assetDisposalTypes.show', [$assetDisposalType->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('assetDisposalTypes.edit', [$assetDisposalType->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>