<table class="table table-responsive" id="paySupplierInvoiceMasters-table">
    <thead>
        <tr>
            <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Documentsystemid</th>
        <th>Documentid</th>
        <th>Serialno</th>
        <th>Companyfinanceyearid</th>
        <th>Fybiggin</th>
        <th>Fyend</th>
        <th>Companyfinanceperiodid</th>
        <th>Fyperioddatefrom</th>
        <th>Fyperioddateto</th>
        <th>Bpvcode</th>
        <th>Bpvdate</th>
        <th>Bpvbank</th>
        <th>Bpvaccount</th>
        <th>Bpvchequeno</th>
        <th>Bpvchequedate</th>
        <th>Bpvnarration</th>
        <th>Bpvbankcurrency</th>
        <th>Bpvbankcurrencyer</th>
        <th>Directpaymentpayeeyn</th>
        <th>Directpaymentpayeeselectemp</th>
        <th>Directpaymentpayeeempid</th>
        <th>Directpaymentpayee</th>
        <th>Directpayeecurrency</th>
        <th>Directpayeebankmemo</th>
        <th>Bpvsupplierid</th>
        <th>Supplierglcode</th>
        <th>Suppliertranscurrencyid</th>
        <th>Suppliertranscurrencyer</th>
        <th>Supplierdefcurrencyid</th>
        <th>Supplierdefcurrencyer</th>
        <th>Localcurrencyid</th>
        <th>Localcurrencyer</th>
        <th>Companyrptcurrencyid</th>
        <th>Companyrptcurrencyer</th>
        <th>Payamountbank</th>
        <th>Payamountsupptrans</th>
        <th>Payamountsuppdef</th>
        <th>Payamountcomplocal</th>
        <th>Payamountcomprpt</th>
        <th>Suppamountdoctotal</th>
        <th>Confirmedyn</th>
        <th>Confirmedbyempid</th>
        <th>Confirmedbyname</th>
        <th>Confirmeddate</th>
        <th>Approved</th>
        <th>Approveddate</th>
        <th>Posteddate</th>
        <th>Invoicetype</th>
        <th>Matchinvoice</th>
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
        <th>Chequepaymentyn</th>
        <th>Chequeprintedyn</th>
        <th>Chequeprinteddatetime</th>
        <th>Chequeprintedbyempid</th>
        <th>Chequeprintedbyempname</th>
        <th>Chequesenttotreasury</th>
        <th>Chequesenttotreasurybyempid</th>
        <th>Chequesenttotreasurybyempname</th>
        <th>Chequesenttotreasurydate</th>
        <th>Chequereceivedbytreasury</th>
        <th>Chequereceivedbytreasurybyempid</th>
        <th>Chequereceivedbytreasurybyempname</th>
        <th>Chequereceivedbytreasurydate</th>
        <th>Timesreferred</th>
        <th>Matchingconfirmedyn</th>
        <th>Matchingconfirmedbyempid</th>
        <th>Matchingconfirmedbyname</th>
        <th>Matchingconfirmeddate</th>
        <th>Rolllevforapp Curr</th>
        <th>Noofapprovallevels</th>
        <th>Isrelatedpartyyn</th>
        <th>Advancepaymenttypeid</th>
        <th>Ispdcchequeyn</th>
        <th>Finalsettlementyn</th>
        <th>Expenseclaimorpettycash</th>
        <th>Intercompanytoid</th>
        <th>Reversedyn</th>
        <th>Cancelyn</th>
        <th>Cancelcomment</th>
        <th>Canceldate</th>
        <th>Canceledbyempid</th>
        <th>Canceledbyempname</th>
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
    @foreach($paySupplierInvoiceMasters as $paySupplierInvoiceMaster)
        <tr>
            <td>{!! $paySupplierInvoiceMaster->companySystemID !!}</td>
            <td>{!! $paySupplierInvoiceMaster->companyID !!}</td>
            <td>{!! $paySupplierInvoiceMaster->documentSystemID !!}</td>
            <td>{!! $paySupplierInvoiceMaster->documentID !!}</td>
            <td>{!! $paySupplierInvoiceMaster->serialNo !!}</td>
            <td>{!! $paySupplierInvoiceMaster->companyFinanceYearID !!}</td>
            <td>{!! $paySupplierInvoiceMaster->FYBiggin !!}</td>
            <td>{!! $paySupplierInvoiceMaster->FYEnd !!}</td>
            <td>{!! $paySupplierInvoiceMaster->companyFinancePeriodID !!}</td>
            <td>{!! $paySupplierInvoiceMaster->FYPeriodDateFrom !!}</td>
            <td>{!! $paySupplierInvoiceMaster->FYPeriodDateTo !!}</td>
            <td>{!! $paySupplierInvoiceMaster->BPVcode !!}</td>
            <td>{!! $paySupplierInvoiceMaster->BPVdate !!}</td>
            <td>{!! $paySupplierInvoiceMaster->BPVbank !!}</td>
            <td>{!! $paySupplierInvoiceMaster->BPVAccount !!}</td>
            <td>{!! $paySupplierInvoiceMaster->BPVchequeNo !!}</td>
            <td>{!! $paySupplierInvoiceMaster->BPVchequeDate !!}</td>
            <td>{!! $paySupplierInvoiceMaster->BPVNarration !!}</td>
            <td>{!! $paySupplierInvoiceMaster->BPVbankCurrency !!}</td>
            <td>{!! $paySupplierInvoiceMaster->BPVbankCurrencyER !!}</td>
            <td>{!! $paySupplierInvoiceMaster->directPaymentpayeeYN !!}</td>
            <td>{!! $paySupplierInvoiceMaster->directPaymentPayeeSelectEmp !!}</td>
            <td>{!! $paySupplierInvoiceMaster->directPaymentPayeeEmpID !!}</td>
            <td>{!! $paySupplierInvoiceMaster->directPaymentPayee !!}</td>
            <td>{!! $paySupplierInvoiceMaster->directPayeeCurrency !!}</td>
            <td>{!! $paySupplierInvoiceMaster->directPayeeBankMemo !!}</td>
            <td>{!! $paySupplierInvoiceMaster->BPVsupplierID !!}</td>
            <td>{!! $paySupplierInvoiceMaster->supplierGLCode !!}</td>
            <td>{!! $paySupplierInvoiceMaster->supplierTransCurrencyID !!}</td>
            <td>{!! $paySupplierInvoiceMaster->supplierTransCurrencyER !!}</td>
            <td>{!! $paySupplierInvoiceMaster->supplierDefCurrencyID !!}</td>
            <td>{!! $paySupplierInvoiceMaster->supplierDefCurrencyER !!}</td>
            <td>{!! $paySupplierInvoiceMaster->localCurrencyID !!}</td>
            <td>{!! $paySupplierInvoiceMaster->localCurrencyER !!}</td>
            <td>{!! $paySupplierInvoiceMaster->companyRptCurrencyID !!}</td>
            <td>{!! $paySupplierInvoiceMaster->companyRptCurrencyER !!}</td>
            <td>{!! $paySupplierInvoiceMaster->payAmountBank !!}</td>
            <td>{!! $paySupplierInvoiceMaster->payAmountSuppTrans !!}</td>
            <td>{!! $paySupplierInvoiceMaster->payAmountSuppDef !!}</td>
            <td>{!! $paySupplierInvoiceMaster->payAmountCompLocal !!}</td>
            <td>{!! $paySupplierInvoiceMaster->payAmountCompRpt !!}</td>
            <td>{!! $paySupplierInvoiceMaster->suppAmountDocTotal !!}</td>
            <td>{!! $paySupplierInvoiceMaster->confirmedYN !!}</td>
            <td>{!! $paySupplierInvoiceMaster->confirmedByEmpID !!}</td>
            <td>{!! $paySupplierInvoiceMaster->confirmedByName !!}</td>
            <td>{!! $paySupplierInvoiceMaster->confirmedDate !!}</td>
            <td>{!! $paySupplierInvoiceMaster->approved !!}</td>
            <td>{!! $paySupplierInvoiceMaster->approvedDate !!}</td>
            <td>{!! $paySupplierInvoiceMaster->postedDate !!}</td>
            <td>{!! $paySupplierInvoiceMaster->invoiceType !!}</td>
            <td>{!! $paySupplierInvoiceMaster->matchInvoice !!}</td>
            <td>{!! $paySupplierInvoiceMaster->trsCollectedYN !!}</td>
            <td>{!! $paySupplierInvoiceMaster->trsCollectedByEmpID !!}</td>
            <td>{!! $paySupplierInvoiceMaster->trsCollectedByEmpName !!}</td>
            <td>{!! $paySupplierInvoiceMaster->trsCollectedDate !!}</td>
            <td>{!! $paySupplierInvoiceMaster->trsClearedYN !!}</td>
            <td>{!! $paySupplierInvoiceMaster->trsClearedDate !!}</td>
            <td>{!! $paySupplierInvoiceMaster->trsClearedByEmpID !!}</td>
            <td>{!! $paySupplierInvoiceMaster->trsClearedByEmpName !!}</td>
            <td>{!! $paySupplierInvoiceMaster->trsClearedAmount !!}</td>
            <td>{!! $paySupplierInvoiceMaster->bankClearedYN !!}</td>
            <td>{!! $paySupplierInvoiceMaster->bankClearedAmount !!}</td>
            <td>{!! $paySupplierInvoiceMaster->bankReconciliationDate !!}</td>
            <td>{!! $paySupplierInvoiceMaster->bankClearedDate !!}</td>
            <td>{!! $paySupplierInvoiceMaster->bankClearedByEmpID !!}</td>
            <td>{!! $paySupplierInvoiceMaster->bankClearedByEmpName !!}</td>
            <td>{!! $paySupplierInvoiceMaster->chequePaymentYN !!}</td>
            <td>{!! $paySupplierInvoiceMaster->chequePrintedYN !!}</td>
            <td>{!! $paySupplierInvoiceMaster->chequePrintedDateTime !!}</td>
            <td>{!! $paySupplierInvoiceMaster->chequePrintedByEmpID !!}</td>
            <td>{!! $paySupplierInvoiceMaster->chequePrintedByEmpName !!}</td>
            <td>{!! $paySupplierInvoiceMaster->chequeSentToTreasury !!}</td>
            <td>{!! $paySupplierInvoiceMaster->chequeSentToTreasuryByEmpID !!}</td>
            <td>{!! $paySupplierInvoiceMaster->chequeSentToTreasuryByEmpName !!}</td>
            <td>{!! $paySupplierInvoiceMaster->chequeSentToTreasuryDate !!}</td>
            <td>{!! $paySupplierInvoiceMaster->chequeReceivedByTreasury !!}</td>
            <td>{!! $paySupplierInvoiceMaster->chequeReceivedByTreasuryByEmpID !!}</td>
            <td>{!! $paySupplierInvoiceMaster->chequeReceivedByTreasuryByEmpName !!}</td>
            <td>{!! $paySupplierInvoiceMaster->chequeReceivedByTreasuryDate !!}</td>
            <td>{!! $paySupplierInvoiceMaster->timesReferred !!}</td>
            <td>{!! $paySupplierInvoiceMaster->matchingConfirmedYN !!}</td>
            <td>{!! $paySupplierInvoiceMaster->matchingConfirmedByEmpID !!}</td>
            <td>{!! $paySupplierInvoiceMaster->matchingConfirmedByName !!}</td>
            <td>{!! $paySupplierInvoiceMaster->matchingConfirmedDate !!}</td>
            <td>{!! $paySupplierInvoiceMaster->RollLevForApp_curr !!}</td>
            <td>{!! $paySupplierInvoiceMaster->noOfApprovalLevels !!}</td>
            <td>{!! $paySupplierInvoiceMaster->isRelatedPartyYN !!}</td>
            <td>{!! $paySupplierInvoiceMaster->advancePaymentTypeID !!}</td>
            <td>{!! $paySupplierInvoiceMaster->isPdcChequeYN !!}</td>
            <td>{!! $paySupplierInvoiceMaster->finalSettlementYN !!}</td>
            <td>{!! $paySupplierInvoiceMaster->expenseClaimOrPettyCash !!}</td>
            <td>{!! $paySupplierInvoiceMaster->interCompanyToID !!}</td>
            <td>{!! $paySupplierInvoiceMaster->ReversedYN !!}</td>
            <td>{!! $paySupplierInvoiceMaster->cancelYN !!}</td>
            <td>{!! $paySupplierInvoiceMaster->cancelComment !!}</td>
            <td>{!! $paySupplierInvoiceMaster->cancelDate !!}</td>
            <td>{!! $paySupplierInvoiceMaster->canceledByEmpID !!}</td>
            <td>{!! $paySupplierInvoiceMaster->canceledByEmpName !!}</td>
            <td>{!! $paySupplierInvoiceMaster->createdUserGroup !!}</td>
            <td>{!! $paySupplierInvoiceMaster->createdUserID !!}</td>
            <td>{!! $paySupplierInvoiceMaster->createdPcID !!}</td>
            <td>{!! $paySupplierInvoiceMaster->modifiedUser !!}</td>
            <td>{!! $paySupplierInvoiceMaster->modifiedPc !!}</td>
            <td>{!! $paySupplierInvoiceMaster->createdDateTime !!}</td>
            <td>{!! $paySupplierInvoiceMaster->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['paySupplierInvoiceMasters.destroy', $paySupplierInvoiceMaster->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('paySupplierInvoiceMasters.show', [$paySupplierInvoiceMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('paySupplierInvoiceMasters.edit', [$paySupplierInvoiceMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>