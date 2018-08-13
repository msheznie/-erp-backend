<table class="table table-responsive" id="paySupplierInvoiceDetails-table">
    <thead>
        <tr>
            <th>Paymasterautoid</th>
        <th>Apautoid</th>
        <th>Matchingdocid</th>
        <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Addeddocumentsystemid</th>
        <th>Addeddocumentid</th>
        <th>Bookinginvsystemcode</th>
        <th>Bookinginvdoccode</th>
        <th>Bookinginvoicedate</th>
        <th>Addeddocumenttype</th>
        <th>Suppliercodesystem</th>
        <th>Supplierinvoiceno</th>
        <th>Supplierinvoicedate</th>
        <th>Suppliertranscurrencyid</th>
        <th>Suppliertranser</th>
        <th>Supplierinvoiceamount</th>
        <th>Supplierdefaultcurrencyid</th>
        <th>Supplierdefaultcurrencyer</th>
        <th>Supplierdefaultamount</th>
        <th>Localcurrencyid</th>
        <th>Localer</th>
        <th>Localamount</th>
        <th>Comrptcurrencyid</th>
        <th>Comrpter</th>
        <th>Comrptamount</th>
        <th>Supplierpaymentcurrencyid</th>
        <th>Supplierpaymenter</th>
        <th>Supplierpaymentamount</th>
        <th>Paymentbalancedamount</th>
        <th>Paymentsupplierdefaultamount</th>
        <th>Paymentlocalamount</th>
        <th>Paymentcomrptamount</th>
        <th>Timesreferred</th>
        <th>Modifieduserid</th>
        <th>Modifiedpcid</th>
        <th>Createddatetime</th>
        <th>Createduserid</th>
        <th>Createdpcid</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($paySupplierInvoiceDetails as $paySupplierInvoiceDetail)
        <tr>
            <td>{!! $paySupplierInvoiceDetail->PayMasterAutoId !!}</td>
            <td>{!! $paySupplierInvoiceDetail->apAutoID !!}</td>
            <td>{!! $paySupplierInvoiceDetail->matchingDocID !!}</td>
            <td>{!! $paySupplierInvoiceDetail->companySystemID !!}</td>
            <td>{!! $paySupplierInvoiceDetail->companyID !!}</td>
            <td>{!! $paySupplierInvoiceDetail->addedDocumentSystemID !!}</td>
            <td>{!! $paySupplierInvoiceDetail->addedDocumentID !!}</td>
            <td>{!! $paySupplierInvoiceDetail->bookingInvSystemCode !!}</td>
            <td>{!! $paySupplierInvoiceDetail->bookingInvDocCode !!}</td>
            <td>{!! $paySupplierInvoiceDetail->bookingInvoiceDate !!}</td>
            <td>{!! $paySupplierInvoiceDetail->addedDocumentType !!}</td>
            <td>{!! $paySupplierInvoiceDetail->supplierCodeSystem !!}</td>
            <td>{!! $paySupplierInvoiceDetail->supplierInvoiceNo !!}</td>
            <td>{!! $paySupplierInvoiceDetail->supplierInvoiceDate !!}</td>
            <td>{!! $paySupplierInvoiceDetail->supplierTransCurrencyID !!}</td>
            <td>{!! $paySupplierInvoiceDetail->supplierTransER !!}</td>
            <td>{!! $paySupplierInvoiceDetail->supplierInvoiceAmount !!}</td>
            <td>{!! $paySupplierInvoiceDetail->supplierDefaultCurrencyID !!}</td>
            <td>{!! $paySupplierInvoiceDetail->supplierDefaultCurrencyER !!}</td>
            <td>{!! $paySupplierInvoiceDetail->supplierDefaultAmount !!}</td>
            <td>{!! $paySupplierInvoiceDetail->localCurrencyID !!}</td>
            <td>{!! $paySupplierInvoiceDetail->localER !!}</td>
            <td>{!! $paySupplierInvoiceDetail->localAmount !!}</td>
            <td>{!! $paySupplierInvoiceDetail->comRptCurrencyID !!}</td>
            <td>{!! $paySupplierInvoiceDetail->comRptER !!}</td>
            <td>{!! $paySupplierInvoiceDetail->comRptAmount !!}</td>
            <td>{!! $paySupplierInvoiceDetail->supplierPaymentCurrencyID !!}</td>
            <td>{!! $paySupplierInvoiceDetail->supplierPaymentER !!}</td>
            <td>{!! $paySupplierInvoiceDetail->supplierPaymentAmount !!}</td>
            <td>{!! $paySupplierInvoiceDetail->paymentBalancedAmount !!}</td>
            <td>{!! $paySupplierInvoiceDetail->paymentSupplierDefaultAmount !!}</td>
            <td>{!! $paySupplierInvoiceDetail->paymentLocalAmount !!}</td>
            <td>{!! $paySupplierInvoiceDetail->paymentComRptAmount !!}</td>
            <td>{!! $paySupplierInvoiceDetail->timesReferred !!}</td>
            <td>{!! $paySupplierInvoiceDetail->modifiedUserID !!}</td>
            <td>{!! $paySupplierInvoiceDetail->modifiedPCID !!}</td>
            <td>{!! $paySupplierInvoiceDetail->createdDateTime !!}</td>
            <td>{!! $paySupplierInvoiceDetail->createdUserID !!}</td>
            <td>{!! $paySupplierInvoiceDetail->createdPcID !!}</td>
            <td>{!! $paySupplierInvoiceDetail->timeStamp !!}</td>
            <td>
                {!! Form::open(['route' => ['paySupplierInvoiceDetails.destroy', $paySupplierInvoiceDetail->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('paySupplierInvoiceDetails.show', [$paySupplierInvoiceDetail->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('paySupplierInvoiceDetails.edit', [$paySupplierInvoiceDetail->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>