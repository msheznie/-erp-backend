<table class="table table-responsive" id="custReceivePaymentDetRefferedHistories-table">
    <thead>
        <tr>
            <th>Custrecivepaydetautoid</th>
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
    @foreach($custReceivePaymentDetRefferedHistories as $custReceivePaymentDetRefferedHistory)
        <tr>
            <td>{!! $custReceivePaymentDetRefferedHistory->custRecivePayDetAutoID !!}</td>
            <td>{!! $custReceivePaymentDetRefferedHistory->custReceivePaymentAutoID !!}</td>
            <td>{!! $custReceivePaymentDetRefferedHistory->arAutoID !!}</td>
            <td>{!! $custReceivePaymentDetRefferedHistory->companySystemID !!}</td>
            <td>{!! $custReceivePaymentDetRefferedHistory->companyID !!}</td>
            <td>{!! $custReceivePaymentDetRefferedHistory->matchingDocID !!}</td>
            <td>{!! $custReceivePaymentDetRefferedHistory->addedDocumentSystemID !!}</td>
            <td>{!! $custReceivePaymentDetRefferedHistory->addedDocumentID !!}</td>
            <td>{!! $custReceivePaymentDetRefferedHistory->bookingInvCodeSystem !!}</td>
            <td>{!! $custReceivePaymentDetRefferedHistory->bookingInvCode !!}</td>
            <td>{!! $custReceivePaymentDetRefferedHistory->bookingDate !!}</td>
            <td>{!! $custReceivePaymentDetRefferedHistory->comments !!}</td>
            <td>{!! $custReceivePaymentDetRefferedHistory->custTransactionCurrencyID !!}</td>
            <td>{!! $custReceivePaymentDetRefferedHistory->custTransactionCurrencyER !!}</td>
            <td>{!! $custReceivePaymentDetRefferedHistory->companyReportingCurrencyID !!}</td>
            <td>{!! $custReceivePaymentDetRefferedHistory->companyReportingER !!}</td>
            <td>{!! $custReceivePaymentDetRefferedHistory->localCurrencyID !!}</td>
            <td>{!! $custReceivePaymentDetRefferedHistory->localCurrencyER !!}</td>
            <td>{!! $custReceivePaymentDetRefferedHistory->bookingAmountTrans !!}</td>
            <td>{!! $custReceivePaymentDetRefferedHistory->bookingAmountLocal !!}</td>
            <td>{!! $custReceivePaymentDetRefferedHistory->bookingAmountRpt !!}</td>
            <td>{!! $custReceivePaymentDetRefferedHistory->custReceiveCurrencyID !!}</td>
            <td>{!! $custReceivePaymentDetRefferedHistory->custReceiveCurrencyER !!}</td>
            <td>{!! $custReceivePaymentDetRefferedHistory->custbalanceAmount !!}</td>
            <td>{!! $custReceivePaymentDetRefferedHistory->receiveAmountTrans !!}</td>
            <td>{!! $custReceivePaymentDetRefferedHistory->receiveAmountLocal !!}</td>
            <td>{!! $custReceivePaymentDetRefferedHistory->receiveAmountRpt !!}</td>
            <td>{!! $custReceivePaymentDetRefferedHistory->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['custReceivePaymentDetRefferedHistories.destroy', $custReceivePaymentDetRefferedHistory->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('custReceivePaymentDetRefferedHistories.show', [$custReceivePaymentDetRefferedHistory->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('custReceivePaymentDetRefferedHistories.edit', [$custReceivePaymentDetRefferedHistory->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>