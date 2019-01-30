<table class="table table-responsive" id="quotationVersionDetails-table">
    <thead>
        <tr>
            <th>Quotationdetailsid</th>
        <th>Quotationmasterid</th>
        <th>Versionno</th>
        <th>Itemautoid</th>
        <th>Itemsystemcode</th>
        <th>Itemdescription</th>
        <th>Itemcategory</th>
        <th>Defaultuomid</th>
        <th>Itemreferenceno</th>
        <th>Defaultuom</th>
        <th>Unitofmeasureid</th>
        <th>Unitofmeasure</th>
        <th>Conversionrateuom</th>
        <th>Requestedqty</th>
        <th>Invoicedyn</th>
        <th>Comment</th>
        <th>Remarks</th>
        <th>Unittransactionamount</th>
        <th>Discountpercentage</th>
        <th>Discountamount</th>
        <th>Discounttotal</th>
        <th>Transactionamount</th>
        <th>Companylocalamount</th>
        <th>Companyreportingamount</th>
        <th>Customeramount</th>
        <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Createdusergroup</th>
        <th>Createdpcid</th>
        <th>Createduserid</th>
        <th>Createddatetime</th>
        <th>Createdusername</th>
        <th>Modifiedpcid</th>
        <th>Modifieduserid</th>
        <th>Modifieddatetime</th>
        <th>Modifiedusername</th>
        <th>Timesreferred</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($quotationVersionDetails as $quotationVersionDetails)
        <tr>
            <td>{!! $quotationVersionDetails->quotationDetailsID !!}</td>
            <td>{!! $quotationVersionDetails->quotationMasterID !!}</td>
            <td>{!! $quotationVersionDetails->versionNo !!}</td>
            <td>{!! $quotationVersionDetails->itemAutoID !!}</td>
            <td>{!! $quotationVersionDetails->itemSystemCode !!}</td>
            <td>{!! $quotationVersionDetails->itemDescription !!}</td>
            <td>{!! $quotationVersionDetails->itemCategory !!}</td>
            <td>{!! $quotationVersionDetails->defaultUOMID !!}</td>
            <td>{!! $quotationVersionDetails->itemReferenceNo !!}</td>
            <td>{!! $quotationVersionDetails->defaultUOM !!}</td>
            <td>{!! $quotationVersionDetails->unitOfMeasureID !!}</td>
            <td>{!! $quotationVersionDetails->unitOfMeasure !!}</td>
            <td>{!! $quotationVersionDetails->conversionRateUOM !!}</td>
            <td>{!! $quotationVersionDetails->requestedQty !!}</td>
            <td>{!! $quotationVersionDetails->invoicedYN !!}</td>
            <td>{!! $quotationVersionDetails->comment !!}</td>
            <td>{!! $quotationVersionDetails->remarks !!}</td>
            <td>{!! $quotationVersionDetails->unittransactionAmount !!}</td>
            <td>{!! $quotationVersionDetails->discountPercentage !!}</td>
            <td>{!! $quotationVersionDetails->discountAmount !!}</td>
            <td>{!! $quotationVersionDetails->discountTotal !!}</td>
            <td>{!! $quotationVersionDetails->transactionAmount !!}</td>
            <td>{!! $quotationVersionDetails->companyLocalAmount !!}</td>
            <td>{!! $quotationVersionDetails->companyReportingAmount !!}</td>
            <td>{!! $quotationVersionDetails->customerAmount !!}</td>
            <td>{!! $quotationVersionDetails->companySystemID !!}</td>
            <td>{!! $quotationVersionDetails->companyID !!}</td>
            <td>{!! $quotationVersionDetails->createdUserGroup !!}</td>
            <td>{!! $quotationVersionDetails->createdPCID !!}</td>
            <td>{!! $quotationVersionDetails->createdUserID !!}</td>
            <td>{!! $quotationVersionDetails->createdDateTime !!}</td>
            <td>{!! $quotationVersionDetails->createdUserName !!}</td>
            <td>{!! $quotationVersionDetails->modifiedPCID !!}</td>
            <td>{!! $quotationVersionDetails->modifiedUserID !!}</td>
            <td>{!! $quotationVersionDetails->modifiedDateTime !!}</td>
            <td>{!! $quotationVersionDetails->modifiedUserName !!}</td>
            <td>{!! $quotationVersionDetails->timesReferred !!}</td>
            <td>{!! $quotationVersionDetails->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['quotationVersionDetails.destroy', $quotationVersionDetails->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('quotationVersionDetails.show', [$quotationVersionDetails->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('quotationVersionDetails.edit', [$quotationVersionDetails->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>