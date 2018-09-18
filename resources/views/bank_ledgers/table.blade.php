<table class="table table-responsive" id="bankLedgers-table">
    <thead>
        <tr>
            <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Documentsystemid</th>
        <th>Documentid</th>
        <th>Documentsystemcode</th>
        <th>Documentcode</th>
        <th>Documentdate</th>
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
        <th>Bankcleareddate</th>
        <th>Bankclearedbyempsystemid</th>
        <th>Bankclearedbyempid</th>
        <th>Bankclearedbyempname</th>
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
    @foreach($bankLedgers as $bankLedger)
        <tr>
            <td>{!! $bankLedger->companySystemID !!}</td>
            <td>{!! $bankLedger->companyID !!}</td>
            <td>{!! $bankLedger->documentSystemID !!}</td>
            <td>{!! $bankLedger->documentID !!}</td>
            <td>{!! $bankLedger->documentSystemCode !!}</td>
            <td>{!! $bankLedger->documentCode !!}</td>
            <td>{!! $bankLedger->documentDate !!}</td>
            <td>{!! $bankLedger->documentNarration !!}</td>
            <td>{!! $bankLedger->bankID !!}</td>
            <td>{!! $bankLedger->bankAccountID !!}</td>
            <td>{!! $bankLedger->bankCurrency !!}</td>
            <td>{!! $bankLedger->bankCurrencyER !!}</td>
            <td>{!! $bankLedger->documentChequeNo !!}</td>
            <td>{!! $bankLedger->documentChequeDate !!}</td>
            <td>{!! $bankLedger->payeeID !!}</td>
            <td>{!! $bankLedger->payeeCode !!}</td>
            <td>{!! $bankLedger->payeeName !!}</td>
            <td>{!! $bankLedger->payeeGLCodeID !!}</td>
            <td>{!! $bankLedger->payeeGLCode !!}</td>
            <td>{!! $bankLedger->supplierTransCurrencyID !!}</td>
            <td>{!! $bankLedger->supplierTransCurrencyER !!}</td>
            <td>{!! $bankLedger->localCurrencyID !!}</td>
            <td>{!! $bankLedger->localCurrencyER !!}</td>
            <td>{!! $bankLedger->companyRptCurrencyID !!}</td>
            <td>{!! $bankLedger->companyRptCurrencyER !!}</td>
            <td>{!! $bankLedger->payAmountBank !!}</td>
            <td>{!! $bankLedger->payAmountSuppTrans !!}</td>
            <td>{!! $bankLedger->payAmountCompLocal !!}</td>
            <td>{!! $bankLedger->payAmountCompRpt !!}</td>
            <td>{!! $bankLedger->invoiceType !!}</td>
            <td>{!! $bankLedger->trsCollectedYN !!}</td>
            <td>{!! $bankLedger->trsCollectedByEmpSystemID !!}</td>
            <td>{!! $bankLedger->trsCollectedByEmpID !!}</td>
            <td>{!! $bankLedger->trsCollectedByEmpName !!}</td>
            <td>{!! $bankLedger->trsCollectedDate !!}</td>
            <td>{!! $bankLedger->trsClearedYN !!}</td>
            <td>{!! $bankLedger->trsClearedDate !!}</td>
            <td>{!! $bankLedger->trsClearedByEmpSystemID !!}</td>
            <td>{!! $bankLedger->trsClearedByEmpID !!}</td>
            <td>{!! $bankLedger->trsClearedByEmpName !!}</td>
            <td>{!! $bankLedger->trsClearedAmount !!}</td>
            <td>{!! $bankLedger->bankClearedYN !!}</td>
            <td>{!! $bankLedger->bankClearedAmount !!}</td>
            <td>{!! $bankLedger->bankReconciliationDate !!}</td>
            <td>{!! $bankLedger->bankClearedDate !!}</td>
            <td>{!! $bankLedger->bankClearedByEmpSystemID !!}</td>
            <td>{!! $bankLedger->bankClearedByEmpID !!}</td>
            <td>{!! $bankLedger->bankClearedByEmpName !!}</td>
            <td>{!! $bankLedger->createdUserSystemID !!}</td>
            <td>{!! $bankLedger->createdUserID !!}</td>
            <td>{!! $bankLedger->createdPcID !!}</td>
            <td>{!! $bankLedger->modifiedUserSystemID !!}</td>
            <td>{!! $bankLedger->modifiedUser !!}</td>
            <td>{!! $bankLedger->modifiedPc !!}</td>
            <td>{!! $bankLedger->createdDateTime !!}</td>
            <td>{!! $bankLedger->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['bankLedgers.destroy', $bankLedger->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('bankLedgers.show', [$bankLedger->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('bankLedgers.edit', [$bankLedger->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>