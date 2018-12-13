<table class="table table-responsive" id="paymentBankTransferDetailRefferedBacks-table">
    <thead>
        <tr>
            <th>Bankledgerautoid</th>
        <th>Bankrecautoid</th>
        <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Documentsystemid</th>
        <th>Documentid</th>
        <th>Documentsystemcode</th>
        <th>Documentcode</th>
        <th>Documentdate</th>
        <th>Posteddate</th>
        <th>Documentnarration</th>
        <th>Bankid</th>
        <th>Bankaccountid</th>
        <th>Bankcurrency</th>
        <th>Bankcurrencyer</th>
        <th>Documentchequeno</th>
        <th>Documentchequedate</th>
        <th>Payeeid</th>
        <th>Payeecode</th>
        <th>Payeename</th>
        <th>Payeeglcodeid</th>
        <th>Payeeglcode</th>
        <th>Suppliertranscurrencyid</th>
        <th>Suppliertranscurrencyer</th>
        <th>Localcurrencyid</th>
        <th>Localcurrencyer</th>
        <th>Companyrptcurrencyid</th>
        <th>Companyrptcurrencyer</th>
        <th>Payamountbank</th>
        <th>Payamountsupptrans</th>
        <th>Payamountcomplocal</th>
        <th>Payamountcomprpt</th>
        <th>Invoicetype</th>
        <th>Trscollectedyn</th>
        <th>Trscollectedbyempsystemid</th>
        <th>Trscollectedbyempid</th>
        <th>Trscollectedbyempname</th>
        <th>Trscollecteddate</th>
        <th>Trsclearedyn</th>
        <th>Trscleareddate</th>
        <th>Trsclearedbyempsystemid</th>
        <th>Trsclearedbyempid</th>
        <th>Trsclearedbyempname</th>
        <th>Trsclearedamount</th>
        <th>Bankclearedyn</th>
        <th>Bankclearedamount</th>
        <th>Bankreconciliationdate</th>
        <th>Bankrecyear</th>
        <th>Bankrecmonth</th>
        <th>Bankcleareddate</th>
        <th>Bankclearedbyempsystemid</th>
        <th>Bankclearedbyempid</th>
        <th>Bankclearedbyempname</th>
        <th>Paymentbanktransferid</th>
        <th>Pulledtobanktransferyn</th>
        <th>Chequepaymentyn</th>
        <th>Chequeprintedyn</th>
        <th>Chequeprinteddatetime</th>
        <th>Chequeprintedbyempsystemid</th>
        <th>Chequeprintedbyempid</th>
        <th>Chequeprintedbyempname</th>
        <th>Chequesenttotreasury</th>
        <th>Chequesenttotreasurydate</th>
        <th>Chequesenttotreasurybyempsystemid</th>
        <th>Chequesenttotreasurybyempid</th>
        <th>Chequesenttotreasurybyempname</th>
        <th>Timesreferred</th>
        <th>Createdusersystemid</th>
        <th>Createduserid</th>
        <th>Createdpcid</th>
        <th>Modifiedusersystemid</th>
        <th>Modifieduser</th>
        <th>Modifiedpc</th>
        <th>Createddatetime</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($paymentBankTransferDetailRefferedBacks as $paymentBankTransferDetailRefferedBack)
        <tr>
            <td>{!! $paymentBankTransferDetailRefferedBack->bankLedgerAutoID !!}</td>
            <td>{!! $paymentBankTransferDetailRefferedBack->bankRecAutoID !!}</td>
            <td>{!! $paymentBankTransferDetailRefferedBack->companySystemID !!}</td>
            <td>{!! $paymentBankTransferDetailRefferedBack->companyID !!}</td>
            <td>{!! $paymentBankTransferDetailRefferedBack->documentSystemID !!}</td>
            <td>{!! $paymentBankTransferDetailRefferedBack->documentID !!}</td>
            <td>{!! $paymentBankTransferDetailRefferedBack->documentSystemCode !!}</td>
            <td>{!! $paymentBankTransferDetailRefferedBack->documentCode !!}</td>
            <td>{!! $paymentBankTransferDetailRefferedBack->documentDate !!}</td>
            <td>{!! $paymentBankTransferDetailRefferedBack->postedDate !!}</td>
            <td>{!! $paymentBankTransferDetailRefferedBack->documentNarration !!}</td>
            <td>{!! $paymentBankTransferDetailRefferedBack->bankID !!}</td>
            <td>{!! $paymentBankTransferDetailRefferedBack->bankAccountID !!}</td>
            <td>{!! $paymentBankTransferDetailRefferedBack->bankCurrency !!}</td>
            <td>{!! $paymentBankTransferDetailRefferedBack->bankCurrencyER !!}</td>
            <td>{!! $paymentBankTransferDetailRefferedBack->documentChequeNo !!}</td>
            <td>{!! $paymentBankTransferDetailRefferedBack->documentChequeDate !!}</td>
            <td>{!! $paymentBankTransferDetailRefferedBack->payeeID !!}</td>
            <td>{!! $paymentBankTransferDetailRefferedBack->payeeCode !!}</td>
            <td>{!! $paymentBankTransferDetailRefferedBack->payeeName !!}</td>
            <td>{!! $paymentBankTransferDetailRefferedBack->payeeGLCodeID !!}</td>
            <td>{!! $paymentBankTransferDetailRefferedBack->payeeGLCode !!}</td>
            <td>{!! $paymentBankTransferDetailRefferedBack->supplierTransCurrencyID !!}</td>
            <td>{!! $paymentBankTransferDetailRefferedBack->supplierTransCurrencyER !!}</td>
            <td>{!! $paymentBankTransferDetailRefferedBack->localCurrencyID !!}</td>
            <td>{!! $paymentBankTransferDetailRefferedBack->localCurrencyER !!}</td>
            <td>{!! $paymentBankTransferDetailRefferedBack->companyRptCurrencyID !!}</td>
            <td>{!! $paymentBankTransferDetailRefferedBack->companyRptCurrencyER !!}</td>
            <td>{!! $paymentBankTransferDetailRefferedBack->payAmountBank !!}</td>
            <td>{!! $paymentBankTransferDetailRefferedBack->payAmountSuppTrans !!}</td>
            <td>{!! $paymentBankTransferDetailRefferedBack->payAmountCompLocal !!}</td>
            <td>{!! $paymentBankTransferDetailRefferedBack->payAmountCompRpt !!}</td>
            <td>{!! $paymentBankTransferDetailRefferedBack->invoiceType !!}</td>
            <td>{!! $paymentBankTransferDetailRefferedBack->trsCollectedYN !!}</td>
            <td>{!! $paymentBankTransferDetailRefferedBack->trsCollectedByEmpSystemID !!}</td>
            <td>{!! $paymentBankTransferDetailRefferedBack->trsCollectedByEmpID !!}</td>
            <td>{!! $paymentBankTransferDetailRefferedBack->trsCollectedByEmpName !!}</td>
            <td>{!! $paymentBankTransferDetailRefferedBack->trsCollectedDate !!}</td>
            <td>{!! $paymentBankTransferDetailRefferedBack->trsClearedYN !!}</td>
            <td>{!! $paymentBankTransferDetailRefferedBack->trsClearedDate !!}</td>
            <td>{!! $paymentBankTransferDetailRefferedBack->trsClearedByEmpSystemID !!}</td>
            <td>{!! $paymentBankTransferDetailRefferedBack->trsClearedByEmpID !!}</td>
            <td>{!! $paymentBankTransferDetailRefferedBack->trsClearedByEmpName !!}</td>
            <td>{!! $paymentBankTransferDetailRefferedBack->trsClearedAmount !!}</td>
            <td>{!! $paymentBankTransferDetailRefferedBack->bankClearedYN !!}</td>
            <td>{!! $paymentBankTransferDetailRefferedBack->bankClearedAmount !!}</td>
            <td>{!! $paymentBankTransferDetailRefferedBack->bankReconciliationDate !!}</td>
            <td>{!! $paymentBankTransferDetailRefferedBack->bankRecYear !!}</td>
            <td>{!! $paymentBankTransferDetailRefferedBack->bankRecMonth !!}</td>
            <td>{!! $paymentBankTransferDetailRefferedBack->bankClearedDate !!}</td>
            <td>{!! $paymentBankTransferDetailRefferedBack->bankClearedByEmpSystemID !!}</td>
            <td>{!! $paymentBankTransferDetailRefferedBack->bankClearedByEmpID !!}</td>
            <td>{!! $paymentBankTransferDetailRefferedBack->bankClearedByEmpName !!}</td>
            <td>{!! $paymentBankTransferDetailRefferedBack->paymentBankTransferID !!}</td>
            <td>{!! $paymentBankTransferDetailRefferedBack->pulledToBankTransferYN !!}</td>
            <td>{!! $paymentBankTransferDetailRefferedBack->chequePaymentYN !!}</td>
            <td>{!! $paymentBankTransferDetailRefferedBack->chequePrintedYN !!}</td>
            <td>{!! $paymentBankTransferDetailRefferedBack->chequePrintedDateTime !!}</td>
            <td>{!! $paymentBankTransferDetailRefferedBack->chequePrintedByEmpSystemID !!}</td>
            <td>{!! $paymentBankTransferDetailRefferedBack->chequePrintedByEmpID !!}</td>
            <td>{!! $paymentBankTransferDetailRefferedBack->chequePrintedByEmpName !!}</td>
            <td>{!! $paymentBankTransferDetailRefferedBack->chequeSentToTreasury !!}</td>
            <td>{!! $paymentBankTransferDetailRefferedBack->chequeSentToTreasuryDate !!}</td>
            <td>{!! $paymentBankTransferDetailRefferedBack->chequeSentToTreasuryByEmpSystemID !!}</td>
            <td>{!! $paymentBankTransferDetailRefferedBack->chequeSentToTreasuryByEmpID !!}</td>
            <td>{!! $paymentBankTransferDetailRefferedBack->chequeSentToTreasuryByEmpName !!}</td>
            <td>{!! $paymentBankTransferDetailRefferedBack->timesReferred !!}</td>
            <td>{!! $paymentBankTransferDetailRefferedBack->createdUserSystemID !!}</td>
            <td>{!! $paymentBankTransferDetailRefferedBack->createdUserID !!}</td>
            <td>{!! $paymentBankTransferDetailRefferedBack->createdPcID !!}</td>
            <td>{!! $paymentBankTransferDetailRefferedBack->modifiedUserSystemID !!}</td>
            <td>{!! $paymentBankTransferDetailRefferedBack->modifiedUser !!}</td>
            <td>{!! $paymentBankTransferDetailRefferedBack->modifiedPc !!}</td>
            <td>{!! $paymentBankTransferDetailRefferedBack->createdDateTime !!}</td>
            <td>{!! $paymentBankTransferDetailRefferedBack->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['paymentBankTransferDetailRefferedBacks.destroy', $paymentBankTransferDetailRefferedBack->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('paymentBankTransferDetailRefferedBacks.show', [$paymentBankTransferDetailRefferedBack->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('paymentBankTransferDetailRefferedBacks.edit', [$paymentBankTransferDetailRefferedBack->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>