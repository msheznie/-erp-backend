<table class="table table-responsive" id="accountsReceivableLedgers-table">
    <thead>
        <tr>
            <th>Companyid</th>
        <th>Documentid</th>
        <th>Documentcodesystem</th>
        <th>Documentcode</th>
        <th>Documentdate</th>
        <th>Customerid</th>
        <th>Invoiceno</th>
        <th>Invoicedate</th>
        <th>Custtranscurrencyid</th>
        <th>Custtranser</th>
        <th>Custinvoiceamount</th>
        <th>Custdefaultcurrencyid</th>
        <th>Custdefaultcurrencyer</th>
        <th>Custdefaultamount</th>
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
        <th>Selectedtopaymentinv</th>
        <th>Fullyinvoiced</th>
        <th>Createddatetime</th>
        <th>Createduserid</th>
        <th>Createdpcid</th>
        <th>Documenttype</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($accountsReceivableLedgers as $accountsReceivableLedger)
        <tr>
            <td>{!! $accountsReceivableLedger->companyID !!}</td>
            <td>{!! $accountsReceivableLedger->documentID !!}</td>
            <td>{!! $accountsReceivableLedger->documentCodeSystem !!}</td>
            <td>{!! $accountsReceivableLedger->documentCode !!}</td>
            <td>{!! $accountsReceivableLedger->documentDate !!}</td>
            <td>{!! $accountsReceivableLedger->customerID !!}</td>
            <td>{!! $accountsReceivableLedger->InvoiceNo !!}</td>
            <td>{!! $accountsReceivableLedger->InvoiceDate !!}</td>
            <td>{!! $accountsReceivableLedger->custTransCurrencyID !!}</td>
            <td>{!! $accountsReceivableLedger->custTransER !!}</td>
            <td>{!! $accountsReceivableLedger->custInvoiceAmount !!}</td>
            <td>{!! $accountsReceivableLedger->custDefaultCurrencyID !!}</td>
            <td>{!! $accountsReceivableLedger->custDefaultCurrencyER !!}</td>
            <td>{!! $accountsReceivableLedger->custDefaultAmount !!}</td>
            <td>{!! $accountsReceivableLedger->localCurrencyID !!}</td>
            <td>{!! $accountsReceivableLedger->localER !!}</td>
            <td>{!! $accountsReceivableLedger->localAmount !!}</td>
            <td>{!! $accountsReceivableLedger->comRptCurrencyID !!}</td>
            <td>{!! $accountsReceivableLedger->comRptER !!}</td>
            <td>{!! $accountsReceivableLedger->comRptAmount !!}</td>
            <td>{!! $accountsReceivableLedger->isInvoiceLockedYN !!}</td>
            <td>{!! $accountsReceivableLedger->lockedBy !!}</td>
            <td>{!! $accountsReceivableLedger->lockedByEmpName !!}</td>
            <td>{!! $accountsReceivableLedger->lockedDate !!}</td>
            <td>{!! $accountsReceivableLedger->lockedComments !!}</td>
            <td>{!! $accountsReceivableLedger->selectedToPaymentInv !!}</td>
            <td>{!! $accountsReceivableLedger->fullyInvoiced !!}</td>
            <td>{!! $accountsReceivableLedger->createdDateTime !!}</td>
            <td>{!! $accountsReceivableLedger->createdUserID !!}</td>
            <td>{!! $accountsReceivableLedger->createdPcID !!}</td>
            <td>{!! $accountsReceivableLedger->documentType !!}</td>
            <td>{!! $accountsReceivableLedger->timeStamp !!}</td>
            <td>
                {!! Form::open(['route' => ['accountsReceivableLedgers.destroy', $accountsReceivableLedger->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('accountsReceivableLedgers.show', [$accountsReceivableLedger->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('accountsReceivableLedgers.edit', [$accountsReceivableLedger->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>