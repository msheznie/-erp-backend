<table class="table table-responsive" id="bankMemoTypes-table">
    <thead>
        <tr>
            <th>Bankmemoheader</th>
        <th>Sortorder</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($bankMemoTypes as $bankMemoTypes)
        <tr>
            <td>{!! $bankMemoTypes->bankMemoHeader !!}</td>
            <td>{!! $bankMemoTypes->sortOrder !!}</td>
            <td>
                {!! Form::open(['route' => ['bankMemoTypes.destroy', $bankMemoTypes->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('bankMemoTypes.show', [$bankMemoTypes->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('bankMemoTypes.edit', [$bankMemoTypes->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>