<table class="table table-responsive" id="documentApproveds-table">
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
        <th>Employeeid</th>
        <th>Docconfirmeddate</th>
        <th>Docconfirmedbyempid</th>
        <th>Prerollapproveddate</th>
        <th>Approvedyn</th>
        <th>Approveddate</th>
        <th>Approvedcomments</th>
        <th>Rejectedyn</th>
        <th>Rejecteddate</th>
        <th>Rejectedcomments</th>
        <th>Myapproveflag</th>
        <th>Isdeligationapproval</th>
        <th>Approvedforempid</th>
        <th>Isapprovedfrompc</th>
        <th>Approvedpcid</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($documentApproveds as $documentApproved)
        <tr>
            <td>{!! $documentApproved->companySystemID !!}</td>
            <td>{!! $documentApproved->companyID !!}</td>
            <td>{!! $documentApproved->departmentSystemID !!}</td>
            <td>{!! $documentApproved->departmentID !!}</td>
            <td>{!! $documentApproved->serviceLineSystemID !!}</td>
            <td>{!! $documentApproved->serviceLineCode !!}</td>
            <td>{!! $documentApproved->documentSystemID !!}</td>
            <td>{!! $documentApproved->documentID !!}</td>
            <td>{!! $documentApproved->documentSystemCode !!}</td>
            <td>{!! $documentApproved->documentCode !!}</td>
            <td>{!! $documentApproved->documentDate !!}</td>
            <td>{!! $documentApproved->approvalLevelID !!}</td>
            <td>{!! $documentApproved->rollID !!}</td>
            <td>{!! $documentApproved->approvalGroupID !!}</td>
            <td>{!! $documentApproved->rollLevelOrder !!}</td>
            <td>{!! $documentApproved->employeeID !!}</td>
            <td>{!! $documentApproved->docConfirmedDate !!}</td>
            <td>{!! $documentApproved->docConfirmedByEmpID !!}</td>
            <td>{!! $documentApproved->preRollApprovedDate !!}</td>
            <td>{!! $documentApproved->approvedYN !!}</td>
            <td>{!! $documentApproved->approvedDate !!}</td>
            <td>{!! $documentApproved->approvedComments !!}</td>
            <td>{!! $documentApproved->rejectedYN !!}</td>
            <td>{!! $documentApproved->rejectedDate !!}</td>
            <td>{!! $documentApproved->rejectedComments !!}</td>
            <td>{!! $documentApproved->myApproveFlag !!}</td>
            <td>{!! $documentApproved->isDeligationApproval !!}</td>
            <td>{!! $documentApproved->approvedForEmpID !!}</td>
            <td>{!! $documentApproved->isApprovedFromPC !!}</td>
            <td>{!! $documentApproved->approvedPCID !!}</td>
            <td>{!! $documentApproved->timeStamp !!}</td>
            <td>
                {!! Form::open(['route' => ['documentApproveds.destroy', $documentApproved->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('documentApproveds.show', [$documentApproved->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('documentApproveds.edit', [$documentApproved->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>