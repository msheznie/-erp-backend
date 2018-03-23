<table class="table table-responsive" id="approvalGroups-table">
    <thead>
        <tr>
            <th>Rightsgroupdes</th>
        <th>Isformsassigned</th>
        <th>Documentid</th>
        <th>Departmentid</th>
        <th>Condition</th>
        <th>Sortorder</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($approvalGroups as $approvalGroups)
        <tr>
            <td>{!! $approvalGroups->rightsGroupDes !!}</td>
            <td>{!! $approvalGroups->isFormsAssigned !!}</td>
            <td>{!! $approvalGroups->documentID !!}</td>
            <td>{!! $approvalGroups->departmentID !!}</td>
            <td>{!! $approvalGroups->condition !!}</td>
            <td>{!! $approvalGroups->sortOrder !!}</td>
            <td>{!! $approvalGroups->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['approvalGroups.destroy', $approvalGroups->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('approvalGroups.show', [$approvalGroups->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('approvalGroups.edit', [$approvalGroups->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>