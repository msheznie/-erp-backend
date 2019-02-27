<table class="table table-responsive" id="gposInvoices-table">
    <thead>
        <tr>
            <th>Segmentid</th>
        <th>Segmentcode</th>
        <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Documentsystemid</th>
        <th>Documentid</th>
        <th>Serialno</th>
        <th>Invoicesequenceno</th>
        <th>Invoicecode</th>
        <th>Financialyearid</th>
        <th>Financialperiodid</th>
        <th>Fybegin</th>
        <th>Fyend</th>
        <th>Fyperioddatefrom</th>
        <th>Fyperioddateto</th>
        <th>Customerid</th>
        <th>Customercode</th>
        <th>Counterid</th>
        <th>Shiftid</th>
        <th>Memberid</th>
        <th>Membername</th>
        <th>Membercontactno</th>
        <th>Memberemail</th>
        <th>Invoicedate</th>
        <th>Subtotal</th>
        <th>Discountpercentage</th>
        <th>Discountamount</th>
        <th>Nettotal</th>
        <th>Paidamount</th>
        <th>Balanceamount</th>
        <th>Cashamount</th>
        <th>Chequeamount</th>
        <th>Chequeno</th>
        <th>Chequedate</th>
        <th>Cardamount</th>
        <th>Creditnoteid</th>
        <th>Creditnoteamount</th>
        <th>Giftcardid</th>
        <th>Giftcardamount</th>
        <th>Cardnumber</th>
        <th>Cardrefno</th>
        <th>Cardbank</th>
        <th>Iscreditsales</th>
        <th>Creditsalesamount</th>
        <th>Warehouseautoid</th>
        <th>Warehousecode</th>
        <th>Warehouselocation</th>
        <th>Warehousedescription</th>
        <th>Transactioncurrencyid</th>
        <th>Transactioncurrency</th>
        <th>Transactionexchangerate</th>
        <th>Transactioncurrencydecimalplaces</th>
        <th>Companylocalcurrencyid</th>
        <th>Companylocalcurrency</th>
        <th>Companylocalexchangerate</th>
        <th>Companylocalcurrencydecimalplaces</th>
        <th>Companyreportingcurrencyid</th>
        <th>Companyreportingcurrency</th>
        <th>Companyreportingexchangerate</th>
        <th>Companyreportingcurrencydecimalplaces</th>
        <th>Customercurrencyid</th>
        <th>Customercurrency</th>
        <th>Customercurrencyexchangerate</th>
        <th>Customercurrencydecimalplaces</th>
        <th>Customerreceivableautoid</th>
        <th>Customerreceivablesystemglcode</th>
        <th>Customerreceivableglaccount</th>
        <th>Customerreceivabledescription</th>
        <th>Customerreceivabletype</th>
        <th>Bankglautoid</th>
        <th>Banksystemglcode</th>
        <th>Bankglaccount</th>
        <th>Bankgldescription</th>
        <th>Bankgltype</th>
        <th>Bankcurrencyid</th>
        <th>Bankcurrency</th>
        <th>Bankcurrencyexchangerate</th>
        <th>Bankcurrencydecimalplaces</th>
        <th>Bankcurrencyamount</th>
        <th>Createdusergroup</th>
        <th>Createdpcid</th>
        <th>Createduserid</th>
        <th>Createdusername</th>
        <th>Createddatetime</th>
        <th>Modifiedpcid</th>
        <th>Modifieduserid</th>
        <th>Modifiedusername</th>
        <th>Modifieddatetime</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($gposInvoices as $gposInvoice)
        <tr>
            <td>{!! $gposInvoice->segmentID !!}</td>
            <td>{!! $gposInvoice->segmentCode !!}</td>
            <td>{!! $gposInvoice->companySystemID !!}</td>
            <td>{!! $gposInvoice->companyID !!}</td>
            <td>{!! $gposInvoice->documentSystemID !!}</td>
            <td>{!! $gposInvoice->documentID !!}</td>
            <td>{!! $gposInvoice->serialNo !!}</td>
            <td>{!! $gposInvoice->invoiceSequenceNo !!}</td>
            <td>{!! $gposInvoice->invoiceCode !!}</td>
            <td>{!! $gposInvoice->financialYearID !!}</td>
            <td>{!! $gposInvoice->financialPeriodID !!}</td>
            <td>{!! $gposInvoice->FYBegin !!}</td>
            <td>{!! $gposInvoice->FYEnd !!}</td>
            <td>{!! $gposInvoice->FYPeriodDateFrom !!}</td>
            <td>{!! $gposInvoice->FYPeriodDateTo !!}</td>
            <td>{!! $gposInvoice->customerID !!}</td>
            <td>{!! $gposInvoice->customerCode !!}</td>
            <td>{!! $gposInvoice->counterID !!}</td>
            <td>{!! $gposInvoice->shiftID !!}</td>
            <td>{!! $gposInvoice->memberID !!}</td>
            <td>{!! $gposInvoice->memberName !!}</td>
            <td>{!! $gposInvoice->memberContactNo !!}</td>
            <td>{!! $gposInvoice->memberEmail !!}</td>
            <td>{!! $gposInvoice->invoiceDate !!}</td>
            <td>{!! $gposInvoice->subTotal !!}</td>
            <td>{!! $gposInvoice->discountPercentage !!}</td>
            <td>{!! $gposInvoice->discountAmount !!}</td>
            <td>{!! $gposInvoice->netTotal !!}</td>
            <td>{!! $gposInvoice->paidAmount !!}</td>
            <td>{!! $gposInvoice->balanceAmount !!}</td>
            <td>{!! $gposInvoice->cashAmount !!}</td>
            <td>{!! $gposInvoice->chequeAmount !!}</td>
            <td>{!! $gposInvoice->chequeNo !!}</td>
            <td>{!! $gposInvoice->chequeDate !!}</td>
            <td>{!! $gposInvoice->cardAmount !!}</td>
            <td>{!! $gposInvoice->creditNoteID !!}</td>
            <td>{!! $gposInvoice->creditNoteAmount !!}</td>
            <td>{!! $gposInvoice->giftCardID !!}</td>
            <td>{!! $gposInvoice->giftCardAmount !!}</td>
            <td>{!! $gposInvoice->cardNumber !!}</td>
            <td>{!! $gposInvoice->cardRefNo !!}</td>
            <td>{!! $gposInvoice->cardBank !!}</td>
            <td>{!! $gposInvoice->isCreditSales !!}</td>
            <td>{!! $gposInvoice->creditSalesAmount !!}</td>
            <td>{!! $gposInvoice->wareHouseAutoID !!}</td>
            <td>{!! $gposInvoice->wareHouseCode !!}</td>
            <td>{!! $gposInvoice->wareHouseLocation !!}</td>
            <td>{!! $gposInvoice->wareHouseDescription !!}</td>
            <td>{!! $gposInvoice->transactionCurrencyID !!}</td>
            <td>{!! $gposInvoice->transactionCurrency !!}</td>
            <td>{!! $gposInvoice->transactionExchangeRate !!}</td>
            <td>{!! $gposInvoice->transactionCurrencyDecimalPlaces !!}</td>
            <td>{!! $gposInvoice->companyLocalCurrencyID !!}</td>
            <td>{!! $gposInvoice->companyLocalCurrency !!}</td>
            <td>{!! $gposInvoice->companyLocalExchangeRate !!}</td>
            <td>{!! $gposInvoice->companyLocalCurrencyDecimalPlaces !!}</td>
            <td>{!! $gposInvoice->companyReportingCurrencyID !!}</td>
            <td>{!! $gposInvoice->companyReportingCurrency !!}</td>
            <td>{!! $gposInvoice->companyReportingExchangeRate !!}</td>
            <td>{!! $gposInvoice->companyReportingCurrencyDecimalPlaces !!}</td>
            <td>{!! $gposInvoice->customerCurrencyID !!}</td>
            <td>{!! $gposInvoice->customerCurrency !!}</td>
            <td>{!! $gposInvoice->customerCurrencyExchangeRate !!}</td>
            <td>{!! $gposInvoice->customerCurrencyDecimalPlaces !!}</td>
            <td>{!! $gposInvoice->customerReceivableAutoID !!}</td>
            <td>{!! $gposInvoice->customerReceivableSystemGLCode !!}</td>
            <td>{!! $gposInvoice->customerReceivableGLAccount !!}</td>
            <td>{!! $gposInvoice->customerReceivableDescription !!}</td>
            <td>{!! $gposInvoice->customerReceivableType !!}</td>
            <td>{!! $gposInvoice->bankGLAutoID !!}</td>
            <td>{!! $gposInvoice->bankSystemGLCode !!}</td>
            <td>{!! $gposInvoice->bankGLAccount !!}</td>
            <td>{!! $gposInvoice->bankGLDescription !!}</td>
            <td>{!! $gposInvoice->bankGLType !!}</td>
            <td>{!! $gposInvoice->bankCurrencyID !!}</td>
            <td>{!! $gposInvoice->bankCurrency !!}</td>
            <td>{!! $gposInvoice->bankCurrencyExchangeRate !!}</td>
            <td>{!! $gposInvoice->bankCurrencyDecimalPlaces !!}</td>
            <td>{!! $gposInvoice->bankCurrencyAmount !!}</td>
            <td>{!! $gposInvoice->createdUserGroup !!}</td>
            <td>{!! $gposInvoice->createdPCID !!}</td>
            <td>{!! $gposInvoice->createdUserID !!}</td>
            <td>{!! $gposInvoice->createdUserName !!}</td>
            <td>{!! $gposInvoice->createdDateTime !!}</td>
            <td>{!! $gposInvoice->modifiedPCID !!}</td>
            <td>{!! $gposInvoice->modifiedUserID !!}</td>
            <td>{!! $gposInvoice->modifiedUserName !!}</td>
            <td>{!! $gposInvoice->modifiedDateTime !!}</td>
            <td>{!! $gposInvoice->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['gposInvoices.destroy', $gposInvoice->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('gposInvoices.show', [$gposInvoice->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('gposInvoices.edit', [$gposInvoice->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>