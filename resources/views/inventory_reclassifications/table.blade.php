<table class="table table-responsive" id="inventoryReclassifications-table">
    <thead>
        <tr>
            <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Servicelinesystemid</th>
        <th>Servicelinecode</th>
        <th>Companyfinanceyearid</th>
        <th>Companyfinanceperiodid</th>
        <th>Fybiggin</th>
        <th>Fyend</th>
        <th>Documentsystemid</th>
        <th>Documentid</th>
        <th>Inventoryreclassificationdate</th>
        <th>Narration</th>
        <th>Confirmedyn</th>
        <th>Confirmedbyempsystemid</th>
        <th>Confirmedbyempid</th>
        <th>Confirmedbyname</th>
        <th>Confirmeddate</th>
        <th>Approved</th>
        <th>Approveddate</th>
        <th>Approvedbyuserid</th>
        <th>Approvedbyusersystemid</th>
        <th>Posteddate</th>
        <th>Rolllevforapp Curr</th>
        <th>Rejectedyn</th>
        <th>Timesreferred</th>
        <th>Createddatetime</th>
        <th>Createdusergroup</th>
        <th>Createdpcid</th>
        <th>Createdusersystemid</th>
        <th>Createduserid</th>
        <th>Modifiedusersystemid</th>
        <th>Modifieduser</th>
        <th>Modifiedpc</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($inventoryReclassifications as $inventoryReclassification)
        <tr>
            <td>{!! $inventoryReclassification->companySystemID !!}</td>
            <td>{!! $inventoryReclassification->companyID !!}</td>
            <td>{!! $inventoryReclassification->serviceLineSystemID !!}</td>
            <td>{!! $inventoryReclassification->serviceLineCode !!}</td>
            <td>{!! $inventoryReclassification->companyFinanceYearID !!}</td>
            <td>{!! $inventoryReclassification->companyFinancePeriodID !!}</td>
            <td>{!! $inventoryReclassification->FYBiggin !!}</td>
            <td>{!! $inventoryReclassification->FYEnd !!}</td>
            <td>{!! $inventoryReclassification->documentSystemID !!}</td>
            <td>{!! $inventoryReclassification->documentID !!}</td>
            <td>{!! $inventoryReclassification->inventoryReclassificationDate !!}</td>
            <td>{!! $inventoryReclassification->narration !!}</td>
            <td>{!! $inventoryReclassification->confirmedYN !!}</td>
            <td>{!! $inventoryReclassification->confirmedByEmpSystemID !!}</td>
            <td>{!! $inventoryReclassification->confirmedByEmpID !!}</td>
            <td>{!! $inventoryReclassification->confirmedByName !!}</td>
            <td>{!! $inventoryReclassification->confirmedDate !!}</td>
            <td>{!! $inventoryReclassification->approved !!}</td>
            <td>{!! $inventoryReclassification->approvedDate !!}</td>
            <td>{!! $inventoryReclassification->approvedByUserID !!}</td>
            <td>{!! $inventoryReclassification->approvedByUserSystemID !!}</td>
            <td>{!! $inventoryReclassification->postedDate !!}</td>
            <td>{!! $inventoryReclassification->RollLevForApp_curr !!}</td>
            <td>{!! $inventoryReclassification->rejectedYN !!}</td>
            <td>{!! $inventoryReclassification->timesReferred !!}</td>
            <td>{!! $inventoryReclassification->createdDateTime !!}</td>
            <td>{!! $inventoryReclassification->createdUserGroup !!}</td>
            <td>{!! $inventoryReclassification->createdPCid !!}</td>
            <td>{!! $inventoryReclassification->createdUserSystemID !!}</td>
            <td>{!! $inventoryReclassification->createdUserID !!}</td>
            <td>{!! $inventoryReclassification->modifiedUserSystemID !!}</td>
            <td>{!! $inventoryReclassification->modifiedUser !!}</td>
            <td>{!! $inventoryReclassification->modifiedPc !!}</td>
            <td>{!! $inventoryReclassification->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['inventoryReclassifications.destroy', $inventoryReclassification->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('inventoryReclassifications.show', [$inventoryReclassification->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('inventoryReclassifications.edit', [$inventoryReclassification->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>