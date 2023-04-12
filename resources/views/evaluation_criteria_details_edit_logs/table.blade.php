<div class="table-responsive">
    <table class="table" id="evaluationCriteriaDetailsEditLogs-table">
        <thead>
            <tr>
                <th>Answer Type Id</th>
        <th>Critera Type Id</th>
        <th>Description</th>
        <th>Is Final Level</th>
        <th>Level</th>
        <th>Master Id</th>
        <th>Max Value</th>
        <th>Min Value</th>
        <th>Modify Type</th>
        <th>Parent Id</th>
        <th>Passing Weightage</th>
        <th>Ref Log Id</th>
        <th>Sort Order</th>
        <th>Tender Id</th>
        <th>Weightage</th>
                <th colspan="3">Action</th>
            </tr>
        </thead>
        <tbody>
        @foreach($evaluationCriteriaDetailsEditLogs as $evaluationCriteriaDetailsEditLog)
            <tr>
                <td>{{ $evaluationCriteriaDetailsEditLog->answer_type_id }}</td>
            <td>{{ $evaluationCriteriaDetailsEditLog->critera_type_id }}</td>
            <td>{{ $evaluationCriteriaDetailsEditLog->description }}</td>
            <td>{{ $evaluationCriteriaDetailsEditLog->is_final_level }}</td>
            <td>{{ $evaluationCriteriaDetailsEditLog->level }}</td>
            <td>{{ $evaluationCriteriaDetailsEditLog->master_id }}</td>
            <td>{{ $evaluationCriteriaDetailsEditLog->max_value }}</td>
            <td>{{ $evaluationCriteriaDetailsEditLog->min_value }}</td>
            <td>{{ $evaluationCriteriaDetailsEditLog->modify_type }}</td>
            <td>{{ $evaluationCriteriaDetailsEditLog->parent_id }}</td>
            <td>{{ $evaluationCriteriaDetailsEditLog->passing_weightage }}</td>
            <td>{{ $evaluationCriteriaDetailsEditLog->ref_log_id }}</td>
            <td>{{ $evaluationCriteriaDetailsEditLog->sort_order }}</td>
            <td>{{ $evaluationCriteriaDetailsEditLog->tender_id }}</td>
            <td>{{ $evaluationCriteriaDetailsEditLog->weightage }}</td>
                <td>
                    {!! Form::open(['route' => ['evaluationCriteriaDetailsEditLogs.destroy', $evaluationCriteriaDetailsEditLog->id], 'method' => 'delete']) !!}
                    <div class='btn-group'>
                        <a href="{{ route('evaluationCriteriaDetailsEditLogs.show', [$evaluationCriteriaDetailsEditLog->id]) }}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                        <a href="{{ route('evaluationCriteriaDetailsEditLogs.edit', [$evaluationCriteriaDetailsEditLog->id]) }}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                        {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                    </div>
                    {!! Form::close() !!}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
