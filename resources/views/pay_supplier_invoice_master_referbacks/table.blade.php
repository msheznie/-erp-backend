<table class="table table-responsive" id="paySupplierInvoiceMasterReferbacks-table">
    <thead>
        <tr>
            <th>Paymasterautoid</th>
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
        <th>Supplierglcodesystemid</th>
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
        <th>Confirmedbyempsystemid</th>
        <th>Confirmedbyempid</th>
        <th>Confirmedbyname</th>
        <th>Confirmeddate</th>
        <th>Approved</th>
        <th>Approveddate</th>
        <th>Approvedbyuserid</th>
        <th>Approvedbyusersystemid</th>
        <th>Posteddate</th>
        <th>Invoicetype</th>
        <th>Matchinvoice</th>
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
        <th>Chequepaymentyn</th>
        <th>Chequeprintedyn</th>
        <th>Chequeprinteddatetime</th>
        <th>Chequeprintedbyempsystemid</th>
        <th>Chequeprintedbyempid</th>
        <th>Chequeprintedbyempname</th>
        <th>Chequesenttotreasury</th>
        <th>Chequesenttotreasurybyempsystemid</th>
        <th>Chequesenttotreasurybyempid</th>
        <th>Chequesenttotreasurybyempname</th>
        <th>Chequesenttotreasurydate</th>
        <th>Chequereceivedbytreasury</th>
        <th>Chequereceivedbytreasurybyempsystemid</th>
        <th>Chequereceivedbytreasurybyempid</th>
        <th>Chequereceivedbytreasurybyempname</th>
        <th>Chequereceivedbytreasurydate</th>
        <th>Timesreferred</th>
        <th>Matchingconfirmedyn</th>
        <th>Matchingconfirmedbyempsystemid</th>
        <th>Matchingconfirmedbyempid</th>
        <th>Matchingconfirmedbyname</th>
        <th>Matchingconfirmeddate</th>
        <th>Refferedbackyn</th>
        <th>Rolllevforapp Curr</th>
        <th>Noofapprovallevels</th>
        <th>Isrelatedpartyyn</th>
        <th>Advancepaymenttypeid</th>
        <th>Ispdcchequeyn</th>
        <th>Finalsettlementyn</th>
        <th>Expenseclaimorpettycash</th>
        <th>Intercompanytosystemid</th>
        <th>Intercompanytoid</th>
        <th>Reversedyn</th>
        <th>Cancelyn</th>
        <th>Cancelcomment</th>
        <th>Canceldate</th>
        <th>Cancelledbyempsystemid</th>
        <th>Canceledbyempid</th>
        <th>Canceledbyempname</th>
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
    @foreach($paySupplierInvoiceMasterReferbacks as $paySupplierInvoiceMasterReferback)
        <tr>
            <td>{!! $paySupplierInvoiceMasterReferback->PayMasterAutoId !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->companySystemID !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->companyID !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->documentSystemID !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->documentID !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->serialNo !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->companyFinanceYearID !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->FYBiggin !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->FYEnd !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->companyFinancePeriodID !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->FYPeriodDateFrom !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->FYPeriodDateTo !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->BPVcode !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->BPVdate !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->BPVbank !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->BPVAccount !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->BPVchequeNo !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->BPVchequeDate !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->BPVNarration !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->BPVbankCurrency !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->BPVbankCurrencyER !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->directPaymentpayeeYN !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->directPaymentPayeeSelectEmp !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->directPaymentPayeeEmpID !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->directPaymentPayee !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->directPayeeCurrency !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->directPayeeBankMemo !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->BPVsupplierID !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->supplierGLCodeSystemID !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->supplierGLCode !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->supplierTransCurrencyID !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->supplierTransCurrencyER !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->supplierDefCurrencyID !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->supplierDefCurrencyER !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->localCurrencyID !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->localCurrencyER !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->companyRptCurrencyID !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->companyRptCurrencyER !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->payAmountBank !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->payAmountSuppTrans !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->payAmountSuppDef !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->payAmountCompLocal !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->payAmountCompRpt !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->suppAmountDocTotal !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->confirmedYN !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->confirmedByEmpSystemID !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->confirmedByEmpID !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->confirmedByName !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->confirmedDate !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->approved !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->approvedDate !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->approvedByUserID !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->approvedByUserSystemID !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->postedDate !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->invoiceType !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->matchInvoice !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->trsCollectedYN !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->trsCollectedByEmpSystemID !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->trsCollectedByEmpID !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->trsCollectedByEmpName !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->trsCollectedDate !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->trsClearedYN !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->trsClearedDate !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->trsClearedByEmpSystemID !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->trsClearedByEmpID !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->trsClearedByEmpName !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->trsClearedAmount !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->bankClearedYN !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->bankClearedAmount !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->bankReconciliationDate !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->bankClearedDate !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->bankClearedByEmpSystemID !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->bankClearedByEmpID !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->bankClearedByEmpName !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->chequePaymentYN !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->chequePrintedYN !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->chequePrintedDateTime !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->chequePrintedByEmpSystemID !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->chequePrintedByEmpID !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->chequePrintedByEmpName !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->chequeSentToTreasury !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->chequeSentToTreasuryByEmpSystemID !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->chequeSentToTreasuryByEmpID !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->chequeSentToTreasuryByEmpName !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->chequeSentToTreasuryDate !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->chequeReceivedByTreasury !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->chequeReceivedByTreasuryByEmpSystemID !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->chequeReceivedByTreasuryByEmpID !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->chequeReceivedByTreasuryByEmpName !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->chequeReceivedByTreasuryDate !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->timesReferred !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->matchingConfirmedYN !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->matchingConfirmedByEmpSystemID !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->matchingConfirmedByEmpID !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->matchingConfirmedByName !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->matchingConfirmedDate !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->refferedBackYN !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->RollLevForApp_curr !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->noOfApprovalLevels !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->isRelatedPartyYN !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->advancePaymentTypeID !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->isPdcChequeYN !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->finalSettlementYN !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->expenseClaimOrPettyCash !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->interCompanyToSystemID !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->interCompanyToID !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->ReversedYN !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->cancelYN !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->cancelComment !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->cancelDate !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->cancelledByEmpSystemID !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->canceledByEmpID !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->canceledByEmpName !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->createdUserGroup !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->createdUserSystemID !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->createdUserID !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->createdPcID !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->modifiedUserSystemID !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->modifiedUser !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->modifiedPc !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->createdDateTime !!}</td>
            <td>{!! $paySupplierInvoiceMasterReferback->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['paySupplierInvoiceMasterReferbacks.destroy', $paySupplierInvoiceMasterReferback->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('paySupplierInvoiceMasterReferbacks.show', [$paySupplierInvoiceMasterReferback->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('paySupplierInvoiceMasterReferbacks.edit', [$paySupplierInvoiceMasterReferback->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>