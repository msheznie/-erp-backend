<table class="table table-responsive" id="itemReturnMasters-table">
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
        <th>Posteddate</th>
        <th>Rolllevforapp Curr</th>
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
    @foreach($itemReturnMasters as $itemReturnMaster)
        <tr>
            <td>{!! $itemReturnMaster->companySystemID !!}</td>
            <td>{!! $itemReturnMaster->companyID !!}</td>
            <td>{!! $itemReturnMaster->serviceLineSystemID !!}</td>
            <td>{!! $itemReturnMaster->serviceLineCode !!}</td>
            <td>{!! $itemReturnMaster->companyFinanceYearID !!}</td>
            <td>{!! $itemReturnMaster->companyFinancePeriodID !!}</td>
            <td>{!! $itemReturnMaster->FYBiggin !!}</td>
            <td>{!! $itemReturnMaster->FYEnd !!}</td>
            <td>{!! $itemReturnMaster->documentSystemID !!}</td>
            <td>{!! $itemReturnMaster->documentID !!}</td>
            <td>{!! $itemReturnMaster->serialNo !!}</td>
            <td>{!! $itemReturnMaster->itemReturnCode !!}</td>
            <td>{!! $itemReturnMaster->ReturnType !!}</td>
            <td>{!! $itemReturnMaster->ReturnDate !!}</td>
            <td>{!! $itemReturnMaster->ReturnedBy !!}</td>
            <td>{!! $itemReturnMaster->jobNo !!}</td>
            <td>{!! $itemReturnMaster->customerID !!}</td>
            <td>{!! $itemReturnMaster->wareHouseLocation !!}</td>
            <td>{!! $itemReturnMaster->ReturnRefNo !!}</td>
            <td>{!! $itemReturnMaster->comment !!}</td>
            <td>{!! $itemReturnMaster->confirmedYN !!}</td>
            <td>{!! $itemReturnMaster->confirmedByEmpSystemID !!}</td>
            <td>{!! $itemReturnMaster->confirmedByEmpID !!}</td>
            <td>{!! $itemReturnMaster->confirmedByName !!}</td>
            <td>{!! $itemReturnMaster->confirmedDate !!}</td>
            <td>{!! $itemReturnMaster->approved !!}</td>
            <td>{!! $itemReturnMaster->approvedDate !!}</td>
            <td>{!! $itemReturnMaster->postedDate !!}</td>
            <td>{!! $itemReturnMaster->RollLevForApp_curr !!}</td>
            <td>{!! $itemReturnMaster->createdDateTime !!}</td>
            <td>{!! $itemReturnMaster->createdUserGroup !!}</td>
            <td>{!! $itemReturnMaster->createdPCid !!}</td>
            <td>{!! $itemReturnMaster->createdUserSystemID !!}</td>
            <td>{!! $itemReturnMaster->createdUserID !!}</td>
            <td>{!! $itemReturnMaster->modifiedUserSystemID !!}</td>
            <td>{!! $itemReturnMaster->modifiedUser !!}</td>
            <td>{!! $itemReturnMaster->modifiedPc !!}</td>
            <td>{!! $itemReturnMaster->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['itemReturnMasters.destroy', $itemReturnMaster->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('itemReturnMasters.show', [$itemReturnMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('itemReturnMasters.edit', [$itemReturnMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>