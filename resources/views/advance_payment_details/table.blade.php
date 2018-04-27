<table class="table table-responsive" id="advancePaymentDetails-table">
    <thead>
        <tr>
            <th>Paymasterautoid</th>
        <th>Poadvpaymentid</th>
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
    @foreach($advancePaymentDetails as $advancePaymentDetails)
        <tr>
            <td>{!! $advancePaymentDetails->PayMasterAutoId !!}</td>
            <td>{!! $advancePaymentDetails->poAdvPaymentID !!}</td>
            <td>{!! $advancePaymentDetails->companyID !!}</td>
            <td>{!! $advancePaymentDetails->purchaseOrderID !!}</td>
            <td>{!! $advancePaymentDetails->purchaseOrderCode !!}</td>
            <td>{!! $advancePaymentDetails->comments !!}</td>
            <td>{!! $advancePaymentDetails->paymentAmount !!}</td>
            <td>{!! $advancePaymentDetails->supplierTransCurrencyID !!}</td>
            <td>{!! $advancePaymentDetails->supplierTransER !!}</td>
            <td>{!! $advancePaymentDetails->supplierDefaultCurrencyID !!}</td>
            <td>{!! $advancePaymentDetails->supplierDefaultCurrencyER !!}</td>
            <td>{!! $advancePaymentDetails->localCurrencyID !!}</td>
            <td>{!! $advancePaymentDetails->localER !!}</td>
            <td>{!! $advancePaymentDetails->comRptCurrencyID !!}</td>
            <td>{!! $advancePaymentDetails->comRptER !!}</td>
            <td>{!! $advancePaymentDetails->supplierDefaultAmount !!}</td>
            <td>{!! $advancePaymentDetails->supplierTransAmount !!}</td>
            <td>{!! $advancePaymentDetails->localAmount !!}</td>
            <td>{!! $advancePaymentDetails->comRptAmount !!}</td>
            <td>{!! $advancePaymentDetails->timesReferred !!}</td>
            <td>{!! $advancePaymentDetails->timeStamp !!}</td>
            <td>
                {!! Form::open(['route' => ['advancePaymentDetails.destroy', $advancePaymentDetails->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('advancePaymentDetails.show', [$advancePaymentDetails->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('advancePaymentDetails.edit', [$advancePaymentDetails->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>