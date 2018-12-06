<table class="table table-responsive" id="itemReturnMasterRefferedBacks-table">
    <thead>
        <tr>
            <th>Itemreturnautoid</th>
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
        <th>Serialno</th>
        <th>Itemreturncode</th>
        <th>Returntype</th>
        <th>Returndate</th>
        <th>Returnedby</th>
        <th>Jobno</th>
        <th>Customerid</th>
        <th>Warehouselocation</th>
        <th>Returnrefno</th>
        <th>Comment</th>
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
        <th>Refferedbackyn</th>
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
    @foreach($itemReturnMasterRefferedBacks as $itemReturnMasterRefferedBack)
        <tr>
            <td>{!! $itemReturnMasterRefferedBack->itemReturnAutoID !!}</td>
            <td>{!! $itemReturnMasterRefferedBack->companySystemID !!}</td>
            <td>{!! $itemReturnMasterRefferedBack->companyID !!}</td>
            <td>{!! $itemReturnMasterRefferedBack->serviceLineSystemID !!}</td>
            <td>{!! $itemReturnMasterRefferedBack->serviceLineCode !!}</td>
            <td>{!! $itemReturnMasterRefferedBack->companyFinanceYearID !!}</td>
            <td>{!! $itemReturnMasterRefferedBack->companyFinancePeriodID !!}</td>
            <td>{!! $itemReturnMasterRefferedBack->FYBiggin !!}</td>
            <td>{!! $itemReturnMasterRefferedBack->FYEnd !!}</td>
            <td>{!! $itemReturnMasterRefferedBack->documentSystemID !!}</td>
            <td>{!! $itemReturnMasterRefferedBack->documentID !!}</td>
            <td>{!! $itemReturnMasterRefferedBack->serialNo !!}</td>
            <td>{!! $itemReturnMasterRefferedBack->itemReturnCode !!}</td>
            <td>{!! $itemReturnMasterRefferedBack->ReturnType !!}</td>
            <td>{!! $itemReturnMasterRefferedBack->ReturnDate !!}</td>
            <td>{!! $itemReturnMasterRefferedBack->ReturnedBy !!}</td>
            <td>{!! $itemReturnMasterRefferedBack->jobNo !!}</td>
            <td>{!! $itemReturnMasterRefferedBack->customerID !!}</td>
            <td>{!! $itemReturnMasterRefferedBack->wareHouseLocation !!}</td>
            <td>{!! $itemReturnMasterRefferedBack->ReturnRefNo !!}</td>
            <td>{!! $itemReturnMasterRefferedBack->comment !!}</td>
            <td>{!! $itemReturnMasterRefferedBack->confirmedYN !!}</td>
            <td>{!! $itemReturnMasterRefferedBack->confirmedByEmpSystemID !!}</td>
            <td>{!! $itemReturnMasterRefferedBack->confirmedByEmpID !!}</td>
            <td>{!! $itemReturnMasterRefferedBack->confirmedByName !!}</td>
            <td>{!! $itemReturnMasterRefferedBack->confirmedDate !!}</td>
            <td>{!! $itemReturnMasterRefferedBack->approved !!}</td>
            <td>{!! $itemReturnMasterRefferedBack->approvedDate !!}</td>
            <td>{!! $itemReturnMasterRefferedBack->approvedByUserID !!}</td>
            <td>{!! $itemReturnMasterRefferedBack->approvedByUserSystemID !!}</td>
            <td>{!! $itemReturnMasterRefferedBack->postedDate !!}</td>
            <td>{!! $itemReturnMasterRefferedBack->RollLevForApp_curr !!}</td>
            <td>{!! $itemReturnMasterRefferedBack->refferedBackYN !!}</td>
            <td>{!! $itemReturnMasterRefferedBack->timesReferred !!}</td>
            <td>{!! $itemReturnMasterRefferedBack->createdDateTime !!}</td>
            <td>{!! $itemReturnMasterRefferedBack->createdUserGroup !!}</td>
            <td>{!! $itemReturnMasterRefferedBack->createdPCid !!}</td>
            <td>{!! $itemReturnMasterRefferedBack->createdUserSystemID !!}</td>
            <td>{!! $itemReturnMasterRefferedBack->createdUserID !!}</td>
            <td>{!! $itemReturnMasterRefferedBack->modifiedUserSystemID !!}</td>
            <td>{!! $itemReturnMasterRefferedBack->modifiedUser !!}</td>
            <td>{!! $itemReturnMasterRefferedBack->modifiedPc !!}</td>
            <td>{!! $itemReturnMasterRefferedBack->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['itemReturnMasterRefferedBacks.destroy', $itemReturnMasterRefferedBack->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('itemReturnMasterRefferedBacks.show', [$itemReturnMasterRefferedBack->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('itemReturnMasterRefferedBacks.edit', [$itemReturnMasterRefferedBack->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>