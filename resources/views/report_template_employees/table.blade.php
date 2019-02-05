<table class="table table-responsive" id="reportTemplateEmployees-table">
    <thead>
        <tr>
            <th>Companyreporttemplateid</th>
        <th>Usergroupid</th>
        <th>Employeesystemid</th>
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
    @foreach($reportTemplateEmployees as $reportTemplateEmployees)
        <tr>
            <td>{!! $reportTemplateEmployees->companyReportTemplateID !!}</td>
            <td>{!! $reportTemplateEmployees->userGroupID !!}</td>
            <td>{!! $reportTemplateEmployees->employeeSystemID !!}</td>
            <td>{!! $reportTemplateEmployees->createdPCID !!}</td>
            <td>{!! $reportTemplateEmployees->createdUserSystemID !!}</td>
            <td>{!! $reportTemplateEmployees->createdUserID !!}</td>
            <td>{!! $reportTemplateEmployees->createdDateTime !!}</td>
            <td>{!! $reportTemplateEmployees->modifiedPCID !!}</td>
            <td>{!! $reportTemplateEmployees->modifiedUserSystemID !!}</td>
            <td>{!! $reportTemplateEmployees->modifiedUserID !!}</td>
            <td>{!! $reportTemplateEmployees->modifiedDateTime !!}</td>
            <td>{!! $reportTemplateEmployees->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['reportTemplateEmployees.destroy', $reportTemplateEmployees->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('reportTemplateEmployees.show', [$reportTemplateEmployees->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('reportTemplateEmployees.edit', [$reportTemplateEmployees->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>