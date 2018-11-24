<table class="table table-responsive" id="advancePaymentReferbacks-table">
    <thead>
        <tr>
            <th>Advancepaymentdetailautoid</th>
        <th>Paymasterautoid</th>
        <th>Poadvpaymentid</th>
        <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Purchaseorderid</th>
        <th>Purchaseordercode</th>
        <th>Comments</th>
        <th>Paymentamount</th>
        <th>Suppliertranscurrencyid</th>
        <th>Suppliertranser</th>
        <th>Supplierdefaultcurrencyid</th>
        <th>Supplierdefaultcurrencyer</th>
        <th>Localcurrencyid</th>
        <th>Localer</th>
        <th>Comrptcurrencyid</th>
        <th>Comrpter</th>
        <th>Supplierdefaultamount</th>
        <th>Suppliertransamount</th>
        <th>Localamount</th>
        <th>Comrptamount</th>
        <th>Timesreferred</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($advancePaymentReferbacks as $advancePaymentReferback)
        <tr>
            <td>{!! $advancePaymentReferback->advancePaymentDetailAutoID !!}</td>
            <td>{!! $advancePaymentReferback->PayMasterAutoId !!}</td>
            <td>{!! $advancePaymentReferback->poAdvPaymentID !!}</td>
            <td>{!! $advancePaymentReferback->companySystemID !!}</td>
            <td>{!! $advancePaymentReferback->companyID !!}</td>
            <td>{!! $advancePaymentReferback->purchaseOrderID !!}</td>
            <td>{!! $advancePaymentReferback->purchaseOrderCode !!}</td>
            <td>{!! $advancePaymentReferback->comments !!}</td>
            <td>{!! $advancePaymentReferback->paymentAmount !!}</td>
            <td>{!! $advancePaymentReferback->supplierTransCurrencyID !!}</td>
            <td>{!! $advancePaymentReferback->supplierTransER !!}</td>
            <td>{!! $advancePaymentReferback->supplierDefaultCurrencyID !!}</td>
            <td>{!! $advancePaymentReferback->supplierDefaultCurrencyER !!}</td>
            <td>{!! $advancePaymentReferback->localCurrencyID !!}</td>
            <td>{!! $advancePaymentReferback->localER !!}</td>
            <td>{!! $advancePaymentReferback->comRptCurrencyID !!}</td>
            <td>{!! $advancePaymentReferback->comRptER !!}</td>
            <td>{!! $advancePaymentReferback->supplierDefaultAmount !!}</td>
            <td>{!! $advancePaymentReferback->supplierTransAmount !!}</td>
            <td>{!! $advancePaymentReferback->localAmount !!}</td>
            <td>{!! $advancePaymentReferback->comRptAmount !!}</td>
            <td>{!! $advancePaymentReferback->timesReferred !!}</td>
            <td>{!! $advancePaymentReferback->timeStamp !!}</td>
            <td>
                {!! Form::open(['route' => ['advancePaymentReferbacks.destroy', $advancePaymentReferback->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('advancePaymentReferbacks.show', [$advancePaymentReferback->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('advancePaymentReferbacks.edit', [$advancePaymentReferback->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>