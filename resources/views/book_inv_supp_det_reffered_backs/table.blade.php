<table class="table table-responsive" id="bookInvSuppDetRefferedBacks-table">
    <thead>
        <tr>
            <th>Bookingsupinvoicedetautoid</th>
        <th>Bookingsuppmasinvautoid</th>
        <th>Unbilledgrvautoid</th>
        <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Supplierid</th>
        <th>Purchaseorderid</th>
        <th>Grvautoid</th>
        <th>Grvtype</th>
        <th>Suppliertransactioncurrencyid</th>
        <th>Suppliertransactioncurrencyer</th>
        <th>Companyreportingcurrencyid</th>
        <th>Companyreportinger</th>
        <th>Localcurrencyid</th>
        <th>Localcurrencyer</th>
        <th>Supplierinvoorderedamount</th>
        <th>Supplierinvoamount</th>
        <th>Transsupplierinvoamount</th>
        <th>Localsupplierinvoamount</th>
        <th>Rptsupplierinvoamount</th>
        <th>Tottransactionamount</th>
        <th>Totlocalamount</th>
        <th>Totrptamount</th>
        <th>Isaddon</th>
        <th>Invoicebeforegrvyn</th>
        <th>Timesreferred</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($bookInvSuppDetRefferedBacks as $bookInvSuppDetRefferedBack)
        <tr>
            <td>{!! $bookInvSuppDetRefferedBack->bookingSupInvoiceDetAutoID !!}</td>
            <td>{!! $bookInvSuppDetRefferedBack->bookingSuppMasInvAutoID !!}</td>
            <td>{!! $bookInvSuppDetRefferedBack->unbilledgrvAutoID !!}</td>
            <td>{!! $bookInvSuppDetRefferedBack->companySystemID !!}</td>
            <td>{!! $bookInvSuppDetRefferedBack->companyID !!}</td>
            <td>{!! $bookInvSuppDetRefferedBack->supplierID !!}</td>
            <td>{!! $bookInvSuppDetRefferedBack->purchaseOrderID !!}</td>
            <td>{!! $bookInvSuppDetRefferedBack->grvAutoID !!}</td>
            <td>{!! $bookInvSuppDetRefferedBack->grvType !!}</td>
            <td>{!! $bookInvSuppDetRefferedBack->supplierTransactionCurrencyID !!}</td>
            <td>{!! $bookInvSuppDetRefferedBack->supplierTransactionCurrencyER !!}</td>
            <td>{!! $bookInvSuppDetRefferedBack->companyReportingCurrencyID !!}</td>
            <td>{!! $bookInvSuppDetRefferedBack->companyReportingER !!}</td>
            <td>{!! $bookInvSuppDetRefferedBack->localCurrencyID !!}</td>
            <td>{!! $bookInvSuppDetRefferedBack->localCurrencyER !!}</td>
            <td>{!! $bookInvSuppDetRefferedBack->supplierInvoOrderedAmount !!}</td>
            <td>{!! $bookInvSuppDetRefferedBack->supplierInvoAmount !!}</td>
            <td>{!! $bookInvSuppDetRefferedBack->transSupplierInvoAmount !!}</td>
            <td>{!! $bookInvSuppDetRefferedBack->localSupplierInvoAmount !!}</td>
            <td>{!! $bookInvSuppDetRefferedBack->rptSupplierInvoAmount !!}</td>
            <td>{!! $bookInvSuppDetRefferedBack->totTransactionAmount !!}</td>
            <td>{!! $bookInvSuppDetRefferedBack->totLocalAmount !!}</td>
            <td>{!! $bookInvSuppDetRefferedBack->totRptAmount !!}</td>
            <td>{!! $bookInvSuppDetRefferedBack->isAddon !!}</td>
            <td>{!! $bookInvSuppDetRefferedBack->invoiceBeforeGRVYN !!}</td>
            <td>{!! $bookInvSuppDetRefferedBack->timesReferred !!}</td>
            <td>{!! $bookInvSuppDetRefferedBack->timeStamp !!}</td>
            <td>
                {!! Form::open(['route' => ['bookInvSuppDetRefferedBacks.destroy', $bookInvSuppDetRefferedBack->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('bookInvSuppDetRefferedBacks.show', [$bookInvSuppDetRefferedBack->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('bookInvSuppDetRefferedBacks.edit', [$bookInvSuppDetRefferedBack->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>