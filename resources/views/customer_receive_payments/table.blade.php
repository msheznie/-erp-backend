<table class="table table-responsive" id="customerReceivePayments-table">
    <thead>
        <tr>
            <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Documentsystemid</th>
        <th>Documentid</th>
        <th>Serialno</th>
        <th>Companyfinanceyearid</th>
        <th>Fybiggin</th>
        <th>Fyperioddatefrom</th>
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
        <th>Posteddate</th>
        <th>Trscollectedyn</th>
        <th>Trscollectedbyempid</th>
        <th>Trscollectedbyempname</th>
        <th>Trscollecteddate</th>
        <th>Trsclearedyn</th>
        <th>Trscleareddate</th>
        <th>Trsclearedbyempid</th>
        <th>Trsclearedbyempname</th>
        <th>Trsclearedamount</th>
        <th>Bankclearedyn</th>
        <th>Bankclearedamount</th>
        <th>Bankreconciliationdate</th>
        <th>Bankcleareddate</th>
        <th>Bankclearedbyempid</th>
        <th>Bankclearedbyempname</th>
        <th>Documenttype</th>
        <th>Matchinvoice</th>
        <th>Matchingconfirmedyn</th>
        <th>Matchingconfirmedbyempid</th>
        <th>Matchingconfirmedbyname</th>
        <th>Matchingconfirmeddate</th>
        <th>Rolllevforapp Curr</th>
        <th>Expenseclaimorpettycash</th>
        <th>Createdusergroup</th>
        <th>Createduserid</th>
        <th>Createdpcid</th>
        <th>Modifieduser</th>
        <th>Modifiedpc</th>
        <th>Createddatetime</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($customerReceivePayments as $customerReceivePayment)
        <tr>
            <td>{!! $customerReceivePayment->companySystemID !!}</td>
            <td>{!! $customerReceivePayment->companyID !!}</td>
            <td>{!! $customerReceivePayment->documentSystemID !!}</td>
            <td>{!! $customerReceivePayment->documentID !!}</td>
            <td>{!! $customerReceivePayment->serialNo !!}</td>
            <td>{!! $customerReceivePayment->companyFinanceYearID !!}</td>
            <td>{!! $customerReceivePayment->FYBiggin !!}</td>
            <td>{!! $customerReceivePayment->FYPeriodDateFrom !!}</td>
            <td>{!! $customerReceivePayment->FYEnd !!}</td>
            <td>{!! $customerReceivePayment->FYPeriodDateTo !!}</td>
            <td>{!! $customerReceivePayment->PayMasterAutoId !!}</td>
            <td>{!! $customerReceivePayment->intercompanyPaymentID !!}</td>
            <td>{!! $customerReceivePayment->intercompanyPaymentCode !!}</td>
            <td>{!! $customerReceivePayment->custPaymentReceiveCode !!}</td>
            <td>{!! $customerReceivePayment->custPaymentReceiveDate !!}</td>
            <td>{!! $customerReceivePayment->narration !!}</td>
            <td>{!! $customerReceivePayment->customerID !!}</td>
            <td>{!! $customerReceivePayment->customerGLCodeSystemID !!}</td>
            <td>{!! $customerReceivePayment->customerGLCode !!}</td>
            <td>{!! $customerReceivePayment->custTransactionCurrencyID !!}</td>
            <td>{!! $customerReceivePayment->custTransactionCurrencyER !!}</td>
            <td>{!! $customerReceivePayment->bankID !!}</td>
            <td>{!! $customerReceivePayment->bankAccount !!}</td>
            <td>{!! $customerReceivePayment->bankCurrency !!}</td>
            <td>{!! $customerReceivePayment->bankCurrencyER !!}</td>
            <td>{!! $customerReceivePayment->payeeYN !!}</td>
            <td>{!! $customerReceivePayment->PayeeSelectEmp !!}</td>
            <td>{!! $customerReceivePayment->PayeeEmpID !!}</td>
            <td>{!! $customerReceivePayment->PayeeName !!}</td>
            <td>{!! $customerReceivePayment->PayeeCurrency !!}</td>
            <td>{!! $customerReceivePayment->custChequeNo !!}</td>
            <td>{!! $customerReceivePayment->custChequeDate !!}</td>
            <td>{!! $customerReceivePayment->custChequeBank !!}</td>
            <td>{!! $customerReceivePayment->receivedAmount !!}</td>
            <td>{!! $customerReceivePayment->localCurrencyID !!}</td>
            <td>{!! $customerReceivePayment->localCurrencyER !!}</td>
            <td>{!! $customerReceivePayment->localAmount !!}</td>
            <td>{!! $customerReceivePayment->companyRptCurrencyID !!}</td>
            <td>{!! $customerReceivePayment->companyRptCurrencyER !!}</td>
            <td>{!! $customerReceivePayment->companyRptAmount !!}</td>
            <td>{!! $customerReceivePayment->bankAmount !!}</td>
            <td>{!! $customerReceivePayment->confirmedYN !!}</td>
            <td>{!! $customerReceivePayment->confirmedByEmpSystemID !!}</td>
            <td>{!! $customerReceivePayment->confirmedByEmpID !!}</td>
            <td>{!! $customerReceivePayment->confirmedByName !!}</td>
            <td>{!! $customerReceivePayment->confirmedDate !!}</td>
            <td>{!! $customerReceivePayment->approved !!}</td>
            <td>{!! $customerReceivePayment->approvedDate !!}</td>
            <td>{!! $customerReceivePayment->postedDate !!}</td>
            <td>{!! $customerReceivePayment->trsCollectedYN !!}</td>
            <td>{!! $customerReceivePayment->trsCollectedByEmpID !!}</td>
            <td>{!! $customerReceivePayment->trsCollectedByEmpName !!}</td>
            <td>{!! $customerReceivePayment->trsCollectedDate !!}</td>
            <td>{!! $customerReceivePayment->trsClearedYN !!}</td>
            <td>{!! $customerReceivePayment->trsClearedDate !!}</td>
            <td>{!! $customerReceivePayment->trsClearedByEmpID !!}</td>
            <td>{!! $customerReceivePayment->trsClearedByEmpName !!}</td>
            <td>{!! $customerReceivePayment->trsClearedAmount !!}</td>
            <td>{!! $customerReceivePayment->bankClearedYN !!}</td>
            <td>{!! $customerReceivePayment->bankClearedAmount !!}</td>
            <td>{!! $customerReceivePayment->bankReconciliationDate !!}</td>
            <td>{!! $customerReceivePayment->bankClearedDate !!}</td>
            <td>{!! $customerReceivePayment->bankClearedByEmpID !!}</td>
            <td>{!! $customerReceivePayment->bankClearedByEmpName !!}</td>
            <td>{!! $customerReceivePayment->documentType !!}</td>
            <td>{!! $customerReceivePayment->matchInvoice !!}</td>
            <td>{!! $customerReceivePayment->matchingConfirmedYN !!}</td>
            <td>{!! $customerReceivePayment->matchingConfirmedByEmpID !!}</td>
            <td>{!! $customerReceivePayment->matchingConfirmedByName !!}</td>
            <td>{!! $customerReceivePayment->matchingConfirmedDate !!}</td>
            <td>{!! $customerReceivePayment->RollLevForApp_curr !!}</td>
            <td>{!! $customerReceivePayment->expenseClaimOrPettyCash !!}</td>
            <td>{!! $customerReceivePayment->createdUserGroup !!}</td>
            <td>{!! $customerReceivePayment->createdUserID !!}</td>
            <td>{!! $customerReceivePayment->createdPcID !!}</td>
            <td>{!! $customerReceivePayment->modifiedUser !!}</td>
            <td>{!! $customerReceivePayment->modifiedPc !!}</td>
            <td>{!! $customerReceivePayment->createdDateTime !!}</td>
            <td>{!! $customerReceivePayment->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['customerReceivePayments.destroy', $customerReceivePayment->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('customerReceivePayments.show', [$customerReceivePayment->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('customerReceivePayments.edit', [$customerReceivePayment->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>