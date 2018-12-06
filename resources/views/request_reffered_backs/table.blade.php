<table class="table table-responsive" id="requestRefferedBacks-table">
    <thead>
        <tr>
            <th>Requestid</th>
        <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Departmentsystemid</th>
        <th>Departmentid</th>
        <th>Servicelinesystemid</th>
        <th>Servicelinecode</th>
        <th>Documentsystemid</th>
        <th>Documentid</th>
        <th>Companyjobid</th>
        <th>Jobdescription</th>
        <th>Serialnumber</th>
        <th>Requestcode</th>
        <th>Comments</th>
        <th>Location</th>
        <th>Priority</th>
        <th>Deliverylocation</th>
        <th>Requesteddate</th>
        <th>Confirmedyn</th>
        <th>Confirmedbysystemid</th>
        <th>Confirmedby</th>
        <th>Confirmedempname</th>
        <th>Confirmeddate</th>
        <th>Isactive</th>
        <th>Quantityonorder</th>
        <th>Quantityinhand</th>
        <th>Selectedforissue</th>
        <th>Approved</th>
        <th>Closedyn</th>
        <th>Issuetrackid</th>
        <th>Timestamp</th>
        <th>Rolllevforapp Curr</th>
        <th>Approveddate</th>
        <th>Approvedbyusersystemid</th>
        <th>Refferedbackyn</th>
        <th>Timesreferred</th>
        <th>Createdusergroup</th>
        <th>Createdpcid</th>
        <th>Createdusersystemid</th>
        <th>Createduserid</th>
        <th>Modifiedpc</th>
        <th>Modifiedusersystemid</th>
        <th>Modifieduser</th>
        <th>Createddatetime</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($requestRefferedBacks as $requestRefferedBack)
        <tr>
            <td>{!! $requestRefferedBack->RequestID !!}</td>
            <td>{!! $requestRefferedBack->companySystemID !!}</td>
            <td>{!! $requestRefferedBack->companyID !!}</td>
            <td>{!! $requestRefferedBack->departmentSystemID !!}</td>
            <td>{!! $requestRefferedBack->departmentID !!}</td>
            <td>{!! $requestRefferedBack->serviceLineSystemID !!}</td>
            <td>{!! $requestRefferedBack->serviceLineCode !!}</td>
            <td>{!! $requestRefferedBack->documentSystemID !!}</td>
            <td>{!! $requestRefferedBack->documentID !!}</td>
            <td>{!! $requestRefferedBack->companyJobID !!}</td>
            <td>{!! $requestRefferedBack->jobDescription !!}</td>
            <td>{!! $requestRefferedBack->serialNumber !!}</td>
            <td>{!! $requestRefferedBack->RequestCode !!}</td>
            <td>{!! $requestRefferedBack->comments !!}</td>
            <td>{!! $requestRefferedBack->location !!}</td>
            <td>{!! $requestRefferedBack->priority !!}</td>
            <td>{!! $requestRefferedBack->deliveryLocation !!}</td>
            <td>{!! $requestRefferedBack->RequestedDate !!}</td>
            <td>{!! $requestRefferedBack->ConfirmedYN !!}</td>
            <td>{!! $requestRefferedBack->ConfirmedBySystemID !!}</td>
            <td>{!! $requestRefferedBack->ConfirmedBy !!}</td>
            <td>{!! $requestRefferedBack->confirmedEmpName !!}</td>
            <td>{!! $requestRefferedBack->ConfirmedDate !!}</td>
            <td>{!! $requestRefferedBack->isActive !!}</td>
            <td>{!! $requestRefferedBack->quantityOnOrder !!}</td>
            <td>{!! $requestRefferedBack->quantityInHand !!}</td>
            <td>{!! $requestRefferedBack->selectedForIssue !!}</td>
            <td>{!! $requestRefferedBack->approved !!}</td>
            <td>{!! $requestRefferedBack->ClosedYN !!}</td>
            <td>{!! $requestRefferedBack->issueTrackID !!}</td>
            <td>{!! $requestRefferedBack->timeStamp !!}</td>
            <td>{!! $requestRefferedBack->RollLevForApp_curr !!}</td>
            <td>{!! $requestRefferedBack->approvedDate !!}</td>
            <td>{!! $requestRefferedBack->approvedByUserSystemID !!}</td>
            <td>{!! $requestRefferedBack->refferedBackYN !!}</td>
            <td>{!! $requestRefferedBack->timesReferred !!}</td>
            <td>{!! $requestRefferedBack->createdUserGroup !!}</td>
            <td>{!! $requestRefferedBack->createdPcID !!}</td>
            <td>{!! $requestRefferedBack->createdUserSystemID !!}</td>
            <td>{!! $requestRefferedBack->createdUserID !!}</td>
            <td>{!! $requestRefferedBack->modifiedPc !!}</td>
            <td>{!! $requestRefferedBack->modifiedUserSystemID !!}</td>
            <td>{!! $requestRefferedBack->modifiedUser !!}</td>
            <td>{!! $requestRefferedBack->createdDateTime !!}</td>
            <td>
                {!! Form::open(['route' => ['requestRefferedBacks.destroy', $requestRefferedBack->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('requestRefferedBacks.show', [$requestRefferedBack->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('requestRefferedBacks.edit', [$requestRefferedBack->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>