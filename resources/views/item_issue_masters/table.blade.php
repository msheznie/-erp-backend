<table class="table table-responsive" id="itemIssueMasters-table">
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
        <th>Itemissuecode</th>
        <th>Issuetype</th>
        <th>Issuedate</th>
        <th>Warehousefrom</th>
        <th>Warehousefromcode</th>
        <th>Warehousefromdes</th>
        <th>Contractid</th>
        <th>Jobno</th>
        <th>Workorderno</th>
        <th>Purchaseorderno</th>
        <th>Networkno</th>
        <th>Itemdeliveredonsitedate</th>
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
        <th>Onfirmedbyempsystemid</th>
        <th>Confirmedbyempid</th>
        <th>Confirmedbyname</th>
        <th>Confirmeddate</th>
        <th>Approved</th>
        <th>Directreqbyid</th>
        <th>Directreqbyname</th>
        <th>Product</th>
        <th>Volume</th>
        <th>Strength</th>
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
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($itemIssueMasters as $itemIssueMaster)
        <tr>
            <td>{!! $itemIssueMaster->companySystemID !!}</td>
            <td>{!! $itemIssueMaster->companyID !!}</td>
            <td>{!! $itemIssueMaster->serviceLineSystemID !!}</td>
            <td>{!! $itemIssueMaster->serviceLineCode !!}</td>
            <td>{!! $itemIssueMaster->companyFinanceYearID !!}</td>
            <td>{!! $itemIssueMaster->companyFinancePeriodID !!}</td>
            <td>{!! $itemIssueMaster->FYBiggin !!}</td>
            <td>{!! $itemIssueMaster->FYEnd !!}</td>
            <td>{!! $itemIssueMaster->documentSystemID !!}</td>
            <td>{!! $itemIssueMaster->documentID !!}</td>
            <td>{!! $itemIssueMaster->serialNo !!}</td>
            <td>{!! $itemIssueMaster->itemIssueCode !!}</td>
            <td>{!! $itemIssueMaster->issueType !!}</td>
            <td>{!! $itemIssueMaster->issueDate !!}</td>
            <td>{!! $itemIssueMaster->wareHouseFrom !!}</td>
            <td>{!! $itemIssueMaster->wareHouseFromCode !!}</td>
            <td>{!! $itemIssueMaster->wareHouseFromDes !!}</td>
            <td>{!! $itemIssueMaster->contractID !!}</td>
            <td>{!! $itemIssueMaster->jobNo !!}</td>
            <td>{!! $itemIssueMaster->workOrderNo !!}</td>
            <td>{!! $itemIssueMaster->purchaseOrderNo !!}</td>
            <td>{!! $itemIssueMaster->networkNo !!}</td>
            <td>{!! $itemIssueMaster->itemDeliveredOnSiteDate !!}</td>
            <td>{!! $itemIssueMaster->customerID !!}</td>
            <td>{!! $itemIssueMaster->issueRefNo !!}</td>
            <td>{!! $itemIssueMaster->reqDocID !!}</td>
            <td>{!! $itemIssueMaster->reqByID !!}</td>
            <td>{!! $itemIssueMaster->reqByName !!}</td>
            <td>{!! $itemIssueMaster->reqDate !!}</td>
            <td>{!! $itemIssueMaster->reqComment !!}</td>
            <td>{!! $itemIssueMaster->wellLocationFieldID !!}</td>
            <td>{!! $itemIssueMaster->fieldShortCode !!}</td>
            <td>{!! $itemIssueMaster->fieldName !!}</td>
            <td>{!! $itemIssueMaster->wellNO !!}</td>
            <td>{!! $itemIssueMaster->comment !!}</td>
            <td>{!! $itemIssueMaster->confirmedYN !!}</td>
            <td>{!! $itemIssueMaster->onfirmedByEmpSystemID !!}</td>
            <td>{!! $itemIssueMaster->confirmedByEmpID !!}</td>
            <td>{!! $itemIssueMaster->confirmedByName !!}</td>
            <td>{!! $itemIssueMaster->confirmedDate !!}</td>
            <td>{!! $itemIssueMaster->approved !!}</td>
            <td>{!! $itemIssueMaster->directReqByID !!}</td>
            <td>{!! $itemIssueMaster->directReqByName !!}</td>
            <td>{!! $itemIssueMaster->product !!}</td>
            <td>{!! $itemIssueMaster->volume !!}</td>
            <td>{!! $itemIssueMaster->strength !!}</td>
            <td>{!! $itemIssueMaster->createdDateTime !!}</td>
            <td>{!! $itemIssueMaster->createdUserGroup !!}</td>
            <td>{!! $itemIssueMaster->createdPCid !!}</td>
            <td>{!! $itemIssueMaster->createdUserSystemID !!}</td>
            <td>{!! $itemIssueMaster->createdUserID !!}</td>
            <td>{!! $itemIssueMaster->modifiedUserSystemID !!}</td>
            <td>{!! $itemIssueMaster->modifiedUser !!}</td>
            <td>{!! $itemIssueMaster->modifiedPc !!}</td>
            <td>{!! $itemIssueMaster->contRefNo !!}</td>
            <td>{!! $itemIssueMaster->is_closed !!}</td>
            <td>{!! $itemIssueMaster->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['itemIssueMasters.destroy', $itemIssueMaster->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('itemIssueMasters.show', [$itemIssueMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('itemIssueMasters.edit', [$itemIssueMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>