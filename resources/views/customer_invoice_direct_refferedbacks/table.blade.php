<table class="table table-responsive" id="customerInvoiceDirectRefferedbacks-table">
    <thead>
        <tr>
            <th>Custinvoicedirectautoid</th>
        <th>Transactionmode</th>
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
        <th>Servicelinesystemid</th>
        <th>Servicelinecode</th>
        <th>Warehousesystemcode</th>
        <th>Bookinginvcode</th>
        <th>Bookingdate</th>
        <th>Comments</th>
        <th>Invoiceduedate</th>
        <th>Customergrvautoid</th>
        <th>Bankid</th>
        <th>Bankaccountid</th>
        <th>Performadate</th>
        <th>Wanno</th>
        <th>Ponumber</th>
        <th>Rigno</th>
        <th>Customerid</th>
        <th>Customerglcode</th>
        <th>Customerglsystemid</th>
        <th>Customerinvoiceno</th>
        <th>Customerinvoicedate</th>
        <th>Custtransactioncurrencyid</th>
        <th>Custtransactioncurrencyer</th>
        <th>Companyreportingcurrencyid</th>
        <th>Companyreportinger</th>
        <th>Localcurrencyid</th>
        <th>Localcurrencyer</th>
        <th>Bookingamounttrans</th>
        <th>Bookingamountlocal</th>
        <th>Bookingamountrpt</th>
        <th>Confirmedyn</th>
        <th>Confirmedbyempsystemid</th>
        <th>Confirmedbyempid</th>
        <th>Confirmedbyname</th>
        <th>Confirmeddate</th>
        <th>Approved</th>
        <th>Approveddate</th>
        <th>Posteddate</th>
        <th>Serviceperiod</th>
        <th>Paymentindaysforjob</th>
        <th>Servicestartdate</th>
        <th>Serviceenddate</th>
        <th>Isperforma</th>
        <th>Documenttype</th>
        <th>Secondarylogocompanysystemid</th>
        <th>Secondarylogocompid</th>
        <th>Secondarylogo</th>
        <th>Refferedbackyn</th>
        <th>Timesreferred</th>
        <th>Rolllevforapp Curr</th>
        <th>Selectedfortracking</th>
        <th>Customerinvoicetrackingid</th>
        <th>Intercompanytransferyn</th>
        <th>Canceledbyempsystemid</th>
        <th>Canceledyn</th>
        <th>Canceledbyempid</th>
        <th>Canceledbyempname</th>
        <th>Vatoutputglcodesystemid</th>
        <th>Vatoutputglcode</th>
        <th>Vatpercentage</th>
        <th>Vatamount</th>
        <th>Vatamountlocal</th>
        <th>Vatamountrpt</th>
        <th>Discountlocalamount</th>
        <th>Discountamount</th>
        <th>Discountrptamount</th>
        <th>Canceleddatetime</th>
        <th>Canceledcomments</th>
        <th>Createdusergroup</th>
        <th>Createdusersystemid</th>
        <th>Createduserid</th>
        <th>Createdpcid</th>
        <th>Modifiedusersystemid</th>
        <th>Modifieduser</th>
        <th>Modifiedpc</th>
        <th>Createddatetime</th>
        <th>Createddateandtime</th>
        <th>Timestamp</th>
        <th>Approvedbyuserid</th>
        <th>Approvedbyusersystemid</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($customerInvoiceDirectRefferedbacks as $customerInvoiceDirectRefferedback)
        <tr>
            <td>{!! $customerInvoiceDirectRefferedback->custInvoiceDirectAutoID !!}</td>
            <td>{!! $customerInvoiceDirectRefferedback->transactionMode !!}</td>
            <td>{!! $customerInvoiceDirectRefferedback->companySystemID !!}</td>
            <td>{!! $customerInvoiceDirectRefferedback->companyID !!}</td>
            <td>{!! $customerInvoiceDirectRefferedback->documentSystemiD !!}</td>
            <td>{!! $customerInvoiceDirectRefferedback->documentID !!}</td>
            <td>{!! $customerInvoiceDirectRefferedback->serialNo !!}</td>
            <td>{!! $customerInvoiceDirectRefferedback->companyFinanceYearID !!}</td>
            <td>{!! $customerInvoiceDirectRefferedback->FYBiggin !!}</td>
            <td>{!! $customerInvoiceDirectRefferedback->FYEnd !!}</td>
            <td>{!! $customerInvoiceDirectRefferedback->companyFinancePeriodID !!}</td>
            <td>{!! $customerInvoiceDirectRefferedback->FYPeriodDateFrom !!}</td>
            <td>{!! $customerInvoiceDirectRefferedback->FYPeriodDateTo !!}</td>
            <td>{!! $customerInvoiceDirectRefferedback->serviceLineSystemID !!}</td>
            <td>{!! $customerInvoiceDirectRefferedback->serviceLineCode !!}</td>
            <td>{!! $customerInvoiceDirectRefferedback->wareHouseSystemCode !!}</td>
            <td>{!! $customerInvoiceDirectRefferedback->bookingInvCode !!}</td>
            <td>{!! $customerInvoiceDirectRefferedback->bookingDate !!}</td>
            <td>{!! $customerInvoiceDirectRefferedback->comments !!}</td>
            <td>{!! $customerInvoiceDirectRefferedback->invoiceDueDate !!}</td>
            <td>{!! $customerInvoiceDirectRefferedback->customerGRVAutoID !!}</td>
            <td>{!! $customerInvoiceDirectRefferedback->bankID !!}</td>
            <td>{!! $customerInvoiceDirectRefferedback->bankAccountID !!}</td>
            <td>{!! $customerInvoiceDirectRefferedback->performaDate !!}</td>
            <td>{!! $customerInvoiceDirectRefferedback->wanNO !!}</td>
            <td>{!! $customerInvoiceDirectRefferedback->PONumber !!}</td>
            <td>{!! $customerInvoiceDirectRefferedback->rigNo !!}</td>
            <td>{!! $customerInvoiceDirectRefferedback->customerID !!}</td>
            <td>{!! $customerInvoiceDirectRefferedback->customerGLCode !!}</td>
            <td>{!! $customerInvoiceDirectRefferedback->customerGLSystemID !!}</td>
            <td>{!! $customerInvoiceDirectRefferedback->customerInvoiceNo !!}</td>
            <td>{!! $customerInvoiceDirectRefferedback->customerInvoiceDate !!}</td>
            <td>{!! $customerInvoiceDirectRefferedback->custTransactionCurrencyID !!}</td>
            <td>{!! $customerInvoiceDirectRefferedback->custTransactionCurrencyER !!}</td>
            <td>{!! $customerInvoiceDirectRefferedback->companyReportingCurrencyID !!}</td>
            <td>{!! $customerInvoiceDirectRefferedback->companyReportingER !!}</td>
            <td>{!! $customerInvoiceDirectRefferedback->localCurrencyID !!}</td>
            <td>{!! $customerInvoiceDirectRefferedback->localCurrencyER !!}</td>
            <td>{!! $customerInvoiceDirectRefferedback->bookingAmountTrans !!}</td>
            <td>{!! $customerInvoiceDirectRefferedback->bookingAmountLocal !!}</td>
            <td>{!! $customerInvoiceDirectRefferedback->bookingAmountRpt !!}</td>
            <td>{!! $customerInvoiceDirectRefferedback->confirmedYN !!}</td>
            <td>{!! $customerInvoiceDirectRefferedback->confirmedByEmpSystemID !!}</td>
            <td>{!! $customerInvoiceDirectRefferedback->confirmedByEmpID !!}</td>
            <td>{!! $customerInvoiceDirectRefferedback->confirmedByName !!}</td>
            <td>{!! $customerInvoiceDirectRefferedback->confirmedDate !!}</td>
            <td>{!! $customerInvoiceDirectRefferedback->approved !!}</td>
            <td>{!! $customerInvoiceDirectRefferedback->approvedDate !!}</td>
            <td>{!! $customerInvoiceDirectRefferedback->postedDate !!}</td>
            <td>{!! $customerInvoiceDirectRefferedback->servicePeriod !!}</td>
            <td>{!! $customerInvoiceDirectRefferedback->paymentInDaysForJob !!}</td>
            <td>{!! $customerInvoiceDirectRefferedback->serviceStartDate !!}</td>
            <td>{!! $customerInvoiceDirectRefferedback->serviceEndDate !!}</td>
            <td>{!! $customerInvoiceDirectRefferedback->isPerforma !!}</td>
            <td>{!! $customerInvoiceDirectRefferedback->documentType !!}</td>
            <td>{!! $customerInvoiceDirectRefferedback->secondaryLogoCompanySystemID !!}</td>
            <td>{!! $customerInvoiceDirectRefferedback->secondaryLogoCompID !!}</td>
            <td>{!! $customerInvoiceDirectRefferedback->secondaryLogo !!}</td>
            <td>{!! $customerInvoiceDirectRefferedback->refferedBackYN !!}</td>
            <td>{!! $customerInvoiceDirectRefferedback->timesReferred !!}</td>
            <td>{!! $customerInvoiceDirectRefferedback->RollLevForApp_curr !!}</td>
            <td>{!! $customerInvoiceDirectRefferedback->selectedForTracking !!}</td>
            <td>{!! $customerInvoiceDirectRefferedback->customerInvoiceTrackingID !!}</td>
            <td>{!! $customerInvoiceDirectRefferedback->interCompanyTransferYN !!}</td>
            <td>{!! $customerInvoiceDirectRefferedback->canceledByEmpSystemID !!}</td>
            <td>{!! $customerInvoiceDirectRefferedback->canceledYN !!}</td>
            <td>{!! $customerInvoiceDirectRefferedback->canceledByEmpID !!}</td>
            <td>{!! $customerInvoiceDirectRefferedback->canceledByEmpName !!}</td>
            <td>{!! $customerInvoiceDirectRefferedback->vatOutputGLCodeSystemID !!}</td>
            <td>{!! $customerInvoiceDirectRefferedback->vatOutputGLCode !!}</td>
            <td>{!! $customerInvoiceDirectRefferedback->VATPercentage !!}</td>
            <td>{!! $customerInvoiceDirectRefferedback->VATAmount !!}</td>
            <td>{!! $customerInvoiceDirectRefferedback->VATAmountLocal !!}</td>
            <td>{!! $customerInvoiceDirectRefferedback->VATAmountRpt !!}</td>
            <td>{!! $customerInvoiceDirectRefferedback->discountLocalAmount !!}</td>
            <td>{!! $customerInvoiceDirectRefferedback->discountAmount !!}</td>
            <td>{!! $customerInvoiceDirectRefferedback->discountRptAmount !!}</td>
            <td>{!! $customerInvoiceDirectRefferedback->canceledDateTime !!}</td>
            <td>{!! $customerInvoiceDirectRefferedback->canceledComments !!}</td>
            <td>{!! $customerInvoiceDirectRefferedback->createdUserGroup !!}</td>
            <td>{!! $customerInvoiceDirectRefferedback->createdUserSystemID !!}</td>
            <td>{!! $customerInvoiceDirectRefferedback->createdUserID !!}</td>
            <td>{!! $customerInvoiceDirectRefferedback->createdPcID !!}</td>
            <td>{!! $customerInvoiceDirectRefferedback->modifiedUserSystemID !!}</td>
            <td>{!! $customerInvoiceDirectRefferedback->modifiedUser !!}</td>
            <td>{!! $customerInvoiceDirectRefferedback->modifiedPc !!}</td>
            <td>{!! $customerInvoiceDirectRefferedback->createdDateTime !!}</td>
            <td>{!! $customerInvoiceDirectRefferedback->createdDateAndTime !!}</td>
            <td>{!! $customerInvoiceDirectRefferedback->timestamp !!}</td>
            <td>{!! $customerInvoiceDirectRefferedback->approvedByUserID !!}</td>
            <td>{!! $customerInvoiceDirectRefferedback->approvedByUserSystemID !!}</td>
            <td>
                {!! Form::open(['route' => ['customerInvoiceDirectRefferedbacks.destroy', $customerInvoiceDirectRefferedback->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('customerInvoiceDirectRefferedbacks.show', [$customerInvoiceDirectRefferedback->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('customerInvoiceDirectRefferedbacks.edit', [$customerInvoiceDirectRefferedback->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>