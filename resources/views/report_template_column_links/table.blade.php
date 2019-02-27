<table class="table table-responsive" id="reportTemplateColumnLinks-table">
    <thead>
        <tr>
            <th>Columnid</th>
        <th>Templateid</th>
        <th>Description</th>
        <th>Shortcode</th>
        <th>Type</th>
        <th>Sortorder</th>
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
    @foreach($reportTemplateColumnLinks as $reportTemplateColumnLink)
        <tr>
            <td>{!! $reportTemplateColumnLink->columnID !!}</td>
            <td>{!! $reportTemplateColumnLink->templateID !!}</td>
            <td>{!! $reportTemplateColumnLink->description !!}</td>
            <td>{!! $reportTemplateColumnLink->shortCode !!}</td>
            <td>{!! $reportTemplateColumnLink->type !!}</td>
            <td>{!! $reportTemplateColumnLink->sortOrder !!}</td>
            <td>{!! $reportTemplateColumnLink->createdPCID !!}</td>
            <td>{!! $reportTemplateColumnLink->createdUserSystemID !!}</td>
            <td>{!! $reportTemplateColumnLink->createdUserID !!}</td>
            <td>{!! $reportTemplateColumnLink->createdDateTime !!}</td>
            <td>{!! $reportTemplateColumnLink->modifiedPCID !!}</td>
            <td>{!! $reportTemplateColumnLink->modifiedUserSystemID !!}</td>
            <td>{!! $reportTemplateColumnLink->modifiedUserID !!}</td>
            <td>{!! $reportTemplateColumnLink->modifiedDateTime !!}</td>
            <td>{!! $reportTemplateColumnLink->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['reportTemplateColumnLinks.destroy', $reportTemplateColumnLink->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('reportTemplateColumnLinks.show', [$reportTemplateColumnLink->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('reportTemplateColumnLinks.edit', [$reportTemplateColumnLink->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>