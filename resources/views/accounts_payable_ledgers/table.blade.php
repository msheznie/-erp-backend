<table class="table table-responsive" id="accountsPayableLedgers-table">
    <thead>
        <tr>
            <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Documentsystemid</th>
        <th>Documentid</th>
        <th>Documentsystemcode</th>
        <th>Documentcode</th>
        <th>Documentdate</th>
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
        <th>Isinvoicelockedyn</th>
        <th>Lockedby</th>
        <th>Lockedbyempname</th>
        <th>Lockeddate</th>
        <th>Lockedcomments</th>
        <th>Invoicetype</th>
        <th>Selectedtopaymentinv</th>
        <th>Fullyinvoice</th>
        <th>Advancepaymenttypeid</th>
        <th>Createddatetime</th>
        <th>Createduserid</th>
        <th>Createdpcid</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($accountsPayableLedgers as $accountsPayableLedger)
        <tr>
            <td>{!! $accountsPayableLedger->companySystemID !!}</td>
            <td>{!! $accountsPayableLedger->companyID !!}</td>
            <td>{!! $accountsPayableLedger->documentSystemID !!}</td>
            <td>{!! $accountsPayableLedger->documentID !!}</td>
            <td>{!! $accountsPayableLedger->documentSystemCode !!}</td>
            <td>{!! $accountsPayableLedger->documentCode !!}</td>
            <td>{!! $accountsPayableLedger->documentDate !!}</td>
            <td>{!! $accountsPayableLedger->supplierCodeSystem !!}</td>
            <td>{!! $accountsPayableLedger->supplierInvoiceNo !!}</td>
            <td>{!! $accountsPayableLedger->supplierInvoiceDate !!}</td>
            <td>{!! $accountsPayableLedger->supplierTransCurrencyID !!}</td>
            <td>{!! $accountsPayableLedger->supplierTransER !!}</td>
            <td>{!! $accountsPayableLedger->supplierInvoiceAmount !!}</td>
            <td>{!! $accountsPayableLedger->supplierDefaultCurrencyID !!}</td>
            <td>{!! $accountsPayableLedger->supplierDefaultCurrencyER !!}</td>
            <td>{!! $accountsPayableLedger->supplierDefaultAmount !!}</td>
            <td>{!! $accountsPayableLedger->localCurrencyID !!}</td>
            <td>{!! $accountsPayableLedger->localER !!}</td>
            <td>{!! $accountsPayableLedger->localAmount !!}</td>
            <td>{!! $accountsPayableLedger->comRptCurrencyID !!}</td>
            <td>{!! $accountsPayableLedger->comRptER !!}</td>
            <td>{!! $accountsPayableLedger->comRptAmount !!}</td>
            <td>{!! $accountsPayableLedger->isInvoiceLockedYN !!}</td>
            <td>{!! $accountsPayableLedger->lockedBy !!}</td>
            <td>{!! $accountsPayableLedger->lockedByEmpName !!}</td>
            <td>{!! $accountsPayableLedger->lockedDate !!}</td>
            <td>{!! $accountsPayableLedger->lockedComments !!}</td>
            <td>{!! $accountsPayableLedger->invoiceType !!}</td>
            <td>{!! $accountsPayableLedger->selectedToPaymentInv !!}</td>
            <td>{!! $accountsPayableLedger->fullyInvoice !!}</td>
            <td>{!! $accountsPayableLedger->advancePaymentTypeID !!}</td>
            <td>{!! $accountsPayableLedger->createdDateTime !!}</td>
            <td>{!! $accountsPayableLedger->createdUserID !!}</td>
            <td>{!! $accountsPayableLedger->createdPcID !!}</td>
            <td>{!! $accountsPayableLedger->timeStamp !!}</td>
            <td>
                {!! Form::open(['route' => ['accountsPayableLedgers.destroy', $accountsPayableLedger->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('accountsPayableLedgers.show', [$accountsPayableLedger->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('accountsPayableLedgers.edit', [$accountsPayableLedger->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>