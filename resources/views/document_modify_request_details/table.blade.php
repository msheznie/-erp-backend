<div class="table-responsive">
    <table class="table" id="documentModifyRequestDetails-table">
        <thead>
            <tr>
                <th>Attribute</th>
        <th>New Value</th>
        <th>Old Value</th>
        <th>Tender Id</th>
        <th>Version Id</th>
                <th colspan="3">Action</th>
            </tr>
        </thead>
        <tbody>
        @foreach($documentModifyRequestDetails as $documentModifyRequestDetail)
            <tr>
                <td>{{ $documentModifyRequestDetail->attribute }}</td>
            <td>{{ $documentModifyRequestDetail->new_value }}</td>
            <td>{{ $documentModifyRequestDetail->old_value }}</td>
            <td>{{ $documentModifyRequestDetail->tender_id }}</td>
            <td>{{ $documentModifyRequestDetail->version_id }}</td>
                <td>
                    {!! Form::open(['route' => ['documentModifyRequestDetails.destroy', $documentModifyRequestDetail->id], 'method' => 'delete']) !!}
                    <div class='btn-group'>
                        <a href="{{ route('documentModifyRequestDetails.show', [$documentModifyRequestDetail->id]) }}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                        <a href="{{ route('documentModifyRequestDetails.edit', [$documentModifyRequestDetail->id]) }}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                        {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                    </div>
                    {!! Form::close() !!}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
