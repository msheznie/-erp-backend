<table class="table table-responsive" id="customerReceivePaymentRefferedHistories-table">
    <thead>
        <tr>
            <th>Custreceivepaymentautoid</th>
        <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Documentsystemid</th>
        <th>Documentid</th>
        <th>Serialno</th>
        <th>Companyfinanceyearid</th>
        <th>Fybiggin</th>
        <th>Fyperioddatefrom</th>
        <th>Companyfinanceperiodid</th>
        <th>Fyend</th>
        <th>Fyperioddateto</th>
        <th>Paymasterautoid</th>
        <th>Intercompanypaymentid</th>
        <th>Intercompanypaymentcode</th>
        <th>Custpaymentreceivecode</th>
        <th>Custpaymentreceivedate</th>
        <th>Narration</th>
        <th>Customerid</th>
        <th>Customerglcodesystemid</th>
        <th>Customerglcode</th>
        <th>Custtransactioncurrencyid</th>
        <th>Custtransactioncurrencyer</th>
        <th>Bankid</th>
        <th>Bankaccount</th>
        <th>Bankcurrency</th>
        <th>Bankcurrencyer</th>
        <th>Payeeyn</th>
        <th>Payeeselectemp</th>
        <th>Payeeempid</th>
        <th>Payeename</th>
        <th>Payeecurrency</th>
        <th>Custchequeno</th>
        <th>Custchequedate</th>
        <th>Custchequebank</th>
        <th>Receivedamount</th>
        <th>Localcurrencyid</th>
        <th>Localcurrencyer</th>
        <th>Localamount</th>
        <th>Companyrptcurrencyid</th>
        <th>Companyrptcurrencyer</th>
        <th>Companyrptamount</th>
        <th>Bankamount</th>
        <th>Confirmedyn</th>
        <th>Confirmedbyempsystemid</th>
        <th>Confirmedbyempid</th>
        <th>Confirmedbyname</th>
        <th>Confirmeddate</th>
        <th>Approved</th>
        <th>Approveddate</th>
        <th>Approvedbyuserid</th>
        <th>Approvedbyusersystemid</th>
        <th>Posteddate</th>
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
        <th>Documenttype</th>
        <th>Matchinvoice</th>
        <th>Matchingconfirmedyn</th>
        <th>Matchingconfirmedbyempsystemid</th>
        <th>Matchingconfirmedbyempid</th>
        <th>Matchingconfirmedbyname</th>
        <th>Matchingconfirmeddate</th>
        <th>Rolllevforapp Curr</th>
        <th>Expenseclaimorpettycash</th>
        <th>Refferedbackyn</th>
        <th>Timesreferred</th>
        <th>Createdusergroup</th>
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
    @foreach($customerReceivePaymentRefferedHistories as $customerReceivePaymentRefferedHistory)
        <tr>
            <td>{!! $customerReceivePaymentRefferedHistory->custReceivePaymentAutoID !!}</td>
            <td>{!! $customerReceivePaymentRefferedHistory->companySystemID !!}</td>
            <td>{!! $customerReceivePaymentRefferedHistory->companyID !!}</td>
            <td>{!! $customerReceivePaymentRefferedHistory->documentSystemID !!}</td>
            <td>{!! $customerReceivePaymentRefferedHistory->documentID !!}</td>
            <td>{!! $customerReceivePaymentRefferedHistory->serialNo !!}</td>
            <td>{!! $customerReceivePaymentRefferedHistory->companyFinanceYearID !!}</td>
            <td>{!! $customerReceivePaymentRefferedHistory->FYBiggin !!}</td>
            <td>{!! $customerReceivePaymentRefferedHistory->FYPeriodDateFrom !!}</td>
            <td>{!! $customerReceivePaymentRefferedHistory->companyFinancePeriodID !!}</td>
            <td>{!! $customerReceivePaymentRefferedHistory->FYEnd !!}</td>
            <td>{!! $customerReceivePaymentRefferedHistory->FYPeriodDateTo !!}</td>
            <td>{!! $customerReceivePaymentRefferedHistory->PayMasterAutoId !!}</td>
            <td>{!! $customerReceivePaymentRefferedHistory->intercompanyPaymentID !!}</td>
            <td>{!! $customerReceivePaymentRefferedHistory->intercompanyPaymentCode !!}</td>
            <td>{!! $customerReceivePaymentRefferedHistory->custPaymentReceiveCode !!}</td>
            <td>{!! $customerReceivePaymentRefferedHistory->custPaymentReceiveDate !!}</td>
            <td>{!! $customerReceivePaymentRefferedHistory->narration !!}</td>
            <td>{!! $customerReceivePaymentRefferedHistory->customerID !!}</td>
            <td>{!! $customerReceivePaymentRefferedHistory->customerGLCodeSystemID !!}</td>
            <td>{!! $customerReceivePaymentRefferedHistory->customerGLCode !!}</td>
            <td>{!! $customerReceivePaymentRefferedHistory->custTransactionCurrencyID !!}</td>
            <td>{!! $customerReceivePaymentRefferedHistory->custTransactionCurrencyER !!}</td>
            <td>{!! $customerReceivePaymentRefferedHistory->bankID !!}</td>
            <td>{!! $customerReceivePaymentRefferedHistory->bankAccount !!}</td>
            <td>{!! $customerReceivePaymentRefferedHistory->bankCurrency !!}</td>
            <td>{!! $customerReceivePaymentRefferedHistory->bankCurrencyER !!}</td>
            <td>{!! $customerReceivePaymentRefferedHistory->payeeYN !!}</td>
            <td>{!! $customerReceivePaymentRefferedHistory->PayeeSelectEmp !!}</td>
            <td>{!! $customerReceivePaymentRefferedHistory->PayeeEmpID !!}</td>
            <td>{!! $customerReceivePaymentRefferedHistory->PayeeName !!}</td>
            <td>{!! $customerReceivePaymentRefferedHistory->PayeeCurrency !!}</td>
            <td>{!! $customerReceivePaymentRefferedHistory->custChequeNo !!}</td>
            <td>{!! $customerReceivePaymentRefferedHistory->custChequeDate !!}</td>
            <td>{!! $customerReceivePaymentRefferedHistory->custChequeBank !!}</td>
            <td>{!! $customerReceivePaymentRefferedHistory->receivedAmount !!}</td>
            <td>{!! $customerReceivePaymentRefferedHistory->localCurrencyID !!}</td>
            <td>{!! $customerReceivePaymentRefferedHistory->localCurrencyER !!}</td>
            <td>{!! $customerReceivePaymentRefferedHistory->localAmount !!}</td>
            <td>{!! $customerReceivePaymentRefferedHistory->companyRptCurrencyID !!}</td>
            <td>{!! $customerReceivePaymentRefferedHistory->companyRptCurrencyER !!}</td>
            <td>{!! $customerReceivePaymentRefferedHistory->companyRptAmount !!}</td>
            <td>{!! $customerReceivePaymentRefferedHistory->bankAmount !!}</td>
            <td>{!! $customerReceivePaymentRefferedHistory->confirmedYN !!}</td>
            <td>{!! $customerReceivePaymentRefferedHistory->confirmedByEmpSystemID !!}</td>
            <td>{!! $customerReceivePaymentRefferedHistory->confirmedByEmpID !!}</td>
            <td>{!! $customerReceivePaymentRefferedHistory->confirmedByName !!}</td>
            <td>{!! $customerReceivePaymentRefferedHistory->confirmedDate !!}</td>
            <td>{!! $customerReceivePaymentRefferedHistory->approved !!}</td>
            <td>{!! $customerReceivePaymentRefferedHistory->approvedDate !!}</td>
            <td>{!! $customerReceivePaymentRefferedHistory->approvedByUserID !!}</td>
            <td>{!! $customerReceivePaymentRefferedHistory->approvedByUserSystemID !!}</td>
            <td>{!! $customerReceivePaymentRefferedHistory->postedDate !!}</td>
            <td>{!! $customerReceivePaymentRefferedHistory->trsCollectedYN !!}</td>
            <td>{!! $customerReceivePaymentRefferedHistory->trsCollectedByEmpSystemID !!}</td>
            <td>{!! $customerReceivePaymentRefferedHistory->trsCollectedByEmpID !!}</td>
            <td>{!! $customerReceivePaymentRefferedHistory->trsCollectedByEmpName !!}</td>
            <td>{!! $customerReceivePaymentRefferedHistory->trsCollectedDate !!}</td>
            <td>{!! $customerReceivePaymentRefferedHistory->trsClearedYN !!}</td>
            <td>{!! $customerReceivePaymentRefferedHistory->trsClearedDate !!}</td>
            <td>{!! $customerReceivePaymentRefferedHistory->trsClearedByEmpSystemID !!}</td>
            <td>{!! $customerReceivePaymentRefferedHistory->trsClearedByEmpID !!}</td>
            <td>{!! $customerReceivePaymentRefferedHistory->trsClearedByEmpName !!}</td>
            <td>{!! $customerReceivePaymentRefferedHistory->trsClearedAmount !!}</td>
            <td>{!! $customerReceivePaymentRefferedHistory->bankClearedYN !!}</td>
            <td>{!! $customerReceivePaymentRefferedHistory->bankClearedAmount !!}</td>
            <td>{!! $customerReceivePaymentRefferedHistory->bankReconciliationDate !!}</td>
            <td>{!! $customerReceivePaymentRefferedHistory->bankClearedDate !!}</td>
            <td>{!! $customerReceivePaymentRefferedHistory->bankClearedByEmpSystemID !!}</td>
            <td>{!! $customerReceivePaymentRefferedHistory->bankClearedByEmpID !!}</td>
            <td>{!! $customerReceivePaymentRefferedHistory->bankClearedByEmpName !!}</td>
            <td>{!! $customerReceivePaymentRefferedHistory->documentType !!}</td>
            <td>{!! $customerReceivePaymentRefferedHistory->matchInvoice !!}</td>
            <td>{!! $customerReceivePaymentRefferedHistory->matchingConfirmedYN !!}</td>
            <td>{!! $customerReceivePaymentRefferedHistory->matchingConfirmedByEmpSystemID !!}</td>
            <td>{!! $customerReceivePaymentRefferedHistory->matchingConfirmedByEmpID !!}</td>
            <td>{!! $customerReceivePaymentRefferedHistory->matchingConfirmedByName !!}</td>
            <td>{!! $customerReceivePaymentRefferedHistory->matchingConfirmedDate !!}</td>
            <td>{!! $customerReceivePaymentRefferedHistory->RollLevForApp_curr !!}</td>
            <td>{!! $customerReceivePaymentRefferedHistory->expenseClaimOrPettyCash !!}</td>
            <td>{!! $customerReceivePaymentRefferedHistory->refferedBackYN !!}</td>
            <td>{!! $customerReceivePaymentRefferedHistory->timesReferred !!}</td>
            <td>{!! $customerReceivePaymentRefferedHistory->createdUserGroup !!}</td>
            <td>{!! $customerReceivePaymentRefferedHistory->createdUserSystemID !!}</td>
            <td>{!! $customerReceivePaymentRefferedHistory->createdUserID !!}</td>
            <td>{!! $customerReceivePaymentRefferedHistory->createdPcID !!}</td>
            <td>{!! $customerReceivePaymentRefferedHistory->modifiedUserSystemID !!}</td>
            <td>{!! $customerReceivePaymentRefferedHistory->modifiedUser !!}</td>
            <td>{!! $customerReceivePaymentRefferedHistory->modifiedPc !!}</td>
            <td>{!! $customerReceivePaymentRefferedHistory->createdDateTime !!}</td>
            <td>{!! $customerReceivePaymentRefferedHistory->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['customerReceivePaymentRefferedHistories.destroy', $customerReceivePaymentRefferedHistory->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('customerReceivePaymentRefferedHistories.show', [$customerReceivePaymentRefferedHistory->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('customerReceivePaymentRefferedHistories.edit', [$customerReceivePaymentRefferedHistory->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>