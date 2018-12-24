<table class="table table-responsive" id="reportTemplateDetails-table">
    <thead>
        <tr>
            <th>Companyreporttemplateid</th>
        <th>Description</th>
        <th>Itemtype</th>
        <th>Sortorder</th>
        <th>Masterid</th>
        <th>Accounttype</th>
        <th>Companysystemid</th>
        <th>Companyid</th>
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
    @foreach($reportTemplateDetails as $reportTemplateDetails)
        <tr>
            <td>{!! $reportTemplateDetails->companyReportTemplateID !!}</td>
            <td>{!! $reportTemplateDetails->description !!}</td>
            <td>{!! $reportTemplateDetails->itemType !!}</td>
            <td>{!! $reportTemplateDetails->sortOrder !!}</td>
            <td>{!! $reportTemplateDetails->masterID !!}</td>
            <td>{!! $reportTemplateDetails->accountType !!}</td>
            <td>{!! $reportTemplateDetails->companySystemID !!}</td>
            <td>{!! $reportTemplateDetails->companyID !!}</td>
            <td>{!! $reportTemplateDetails->createdPCID !!}</td>
            <td>{!! $reportTemplateDetails->createdUserSystemID !!}</td>
            <td>{!! $reportTemplateDetails->createdUserID !!}</td>
            <td>{!! $reportTemplateDetails->createdDateTime !!}</td>
            <td>{!! $reportTemplateDetails->modifiedPCID !!}</td>
            <td>{!! $reportTemplateDetails->modifiedUserSystemID !!}</td>
            <td>{!! $reportTemplateDetails->modifiedUserID !!}</td>
            <td>{!! $reportTemplateDetails->modifiedDateTime !!}</td>
            <td>{!! $reportTemplateDetails->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['reportTemplateDetails.destroy', $reportTemplateDetails->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('reportTemplateDetails.show', [$reportTemplateDetails->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('reportTemplateDetails.edit', [$reportTemplateDetails->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>