<table class="table table-responsive" id="itemIssueTypes-table">
    <thead>
        <tr>
            <th>Issuetypedes</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($itemIssueTypes as $itemIssueType)
        <tr>
            <td>{!! $itemIssueType->issueTypeDes !!}</td>
            <td>
                {!! Form::open(['route' => ['itemIssueTypes.destroy', $itemIssueType->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('itemIssueTypes.show', [$itemIssueType->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('itemIssueTypes.edit', [$itemIssueType->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>