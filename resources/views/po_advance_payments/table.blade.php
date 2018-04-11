<table class="table table-responsive" id="poAdvancePayments-table">
    <thead>
        <tr>
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
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($poAdvancePayments as $poAdvancePayment)
        <tr>
            <td>{!! $poAdvancePayment->companySystemID !!}</td>
            <td>{!! $poAdvancePayment->companyID !!}</td>
            <td>{!! $poAdvancePayment->serviceLineSystemID !!}</td>
            <td>{!! $poAdvancePayment->serviceLineID !!}</td>
            <td>{!! $poAdvancePayment->poID !!}</td>
            <td>{!! $poAdvancePayment->grvAutoID !!}</td>
            <td>{!! $poAdvancePayment->poCode !!}</td>
            <td>{!! $poAdvancePayment->poTermID !!}</td>
            <td>{!! $poAdvancePayment->supplierID !!}</td>
            <td>{!! $poAdvancePayment->SupplierPrimaryCode !!}</td>
            <td>{!! $poAdvancePayment->reqDate !!}</td>
            <td>{!! $poAdvancePayment->narration !!}</td>
            <td>{!! $poAdvancePayment->currencyID !!}</td>
            <td>{!! $poAdvancePayment->reqAmount !!}</td>
            <td>{!! $poAdvancePayment->reqAmountTransCur_amount !!}</td>
            <td>{!! $poAdvancePayment->confirmedYN !!}</td>
            <td>{!! $poAdvancePayment->approvedYN !!}</td>
            <td>{!! $poAdvancePayment->selectedToPayment !!}</td>
            <td>{!! $poAdvancePayment->fullyPaid !!}</td>
            <td>{!! $poAdvancePayment->isAdvancePaymentYN !!}</td>
            <td>{!! $poAdvancePayment->dueDate !!}</td>
            <td>{!! $poAdvancePayment->LCPaymentYN !!}</td>
            <td>{!! $poAdvancePayment->requestedByEmpID !!}</td>
            <td>{!! $poAdvancePayment->requestedByEmpName !!}</td>
            <td>{!! $poAdvancePayment->reqAmountInPOTransCur !!}</td>
            <td>{!! $poAdvancePayment->reqAmountInPOLocalCur !!}</td>
            <td>{!! $poAdvancePayment->reqAmountInPORptCur !!}</td>
            <td>{!! $poAdvancePayment->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['poAdvancePayments.destroy', $poAdvancePayment->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('poAdvancePayments.show', [$poAdvancePayment->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('poAdvancePayments.edit', [$poAdvancePayment->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>