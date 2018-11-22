<table class="table table-responsive" id="paySupplierInvoiceDetailReferbacks-table">
    <thead>
        <tr>
            <th>Paydetailautoid</th>
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
        <th>Createdusersystemid</th>
        <th>Createduserid</th>
        <th>Createdpcid</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($paySupplierInvoiceDetailReferbacks as $paySupplierInvoiceDetailReferback)
        <tr>
            <td>{!! $paySupplierInvoiceDetailReferback->payDetailAutoID !!}</td>
            <td>{!! $paySupplierInvoiceDetailReferback->PayMasterAutoId !!}</td>
            <td>{!! $paySupplierInvoiceDetailReferback->apAutoID !!}</td>
            <td>{!! $paySupplierInvoiceDetailReferback->matchingDocID !!}</td>
            <td>{!! $paySupplierInvoiceDetailReferback->companySystemID !!}</td>
            <td>{!! $paySupplierInvoiceDetailReferback->companyID !!}</td>
            <td>{!! $paySupplierInvoiceDetailReferback->addedDocumentSystemID !!}</td>
            <td>{!! $paySupplierInvoiceDetailReferback->addedDocumentID !!}</td>
            <td>{!! $paySupplierInvoiceDetailReferback->bookingInvSystemCode !!}</td>
            <td>{!! $paySupplierInvoiceDetailReferback->bookingInvDocCode !!}</td>
            <td>{!! $paySupplierInvoiceDetailReferback->bookingInvoiceDate !!}</td>
            <td>{!! $paySupplierInvoiceDetailReferback->addedDocumentType !!}</td>
            <td>{!! $paySupplierInvoiceDetailReferback->supplierCodeSystem !!}</td>
            <td>{!! $paySupplierInvoiceDetailReferback->supplierInvoiceNo !!}</td>
            <td>{!! $paySupplierInvoiceDetailReferback->supplierInvoiceDate !!}</td>
            <td>{!! $paySupplierInvoiceDetailReferback->supplierTransCurrencyID !!}</td>
            <td>{!! $paySupplierInvoiceDetailReferback->supplierTransER !!}</td>
            <td>{!! $paySupplierInvoiceDetailReferback->supplierInvoiceAmount !!}</td>
            <td>{!! $paySupplierInvoiceDetailReferback->supplierDefaultCurrencyID !!}</td>
            <td>{!! $paySupplierInvoiceDetailReferback->supplierDefaultCurrencyER !!}</td>
            <td>{!! $paySupplierInvoiceDetailReferback->supplierDefaultAmount !!}</td>
            <td>{!! $paySupplierInvoiceDetailReferback->localCurrencyID !!}</td>
            <td>{!! $paySupplierInvoiceDetailReferback->localER !!}</td>
            <td>{!! $paySupplierInvoiceDetailReferback->localAmount !!}</td>
            <td>{!! $paySupplierInvoiceDetailReferback->comRptCurrencyID !!}</td>
            <td>{!! $paySupplierInvoiceDetailReferback->comRptER !!}</td>
            <td>{!! $paySupplierInvoiceDetailReferback->comRptAmount !!}</td>
            <td>{!! $paySupplierInvoiceDetailReferback->supplierPaymentCurrencyID !!}</td>
            <td>{!! $paySupplierInvoiceDetailReferback->supplierPaymentER !!}</td>
            <td>{!! $paySupplierInvoiceDetailReferback->supplierPaymentAmount !!}</td>
            <td>{!! $paySupplierInvoiceDetailReferback->paymentBalancedAmount !!}</td>
            <td>{!! $paySupplierInvoiceDetailReferback->paymentSupplierDefaultAmount !!}</td>
            <td>{!! $paySupplierInvoiceDetailReferback->paymentLocalAmount !!}</td>
            <td>{!! $paySupplierInvoiceDetailReferback->paymentComRptAmount !!}</td>
            <td>{!! $paySupplierInvoiceDetailReferback->timesReferred !!}</td>
            <td>{!! $paySupplierInvoiceDetailReferback->modifiedUserID !!}</td>
            <td>{!! $paySupplierInvoiceDetailReferback->modifiedPCID !!}</td>
            <td>{!! $paySupplierInvoiceDetailReferback->createdDateTime !!}</td>
            <td>{!! $paySupplierInvoiceDetailReferback->createdUserSystemID !!}</td>
            <td>{!! $paySupplierInvoiceDetailReferback->createdUserID !!}</td>
            <td>{!! $paySupplierInvoiceDetailReferback->createdPcID !!}</td>
            <td>{!! $paySupplierInvoiceDetailReferback->timeStamp !!}</td>
            <td>
                {!! Form::open(['route' => ['paySupplierInvoiceDetailReferbacks.destroy', $paySupplierInvoiceDetailReferback->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('paySupplierInvoiceDetailReferbacks.show', [$paySupplierInvoiceDetailReferback->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('paySupplierInvoiceDetailReferbacks.edit', [$paySupplierInvoiceDetailReferback->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>