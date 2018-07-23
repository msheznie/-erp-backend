<table class="table table-responsive" id="documentReferedHistories-table">
    <thead>
        <tr>
            <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Departmentsystemid</th>
        <th>Departmentid</th>
        <th>Servicelinesystemid</th>
        <th>Servicelinecode</th>
        <th>Documentsystemid</th>
        <th>Documentid</th>
        <th>Documentsystemcode</th>
        <th>Documentcode</th>
        <th>Documentdate</th>
        <th>Approvallevelid</th>
        <th>Rollid</th>
        <th>Approvalgroupid</th>
        <th>Rolllevelorder</th>
        <th>Employeesystemid</th>
        <th>Employeeid</th>
        <th>Docconfirmeddate</th>
        <th>Docconfirmedbyempsystemid</th>
        <th>Docconfirmedbyempid</th>
        <th>Prerollapproveddate</th>
        <th>Approvedyn</th>
        <th>Approveddate</th>
        <th>Approvedcomments</th>
        <th>Rejectedyn</th>
        <th>Rejecteddate</th>
        <th>Rejectedcomments</th>
        <th>Approvedpcid</th>
        <th>Timestamp</th>
        <th>Reftimes</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($documentReferedHistories as $documentReferedHistory)
        <tr>
            <td>{!! $documentReferedHistory->companySystemID !!}</td>
            <td>{!! $documentReferedHistory->companyID !!}</td>
            <td>{!! $documentReferedHistory->departmentSystemID !!}</td>
            <td>{!! $documentReferedHistory->departmentID !!}</td>
            <td>{!! $documentReferedHistory->serviceLineSystemID !!}</td>
            <td>{!! $documentReferedHistory->serviceLineCode !!}</td>
            <td>{!! $documentReferedHistory->documentSystemID !!}</td>
            <td>{!! $documentReferedHistory->documentID !!}</td>
            <td>{!! $documentReferedHistory->documentSystemCode !!}</td>
            <td>{!! $documentReferedHistory->documentCode !!}</td>
            <td>{!! $documentReferedHistory->documentDate !!}</td>
            <td>{!! $documentReferedHistory->approvalLevelID !!}</td>
            <td>{!! $documentReferedHistory->rollID !!}</td>
            <td>{!! $documentReferedHistory->approvalGroupID !!}</td>
            <td>{!! $documentReferedHistory->rollLevelOrder !!}</td>
            <td>{!! $documentReferedHistory->employeeSystemID !!}</td>
            <td>{!! $documentReferedHistory->employeeID !!}</td>
            <td>{!! $documentReferedHistory->docConfirmedDate !!}</td>
            <td>{!! $documentReferedHistory->docConfirmedByEmpSystemID !!}</td>
            <td>{!! $documentReferedHistory->docConfirmedByEmpID !!}</td>
            <td>{!! $documentReferedHistory->preRollApprovedDate !!}</td>
            <td>{!! $documentReferedHistory->approvedYN !!}</td>
            <td>{!! $documentReferedHistory->approvedDate !!}</td>
            <td>{!! $documentReferedHistory->approvedComments !!}</td>
            <td>{!! $documentReferedHistory->rejectedYN !!}</td>
            <td>{!! $documentReferedHistory->rejectedDate !!}</td>
            <td>{!! $documentReferedHistory->rejectedComments !!}</td>
            <td>{!! $documentReferedHistory->approvedPCID !!}</td>
            <td>{!! $documentReferedHistory->timeStamp !!}</td>
            <td>{!! $documentReferedHistory->refTimes !!}</td>
            <td>
                {!! Form::open(['route' => ['documentReferedHistories.destroy', $documentReferedHistory->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('documentReferedHistories.show', [$documentReferedHistory->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('documentReferedHistories.edit', [$documentReferedHistory->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>