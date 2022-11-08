<div class="table-responsive">
    <table class="table" id="bidEvaluationSelections-table">
        <thead>
            <tr>
                <th>Bids</th>
        <th>Created By</th>
        <th>Description</th>
        <th>Status</th>
        <th>Tender Id</th>
        <th>Updated By</th>
                <th colspan="3">Action</th>
            </tr>
        </thead>
        <tbody>
        @foreach($bidEvaluationSelections as $bidEvaluationSelection)
            <tr>
                <td>{{ $bidEvaluationSelection->bids }}</td>
            <td>{{ $bidEvaluationSelection->created_by }}</td>
            <td>{{ $bidEvaluationSelection->description }}</td>
            <td>{{ $bidEvaluationSelection->status }}</td>
            <td>{{ $bidEvaluationSelection->tender_id }}</td>
            <td>{{ $bidEvaluationSelection->updated_by }}</td>
                <td>
                    {!! Form::open(['route' => ['bidEvaluationSelections.destroy', $bidEvaluationSelection->id], 'method' => 'delete']) !!}
                    <div class='btn-group'>
                        <a href="{{ route('bidEvaluationSelections.show', [$bidEvaluationSelection->id]) }}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                        <a href="{{ route('bidEvaluationSelections.edit', [$bidEvaluationSelection->id]) }}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                        {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                    </div>
                    {!! Form::close() !!}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
