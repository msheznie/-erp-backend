<table class="table table-responsive" id="bookInvSuppDets-table">
    <thead>
        <tr>
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
    @foreach($bookInvSuppDets as $bookInvSuppDet)
        <tr>
            <td>{!! $bookInvSuppDet->bookingSuppMasInvAutoID !!}</td>
            <td>{!! $bookInvSuppDet->unbilledgrvAutoID !!}</td>
            <td>{!! $bookInvSuppDet->companySystemID !!}</td>
            <td>{!! $bookInvSuppDet->companyID !!}</td>
            <td>{!! $bookInvSuppDet->supplierID !!}</td>
            <td>{!! $bookInvSuppDet->purchaseOrderID !!}</td>
            <td>{!! $bookInvSuppDet->grvAutoID !!}</td>
            <td>{!! $bookInvSuppDet->grvType !!}</td>
            <td>{!! $bookInvSuppDet->supplierTransactionCurrencyID !!}</td>
            <td>{!! $bookInvSuppDet->supplierTransactionCurrencyER !!}</td>
            <td>{!! $bookInvSuppDet->companyReportingCurrencyID !!}</td>
            <td>{!! $bookInvSuppDet->companyReportingER !!}</td>
            <td>{!! $bookInvSuppDet->localCurrencyID !!}</td>
            <td>{!! $bookInvSuppDet->localCurrencyER !!}</td>
            <td>{!! $bookInvSuppDet->supplierInvoOrderedAmount !!}</td>
            <td>{!! $bookInvSuppDet->supplierInvoAmount !!}</td>
            <td>{!! $bookInvSuppDet->transSupplierInvoAmount !!}</td>
            <td>{!! $bookInvSuppDet->localSupplierInvoAmount !!}</td>
            <td>{!! $bookInvSuppDet->rptSupplierInvoAmount !!}</td>
            <td>{!! $bookInvSuppDet->totTransactionAmount !!}</td>
            <td>{!! $bookInvSuppDet->totLocalAmount !!}</td>
            <td>{!! $bookInvSuppDet->totRptAmount !!}</td>
            <td>{!! $bookInvSuppDet->isAddon !!}</td>
            <td>{!! $bookInvSuppDet->invoiceBeforeGRVYN !!}</td>
            <td>{!! $bookInvSuppDet->timesReferred !!}</td>
            <td>{!! $bookInvSuppDet->timeStamp !!}</td>
            <td>
                {!! Form::open(['route' => ['bookInvSuppDets.destroy', $bookInvSuppDet->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('bookInvSuppDets.show', [$bookInvSuppDet->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('bookInvSuppDets.edit', [$bookInvSuppDet->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>