<table class="table table-responsive" id="materielRequests-table">
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
        <th>Confirmeddate</th>
        <th>Isactive</th>
        <th>Quantityonorder</th>
        <th>Quantityinhand</th>
        <th>Createdusergroup</th>
        <th>Createdpcid</th>
        <th>Createdusersystemid</th>
        <th>Createduserid</th>
        <th>Modifiedpc</th>
        <th>Modifiedusersystemid</th>
        <th>Modifieduser</th>
        <th>Createddatetime</th>
        <th>Selectedforissue</th>
        <th>Approved</th>
        <th>Closedyn</th>
        <th>Issuetrackid</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($materielRequests as $materielRequest)
        <tr>
            <td>{!! $materielRequest->companySystemID !!}</td>
            <td>{!! $materielRequest->companyID !!}</td>
            <td>{!! $materielRequest->departmentSystemID !!}</td>
            <td>{!! $materielRequest->departmentID !!}</td>
            <td>{!! $materielRequest->serviceLineSystemID !!}</td>
            <td>{!! $materielRequest->serviceLineCode !!}</td>
            <td>{!! $materielRequest->documentSystemID !!}</td>
            <td>{!! $materielRequest->documentID !!}</td>
            <td>{!! $materielRequest->companyJobID !!}</td>
            <td>{!! $materielRequest->jobDescription !!}</td>
            <td>{!! $materielRequest->serialNumber !!}</td>
            <td>{!! $materielRequest->RequestCode !!}</td>
            <td>{!! $materielRequest->comments !!}</td>
            <td>{!! $materielRequest->location !!}</td>
            <td>{!! $materielRequest->priority !!}</td>
            <td>{!! $materielRequest->deliveryLocation !!}</td>
            <td>{!! $materielRequest->RequestedDate !!}</td>
            <td>{!! $materielRequest->ConfirmedYN !!}</td>
            <td>{!! $materielRequest->ConfirmedBySystemID !!}</td>
            <td>{!! $materielRequest->ConfirmedBy !!}</td>
            <td>{!! $materielRequest->ConfirmedDate !!}</td>
            <td>{!! $materielRequest->isActive !!}</td>
            <td>{!! $materielRequest->quantityOnOrder !!}</td>
            <td>{!! $materielRequest->quantityInHand !!}</td>
            <td>{!! $materielRequest->createdUserGroup !!}</td>
            <td>{!! $materielRequest->createdPcID !!}</td>
            <td>{!! $materielRequest->createdUserSystemID !!}</td>
            <td>{!! $materielRequest->createdUserID !!}</td>
            <td>{!! $materielRequest->modifiedPc !!}</td>
            <td>{!! $materielRequest->modifiedUserSystemID !!}</td>
            <td>{!! $materielRequest->modifiedUser !!}</td>
            <td>{!! $materielRequest->createdDateTime !!}</td>
            <td>{!! $materielRequest->selectedForIssue !!}</td>
            <td>{!! $materielRequest->approved !!}</td>
            <td>{!! $materielRequest->ClosedYN !!}</td>
            <td>{!! $materielRequest->issueTrackID !!}</td>
            <td>{!! $materielRequest->timeStamp !!}</td>
            <td>
                {!! Form::open(['route' => ['materielRequests.destroy', $materielRequest->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('materielRequests.show', [$materielRequest->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('materielRequests.edit', [$materielRequest->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>