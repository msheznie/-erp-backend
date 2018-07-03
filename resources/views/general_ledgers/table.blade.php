<table class="table table-responsive" id="generalLedgers-table">
    <thead>
        <tr>
            <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Servicelinesystemid</th>
        <th>Servicelinecode</th>
        <th>Mastercompanyid</th>
        <th>Documentsystemid</th>
        <th>Documentid</th>
        <th>Documentsystemcode</th>
        <th>Documentcode</th>
        <th>Documentdate</th>
        <th>Documentyear</th>
        <th>Documentmonth</th>
        <th>Chequenumber</th>
        <th>Invoicenumber</th>
        <th>Invoicedate</th>
        <th>Chartofaccountsystemid</th>
        <th>Glcode</th>
        <th>Glaccounttype</th>
        <th>Holdingshareholder</th>
        <th>Holdingpercentage</th>
        <th>Nonholdingpercentage</th>
        <th>Documentconfirmeddate</th>
        <th>Documentconfirmedby</th>
        <th>Documentfinalapproveddate</th>
        <th>Documentfinalapprovedby</th>
        <th>Documentnarration</th>
        <th>Contractuid</th>
        <th>Clientcontractid</th>
        <th>Suppliercodesystem</th>
        <th>Vendername</th>
        <th>Documenttranscurrencyid</th>
        <th>Documenttranscurrencyer</th>
        <th>Documenttransamount</th>
        <th>Documentlocalcurrencyid</th>
        <th>Documentlocalcurrencyer</th>
        <th>Documentlocalamount</th>
        <th>Documentrptcurrencyid</th>
        <th>Documentrptcurrencyer</th>
        <th>Documentrptamount</th>
        <th>Empid</th>
        <th>Employeepaymentyn</th>
        <th>Isrelatedpartyyn</th>
        <th>Hidefortax</th>
        <th>Documenttype</th>
        <th>Advancepaymenttypeid</th>
        <th>Ispdcchequeyn</th>
        <th>Isaddon</th>
        <th>Isallocationjv</th>
        <th>Createddatetime</th>
        <th>Createduserid</th>
        <th>Createduserpc</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($generalLedgers as $generalLedger)
        <tr>
            <td>{!! $generalLedger->companySystemID !!}</td>
            <td>{!! $generalLedger->companyID !!}</td>
            <td>{!! $generalLedger->serviceLineSystemID !!}</td>
            <td>{!! $generalLedger->serviceLineCode !!}</td>
            <td>{!! $generalLedger->masterCompanyID !!}</td>
            <td>{!! $generalLedger->documentSystemID !!}</td>
            <td>{!! $generalLedger->documentID !!}</td>
            <td>{!! $generalLedger->documentSystemCode !!}</td>
            <td>{!! $generalLedger->documentCode !!}</td>
            <td>{!! $generalLedger->documentDate !!}</td>
            <td>{!! $generalLedger->documentYear !!}</td>
            <td>{!! $generalLedger->documentMonth !!}</td>
            <td>{!! $generalLedger->chequeNumber !!}</td>
            <td>{!! $generalLedger->invoiceNumber !!}</td>
            <td>{!! $generalLedger->invoiceDate !!}</td>
            <td>{!! $generalLedger->chartOfAccountSystemID !!}</td>
            <td>{!! $generalLedger->glCode !!}</td>
            <td>{!! $generalLedger->glAccountType !!}</td>
            <td>{!! $generalLedger->holdingShareholder !!}</td>
            <td>{!! $generalLedger->holdingPercentage !!}</td>
            <td>{!! $generalLedger->nonHoldingPercentage !!}</td>
            <td>{!! $generalLedger->documentConfirmedDate !!}</td>
            <td>{!! $generalLedger->documentConfirmedBy !!}</td>
            <td>{!! $generalLedger->documentFinalApprovedDate !!}</td>
            <td>{!! $generalLedger->documentFinalApprovedBy !!}</td>
            <td>{!! $generalLedger->documentNarration !!}</td>
            <td>{!! $generalLedger->contractUID !!}</td>
            <td>{!! $generalLedger->clientContractID !!}</td>
            <td>{!! $generalLedger->supplierCodeSystem !!}</td>
            <td>{!! $generalLedger->venderName !!}</td>
            <td>{!! $generalLedger->documentTransCurrencyID !!}</td>
            <td>{!! $generalLedger->documentTransCurrencyER !!}</td>
            <td>{!! $generalLedger->documentTransAmount !!}</td>
            <td>{!! $generalLedger->documentLocalCurrencyID !!}</td>
            <td>{!! $generalLedger->documentLocalCurrencyER !!}</td>
            <td>{!! $generalLedger->documentLocalAmount !!}</td>
            <td>{!! $generalLedger->documentRptCurrencyID !!}</td>
            <td>{!! $generalLedger->documentRptCurrencyER !!}</td>
            <td>{!! $generalLedger->documentRptAmount !!}</td>
            <td>{!! $generalLedger->empID !!}</td>
            <td>{!! $generalLedger->employeePaymentYN !!}</td>
            <td>{!! $generalLedger->isRelatedPartyYN !!}</td>
            <td>{!! $generalLedger->hideForTax !!}</td>
            <td>{!! $generalLedger->documentType !!}</td>
            <td>{!! $generalLedger->advancePaymentTypeID !!}</td>
            <td>{!! $generalLedger->isPdcChequeYN !!}</td>
            <td>{!! $generalLedger->isAddon !!}</td>
            <td>{!! $generalLedger->isAllocationJV !!}</td>
            <td>{!! $generalLedger->createdDateTime !!}</td>
            <td>{!! $generalLedger->createdUserID !!}</td>
            <td>{!! $generalLedger->createdUserPC !!}</td>
            <td>{!! $generalLedger->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['generalLedgers.destroy', $generalLedger->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('generalLedgers.show', [$generalLedger->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('generalLedgers.edit', [$generalLedger->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>