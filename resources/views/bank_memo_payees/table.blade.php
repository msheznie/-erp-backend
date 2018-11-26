<table class="table table-responsive" id="bankMemoPayees-table">
    <thead>
        <tr>
            <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Documentsystemid</th>
        <th>Documentid</th>
        <th>Documentsystemcode</th>
        <th>Bankmemotypeid</th>
        <th>Memoheader</th>
        <th>Memodetail</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($bankMemoPayees as $bankMemoPayee)
        <tr>
            <td>{!! $bankMemoPayee->companySystemID !!}</td>
            <td>{!! $bankMemoPayee->companyID !!}</td>
            <td>{!! $bankMemoPayee->documentSystemID !!}</td>
            <td>{!! $bankMemoPayee->documentID !!}</td>
            <td>{!! $bankMemoPayee->documentSystemCode !!}</td>
            <td>{!! $bankMemoPayee->bankMemoTypeID !!}</td>
            <td>{!! $bankMemoPayee->memoHeader !!}</td>
            <td>{!! $bankMemoPayee->memoDetail !!}</td>
            <td>{!! $bankMemoPayee->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['bankMemoPayees.destroy', $bankMemoPayee->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('bankMemoPayees.show', [$bankMemoPayee->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('bankMemoPayees.edit', [$bankMemoPayee->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>