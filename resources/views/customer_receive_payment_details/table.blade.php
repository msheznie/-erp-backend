<table class="table table-responsive" id="customerReceivePaymentDetails-table">
    <thead>
        <tr>
            <th>Custreceivepaymentautoid</th>
        <th>Arautoid</th>
        <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Matchingdocid</th>
        <th>Addeddocumentsystemid</th>
        <th>Addeddocumentid</th>
        <th>Bookinginvcodesystem</th>
        <th>Bookinginvcode</th>
        <th>Bookingdate</th>
        <th>Comments</th>
        <th>Custtransactioncurrencyid</th>
        <th>Custtransactioncurrencyer</th>
        <th>Companyreportingcurrencyid</th>
        <th>Companyreportinger</th>
        <th>Localcurrencyid</th>
        <th>Localcurrencyer</th>
        <th>Bookingamounttrans</th>
        <th>Bookingamountlocal</th>
        <th>Bookingamountrpt</th>
        <th>Custreceivecurrencyid</th>
        <th>Custreceivecurrencyer</th>
        <th>Custbalanceamount</th>
        <th>Receiveamounttrans</th>
        <th>Receiveamountlocal</th>
        <th>Receiveamountrpt</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($customerReceivePaymentDetails as $customerReceivePaymentDetail)
        <tr>
            <td>{!! $customerReceivePaymentDetail->custReceivePaymentAutoID !!}</td>
            <td>{!! $customerReceivePaymentDetail->arAutoID !!}</td>
            <td>{!! $customerReceivePaymentDetail->companySystemID !!}</td>
            <td>{!! $customerReceivePaymentDetail->companyID !!}</td>
            <td>{!! $customerReceivePaymentDetail->matchingDocID !!}</td>
            <td>{!! $customerReceivePaymentDetail->addedDocumentSystemID !!}</td>
            <td>{!! $customerReceivePaymentDetail->addedDocumentID !!}</td>
            <td>{!! $customerReceivePaymentDetail->bookingInvCodeSystem !!}</td>
            <td>{!! $customerReceivePaymentDetail->bookingInvCode !!}</td>
            <td>{!! $customerReceivePaymentDetail->bookingDate !!}</td>
            <td>{!! $customerReceivePaymentDetail->comments !!}</td>
            <td>{!! $customerReceivePaymentDetail->custTransactionCurrencyID !!}</td>
            <td>{!! $customerReceivePaymentDetail->custTransactionCurrencyER !!}</td>
            <td>{!! $customerReceivePaymentDetail->companyReportingCurrencyID !!}</td>
            <td>{!! $customerReceivePaymentDetail->companyReportingER !!}</td>
            <td>{!! $customerReceivePaymentDetail->localCurrencyID !!}</td>
            <td>{!! $customerReceivePaymentDetail->localCurrencyER !!}</td>
            <td>{!! $customerReceivePaymentDetail->bookingAmountTrans !!}</td>
            <td>{!! $customerReceivePaymentDetail->bookingAmountLocal !!}</td>
            <td>{!! $customerReceivePaymentDetail->bookingAmountRpt !!}</td>
            <td>{!! $customerReceivePaymentDetail->custReceiveCurrencyID !!}</td>
            <td>{!! $customerReceivePaymentDetail->custReceiveCurrencyER !!}</td>
            <td>{!! $customerReceivePaymentDetail->custbalanceAmount !!}</td>
            <td>{!! $customerReceivePaymentDetail->receiveAmountTrans !!}</td>
            <td>{!! $customerReceivePaymentDetail->receiveAmountLocal !!}</td>
            <td>{!! $customerReceivePaymentDetail->receiveAmountRpt !!}</td>
            <td>{!! $customerReceivePaymentDetail->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['customerReceivePaymentDetails.destroy', $customerReceivePaymentDetail->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('customerReceivePaymentDetails.show', [$customerReceivePaymentDetail->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('customerReceivePaymentDetails.edit', [$customerReceivePaymentDetail->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>