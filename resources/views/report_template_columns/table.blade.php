<table class="table table-responsive" id="reportTemplateColumns-table">
    <thead>
        <tr>
            <th>Description</th>
        <th>Shortcode</th>
        <th>Type</th>
        <th>Createdpcid</th>
        <th>Createdusersystemid</th>
        <th>Createduserid</th>
        <th>Createddatetime</th>
        <th>Modifiedpcid</th>
        <th>Modifiedusersystemid</th>
        <th>Modifieduserid</th>
        <th>Modifieddatetime</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($reportTemplateColumns as $reportTemplateColumns)
        <tr>
            <td>{!! $reportTemplateColumns->description !!}</td>
            <td>{!! $reportTemplateColumns->shortCode !!}</td>
            <td>{!! $reportTemplateColumns->type !!}</td>
            <td>{!! $reportTemplateColumns->createdPCID !!}</td>
            <td>{!! $reportTemplateColumns->createdUserSystemID !!}</td>
            <td>{!! $reportTemplateColumns->createdUserID !!}</td>
            <td>{!! $reportTemplateColumns->createdDateTime !!}</td>
            <td>{!! $reportTemplateColumns->modifiedPCID !!}</td>
            <td>{!! $reportTemplateColumns->modifiedUserSystemID !!}</td>
            <td>{!! $reportTemplateColumns->modifiedUserID !!}</td>
            <td>{!! $reportTemplateColumns->modifiedDateTime !!}</td>
            <td>{!! $reportTemplateColumns->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['reportTemplateColumns.destroy', $reportTemplateColumns->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('reportTemplateColumns.show', [$reportTemplateColumns->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('reportTemplateColumns.edit', [$reportTemplateColumns->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>