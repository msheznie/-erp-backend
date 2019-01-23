<table class="table table-responsive" id="quotationDetails-table">
    <thead>
        <tr>
            <th>Quotationmasterid</th>
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
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($quotationDetails as $quotationDetails)
        <tr>
            <td>{!! $quotationDetails->quotationMasterID !!}</td>
            <td>{!! $quotationDetails->itemAutoID !!}</td>
            <td>{!! $quotationDetails->itemSystemCode !!}</td>
            <td>{!! $quotationDetails->itemDescription !!}</td>
            <td>{!! $quotationDetails->itemCategory !!}</td>
            <td>{!! $quotationDetails->defaultUOMID !!}</td>
            <td>{!! $quotationDetails->itemReferenceNo !!}</td>
            <td>{!! $quotationDetails->defaultUOM !!}</td>
            <td>{!! $quotationDetails->unitOfMeasureID !!}</td>
            <td>{!! $quotationDetails->unitOfMeasure !!}</td>
            <td>{!! $quotationDetails->conversionRateUOM !!}</td>
            <td>{!! $quotationDetails->requestedQty !!}</td>
            <td>{!! $quotationDetails->invoicedYN !!}</td>
            <td>{!! $quotationDetails->comment !!}</td>
            <td>{!! $quotationDetails->remarks !!}</td>
            <td>{!! $quotationDetails->unittransactionAmount !!}</td>
            <td>{!! $quotationDetails->discountPercentage !!}</td>
            <td>{!! $quotationDetails->discountAmount !!}</td>
            <td>{!! $quotationDetails->discountTotal !!}</td>
            <td>{!! $quotationDetails->transactionAmount !!}</td>
            <td>{!! $quotationDetails->companyLocalAmount !!}</td>
            <td>{!! $quotationDetails->companyReportingAmount !!}</td>
            <td>{!! $quotationDetails->customerAmount !!}</td>
            <td>{!! $quotationDetails->companySystemID !!}</td>
            <td>{!! $quotationDetails->companyID !!}</td>
            <td>{!! $quotationDetails->createdUserGroup !!}</td>
            <td>{!! $quotationDetails->createdPCID !!}</td>
            <td>{!! $quotationDetails->createdUserID !!}</td>
            <td>{!! $quotationDetails->createdDateTime !!}</td>
            <td>{!! $quotationDetails->createdUserName !!}</td>
            <td>{!! $quotationDetails->modifiedPCID !!}</td>
            <td>{!! $quotationDetails->modifiedUserID !!}</td>
            <td>{!! $quotationDetails->modifiedDateTime !!}</td>
            <td>{!! $quotationDetails->modifiedUserName !!}</td>
            <td>{!! $quotationDetails->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['quotationDetails.destroy', $quotationDetails->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('quotationDetails.show', [$quotationDetails->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('quotationDetails.edit', [$quotationDetails->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>