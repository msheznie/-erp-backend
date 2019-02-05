<table class="table table-responsive" id="quotationDetailsRefferedbacks-table">
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
    @foreach($quotationDetailsRefferedbacks as $quotationDetailsRefferedback)
        <tr>
            <td>{!! $quotationDetailsRefferedback->quotationDetailsID !!}</td>
            <td>{!! $quotationDetailsRefferedback->quotationMasterID !!}</td>
            <td>{!! $quotationDetailsRefferedback->versionNo !!}</td>
            <td>{!! $quotationDetailsRefferedback->itemAutoID !!}</td>
            <td>{!! $quotationDetailsRefferedback->itemSystemCode !!}</td>
            <td>{!! $quotationDetailsRefferedback->itemDescription !!}</td>
            <td>{!! $quotationDetailsRefferedback->itemCategory !!}</td>
            <td>{!! $quotationDetailsRefferedback->defaultUOMID !!}</td>
            <td>{!! $quotationDetailsRefferedback->itemReferenceNo !!}</td>
            <td>{!! $quotationDetailsRefferedback->defaultUOM !!}</td>
            <td>{!! $quotationDetailsRefferedback->unitOfMeasureID !!}</td>
            <td>{!! $quotationDetailsRefferedback->unitOfMeasure !!}</td>
            <td>{!! $quotationDetailsRefferedback->conversionRateUOM !!}</td>
            <td>{!! $quotationDetailsRefferedback->requestedQty !!}</td>
            <td>{!! $quotationDetailsRefferedback->invoicedYN !!}</td>
            <td>{!! $quotationDetailsRefferedback->comment !!}</td>
            <td>{!! $quotationDetailsRefferedback->remarks !!}</td>
            <td>{!! $quotationDetailsRefferedback->unittransactionAmount !!}</td>
            <td>{!! $quotationDetailsRefferedback->discountPercentage !!}</td>
            <td>{!! $quotationDetailsRefferedback->discountAmount !!}</td>
            <td>{!! $quotationDetailsRefferedback->discountTotal !!}</td>
            <td>{!! $quotationDetailsRefferedback->transactionAmount !!}</td>
            <td>{!! $quotationDetailsRefferedback->companyLocalAmount !!}</td>
            <td>{!! $quotationDetailsRefferedback->companyReportingAmount !!}</td>
            <td>{!! $quotationDetailsRefferedback->customerAmount !!}</td>
            <td>{!! $quotationDetailsRefferedback->companySystemID !!}</td>
            <td>{!! $quotationDetailsRefferedback->companyID !!}</td>
            <td>{!! $quotationDetailsRefferedback->createdUserGroup !!}</td>
            <td>{!! $quotationDetailsRefferedback->createdPCID !!}</td>
            <td>{!! $quotationDetailsRefferedback->createdUserID !!}</td>
            <td>{!! $quotationDetailsRefferedback->createdDateTime !!}</td>
            <td>{!! $quotationDetailsRefferedback->createdUserName !!}</td>
            <td>{!! $quotationDetailsRefferedback->modifiedPCID !!}</td>
            <td>{!! $quotationDetailsRefferedback->modifiedUserID !!}</td>
            <td>{!! $quotationDetailsRefferedback->modifiedDateTime !!}</td>
            <td>{!! $quotationDetailsRefferedback->modifiedUserName !!}</td>
            <td>{!! $quotationDetailsRefferedback->timesReferred !!}</td>
            <td>{!! $quotationDetailsRefferedback->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['quotationDetailsRefferedbacks.destroy', $quotationDetailsRefferedback->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('quotationDetailsRefferedbacks.show', [$quotationDetailsRefferedback->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('quotationDetailsRefferedbacks.edit', [$quotationDetailsRefferedback->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>