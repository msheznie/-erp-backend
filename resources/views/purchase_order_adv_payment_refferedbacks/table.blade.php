<table class="table table-responsive" id="purchaseOrderAdvPaymentRefferedbacks-table">
    <thead>
        <tr>
            <th>Poadvpaymentid</th>
        <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Servicelinesystemid</th>
        <th>Servicelineid</th>
        <th>Poid</th>
        <th>Grvautoid</th>
        <th>Pocode</th>
        <th>Potermid</th>
        <th>Supplierid</th>
        <th>Supplierprimarycode</th>
        <th>Reqdate</th>
        <th>Narration</th>
        <th>Currencyid</th>
        <th>Reqamount</th>
        <th>Reqamounttranscur Amount</th>
        <th>Confirmedyn</th>
        <th>Approvedyn</th>
        <th>Selectedtopayment</th>
        <th>Fullypaid</th>
        <th>Isadvancepaymentyn</th>
        <th>Duedate</th>
        <th>Lcpaymentyn</th>
        <th>Requestedbyempid</th>
        <th>Requestedbyempname</th>
        <th>Reqamountinpotranscur</th>
        <th>Reqamountinpolocalcur</th>
        <th>Reqamountinporptcur</th>
        <th>Timesreferred</th>
        <th>Createddatetime</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($purchaseOrderAdvPaymentRefferedbacks as $purchaseOrderAdvPaymentRefferedback)
        <tr>
            <td>{!! $purchaseOrderAdvPaymentRefferedback->poAdvPaymentID !!}</td>
            <td>{!! $purchaseOrderAdvPaymentRefferedback->companySystemID !!}</td>
            <td>{!! $purchaseOrderAdvPaymentRefferedback->companyID !!}</td>
            <td>{!! $purchaseOrderAdvPaymentRefferedback->serviceLineSystemID !!}</td>
            <td>{!! $purchaseOrderAdvPaymentRefferedback->serviceLineID !!}</td>
            <td>{!! $purchaseOrderAdvPaymentRefferedback->poID !!}</td>
            <td>{!! $purchaseOrderAdvPaymentRefferedback->grvAutoID !!}</td>
            <td>{!! $purchaseOrderAdvPaymentRefferedback->poCode !!}</td>
            <td>{!! $purchaseOrderAdvPaymentRefferedback->poTermID !!}</td>
            <td>{!! $purchaseOrderAdvPaymentRefferedback->supplierID !!}</td>
            <td>{!! $purchaseOrderAdvPaymentRefferedback->SupplierPrimaryCode !!}</td>
            <td>{!! $purchaseOrderAdvPaymentRefferedback->reqDate !!}</td>
            <td>{!! $purchaseOrderAdvPaymentRefferedback->narration !!}</td>
            <td>{!! $purchaseOrderAdvPaymentRefferedback->currencyID !!}</td>
            <td>{!! $purchaseOrderAdvPaymentRefferedback->reqAmount !!}</td>
            <td>{!! $purchaseOrderAdvPaymentRefferedback->reqAmountTransCur_amount !!}</td>
            <td>{!! $purchaseOrderAdvPaymentRefferedback->confirmedYN !!}</td>
            <td>{!! $purchaseOrderAdvPaymentRefferedback->approvedYN !!}</td>
            <td>{!! $purchaseOrderAdvPaymentRefferedback->selectedToPayment !!}</td>
            <td>{!! $purchaseOrderAdvPaymentRefferedback->fullyPaid !!}</td>
            <td>{!! $purchaseOrderAdvPaymentRefferedback->isAdvancePaymentYN !!}</td>
            <td>{!! $purchaseOrderAdvPaymentRefferedback->dueDate !!}</td>
            <td>{!! $purchaseOrderAdvPaymentRefferedback->LCPaymentYN !!}</td>
            <td>{!! $purchaseOrderAdvPaymentRefferedback->requestedByEmpID !!}</td>
            <td>{!! $purchaseOrderAdvPaymentRefferedback->requestedByEmpName !!}</td>
            <td>{!! $purchaseOrderAdvPaymentRefferedback->reqAmountInPOTransCur !!}</td>
            <td>{!! $purchaseOrderAdvPaymentRefferedback->reqAmountInPOLocalCur !!}</td>
            <td>{!! $purchaseOrderAdvPaymentRefferedback->reqAmountInPORptCur !!}</td>
            <td>{!! $purchaseOrderAdvPaymentRefferedback->timesReferred !!}</td>
            <td>{!! $purchaseOrderAdvPaymentRefferedback->createdDateTime !!}</td>
            <td>{!! $purchaseOrderAdvPaymentRefferedback->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['purchaseOrderAdvPaymentRefferedbacks.destroy', $purchaseOrderAdvPaymentRefferedback->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('purchaseOrderAdvPaymentRefferedbacks.show', [$purchaseOrderAdvPaymentRefferedback->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('purchaseOrderAdvPaymentRefferedbacks.edit', [$purchaseOrderAdvPaymentRefferedback->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>