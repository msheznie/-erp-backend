<table class="table table-responsive" id="reportTemplates-table">
    <thead>
        <tr>
            <th>Description</th>
        <th>Reportid</th>
        <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Isactive</th>
        <th>Ismprenabled</th>
        <th>Isassigntogroup</th>
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
    @foreach($reportTemplates as $reportTemplate)
        <tr>
            <td>{!! $reportTemplate->description !!}</td>
            <td>{!! $reportTemplate->reportID !!}</td>
            <td>{!! $reportTemplate->companySystemID !!}</td>
            <td>{!! $reportTemplate->companyID !!}</td>
            <td>{!! $reportTemplate->isActive !!}</td>
            <td>{!! $reportTemplate->isMPREnabled !!}</td>
            <td>{!! $reportTemplate->isAssignToGroup !!}</td>
            <td>{!! $reportTemplate->createdPCID !!}</td>
            <td>{!! $reportTemplate->createdUserSystemID !!}</td>
            <td>{!! $reportTemplate->createdUserID !!}</td>
            <td>{!! $reportTemplate->createdDateTime !!}</td>
            <td>{!! $reportTemplate->modifiedPCID !!}</td>
            <td>{!! $reportTemplate->modifiedUserSystemID !!}</td>
            <td>{!! $reportTemplate->modifiedUserID !!}</td>
            <td>{!! $reportTemplate->modifiedDateTime !!}</td>
            <td>{!! $reportTemplate->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['reportTemplates.destroy', $reportTemplate->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('reportTemplates.show', [$reportTemplate->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('reportTemplates.edit', [$reportTemplate->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>