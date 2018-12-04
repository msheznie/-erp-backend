<table class="table table-responsive" id="itemIssueMasterRefferedBacks-table">
    <thead>
        <tr>
            <th>Itemissueautoid</th>
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
        <th>Itemissuecode</th>
        <th>Issuetype</th>
        <th>Issuedate</th>
        <th>Warehousefrom</th>
        <th>Warehousefromcode</th>
        <th>Warehousefromdes</th>
        <th>Contractuiid</th>
        <th>Contractid</th>
        <th>Jobno</th>
        <th>Workorderno</th>
        <th>Purchaseorderno</th>
        <th>Networkno</th>
        <th>Itemdeliveredonsitedate</th>
        <th>Customersystemid</th>
        <th>Customerid</th>
        <th>Issuerefno</th>
        <th>Reqdocid</th>
        <th>Reqbyid</th>
        <th>Reqbyname</th>
        <th>Reqdate</th>
        <th>Reqcomment</th>
        <th>Welllocationfieldid</th>
        <th>Fieldshortcode</th>
        <th>Fieldname</th>
        <th>Wellno</th>
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
        <th>Directreqbyid</th>
        <th>Directreqbyname</th>
        <th>Product</th>
        <th>Volume</th>
        <th>Strength</th>
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
        <th>Contrefno</th>
        <th>Is Closed</th>
        <th>Rolllevforapp Curr</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($itemIssueMasterRefferedBacks as $itemIssueMasterRefferedBack)
        <tr>
            <td>{!! $itemIssueMasterRefferedBack->itemIssueAutoID !!}</td>
            <td>{!! $itemIssueMasterRefferedBack->companySystemID !!}</td>
            <td>{!! $itemIssueMasterRefferedBack->companyID !!}</td>
            <td>{!! $itemIssueMasterRefferedBack->serviceLineSystemID !!}</td>
            <td>{!! $itemIssueMasterRefferedBack->serviceLineCode !!}</td>
            <td>{!! $itemIssueMasterRefferedBack->companyFinanceYearID !!}</td>
            <td>{!! $itemIssueMasterRefferedBack->companyFinancePeriodID !!}</td>
            <td>{!! $itemIssueMasterRefferedBack->FYBiggin !!}</td>
            <td>{!! $itemIssueMasterRefferedBack->FYEnd !!}</td>
            <td>{!! $itemIssueMasterRefferedBack->documentSystemID !!}</td>
            <td>{!! $itemIssueMasterRefferedBack->documentID !!}</td>
            <td>{!! $itemIssueMasterRefferedBack->serialNo !!}</td>
            <td>{!! $itemIssueMasterRefferedBack->itemIssueCode !!}</td>
            <td>{!! $itemIssueMasterRefferedBack->issueType !!}</td>
            <td>{!! $itemIssueMasterRefferedBack->issueDate !!}</td>
            <td>{!! $itemIssueMasterRefferedBack->wareHouseFrom !!}</td>
            <td>{!! $itemIssueMasterRefferedBack->wareHouseFromCode !!}</td>
            <td>{!! $itemIssueMasterRefferedBack->wareHouseFromDes !!}</td>
            <td>{!! $itemIssueMasterRefferedBack->contractUIID !!}</td>
            <td>{!! $itemIssueMasterRefferedBack->contractID !!}</td>
            <td>{!! $itemIssueMasterRefferedBack->jobNo !!}</td>
            <td>{!! $itemIssueMasterRefferedBack->workOrderNo !!}</td>
            <td>{!! $itemIssueMasterRefferedBack->purchaseOrderNo !!}</td>
            <td>{!! $itemIssueMasterRefferedBack->networkNo !!}</td>
            <td>{!! $itemIssueMasterRefferedBack->itemDeliveredOnSiteDate !!}</td>
            <td>{!! $itemIssueMasterRefferedBack->customerSystemID !!}</td>
            <td>{!! $itemIssueMasterRefferedBack->customerID !!}</td>
            <td>{!! $itemIssueMasterRefferedBack->issueRefNo !!}</td>
            <td>{!! $itemIssueMasterRefferedBack->reqDocID !!}</td>
            <td>{!! $itemIssueMasterRefferedBack->reqByID !!}</td>
            <td>{!! $itemIssueMasterRefferedBack->reqByName !!}</td>
            <td>{!! $itemIssueMasterRefferedBack->reqDate !!}</td>
            <td>{!! $itemIssueMasterRefferedBack->reqComment !!}</td>
            <td>{!! $itemIssueMasterRefferedBack->wellLocationFieldID !!}</td>
            <td>{!! $itemIssueMasterRefferedBack->fieldShortCode !!}</td>
            <td>{!! $itemIssueMasterRefferedBack->fieldName !!}</td>
            <td>{!! $itemIssueMasterRefferedBack->wellNO !!}</td>
            <td>{!! $itemIssueMasterRefferedBack->comment !!}</td>
            <td>{!! $itemIssueMasterRefferedBack->confirmedYN !!}</td>
            <td>{!! $itemIssueMasterRefferedBack->confirmedByEmpSystemID !!}</td>
            <td>{!! $itemIssueMasterRefferedBack->confirmedByEmpID !!}</td>
            <td>{!! $itemIssueMasterRefferedBack->confirmedByName !!}</td>
            <td>{!! $itemIssueMasterRefferedBack->confirmedDate !!}</td>
            <td>{!! $itemIssueMasterRefferedBack->approved !!}</td>
            <td>{!! $itemIssueMasterRefferedBack->approvedDate !!}</td>
            <td>{!! $itemIssueMasterRefferedBack->approvedByUserID !!}</td>
            <td>{!! $itemIssueMasterRefferedBack->approvedByUserSystemID !!}</td>
            <td>{!! $itemIssueMasterRefferedBack->directReqByID !!}</td>
            <td>{!! $itemIssueMasterRefferedBack->directReqByName !!}</td>
            <td>{!! $itemIssueMasterRefferedBack->product !!}</td>
            <td>{!! $itemIssueMasterRefferedBack->volume !!}</td>
            <td>{!! $itemIssueMasterRefferedBack->strength !!}</td>
            <td>{!! $itemIssueMasterRefferedBack->refferedBackYN !!}</td>
            <td>{!! $itemIssueMasterRefferedBack->timesReferred !!}</td>
            <td>{!! $itemIssueMasterRefferedBack->createdDateTime !!}</td>
            <td>{!! $itemIssueMasterRefferedBack->createdUserGroup !!}</td>
            <td>{!! $itemIssueMasterRefferedBack->createdPCid !!}</td>
            <td>{!! $itemIssueMasterRefferedBack->createdUserSystemID !!}</td>
            <td>{!! $itemIssueMasterRefferedBack->createdUserID !!}</td>
            <td>{!! $itemIssueMasterRefferedBack->modifiedUserSystemID !!}</td>
            <td>{!! $itemIssueMasterRefferedBack->modifiedUser !!}</td>
            <td>{!! $itemIssueMasterRefferedBack->modifiedPc !!}</td>
            <td>{!! $itemIssueMasterRefferedBack->contRefNo !!}</td>
            <td>{!! $itemIssueMasterRefferedBack->is_closed !!}</td>
            <td>{!! $itemIssueMasterRefferedBack->RollLevForApp_curr !!}</td>
            <td>{!! $itemIssueMasterRefferedBack->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['itemIssueMasterRefferedBacks.destroy', $itemIssueMasterRefferedBack->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('itemIssueMasterRefferedBacks.show', [$itemIssueMasterRefferedBack->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('itemIssueMasterRefferedBacks.edit', [$itemIssueMasterRefferedBack->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>