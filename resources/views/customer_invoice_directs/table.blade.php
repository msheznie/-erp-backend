<table class="table table-responsive" id="customerInvoiceDirects-table">
    <thead>
        <tr>
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
        <th>Secondarylogocompid</th>
        <th>Secondarylogo</th>
        <th>Timesreferred</th>
        <th>Rolllevforapp Curr</th>
        <th>Selectedfortracking</th>
        <th>Customerinvoicetrackingid</th>
        <th>Intercompanytransferyn</th>
        <th>Canceledyn</th>
        <th>Canceledbyempsystemid</th>
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
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($customerInvoiceDirects as $customerInvoiceDirect)
        <tr>
            <td>{!! $customerInvoiceDirect->transactionMode !!}</td>
            <td>{!! $customerInvoiceDirect->companySystemID !!}</td>
            <td>{!! $customerInvoiceDirect->companyID !!}</td>
            <td>{!! $customerInvoiceDirect->documentSystemiD !!}</td>
            <td>{!! $customerInvoiceDirect->documentID !!}</td>
            <td>{!! $customerInvoiceDirect->serialNo !!}</td>
            <td>{!! $customerInvoiceDirect->companyFinanceYearID !!}</td>
            <td>{!! $customerInvoiceDirect->FYBiggin !!}</td>
            <td>{!! $customerInvoiceDirect->FYEnd !!}</td>
            <td>{!! $customerInvoiceDirect->companyFinancePeriodID !!}</td>
            <td>{!! $customerInvoiceDirect->FYPeriodDateFrom !!}</td>
            <td>{!! $customerInvoiceDirect->FYPeriodDateTo !!}</td>
            <td>{!! $customerInvoiceDirect->serviceLineSystemID !!}</td>
            <td>{!! $customerInvoiceDirect->serviceLineCode !!}</td>
            <td>{!! $customerInvoiceDirect->wareHouseSystemCode !!}</td>
            <td>{!! $customerInvoiceDirect->bookingInvCode !!}</td>
            <td>{!! $customerInvoiceDirect->bookingDate !!}</td>
            <td>{!! $customerInvoiceDirect->comments !!}</td>
            <td>{!! $customerInvoiceDirect->invoiceDueDate !!}</td>
            <td>{!! $customerInvoiceDirect->customerGRVAutoID !!}</td>
            <td>{!! $customerInvoiceDirect->bankID !!}</td>
            <td>{!! $customerInvoiceDirect->bankAccountID !!}</td>
            <td>{!! $customerInvoiceDirect->performaDate !!}</td>
            <td>{!! $customerInvoiceDirect->wanNO !!}</td>
            <td>{!! $customerInvoiceDirect->PONumber !!}</td>
            <td>{!! $customerInvoiceDirect->rigNo !!}</td>
            <td>{!! $customerInvoiceDirect->customerID !!}</td>
            <td>{!! $customerInvoiceDirect->customerGLCode !!}</td>
            <td>{!! $customerInvoiceDirect->customerInvoiceNo !!}</td>
            <td>{!! $customerInvoiceDirect->customerInvoiceDate !!}</td>
            <td>{!! $customerInvoiceDirect->custTransactionCurrencyID !!}</td>
            <td>{!! $customerInvoiceDirect->custTransactionCurrencyER !!}</td>
            <td>{!! $customerInvoiceDirect->companyReportingCurrencyID !!}</td>
            <td>{!! $customerInvoiceDirect->companyReportingER !!}</td>
            <td>{!! $customerInvoiceDirect->localCurrencyID !!}</td>
            <td>{!! $customerInvoiceDirect->localCurrencyER !!}</td>
            <td>{!! $customerInvoiceDirect->bookingAmountTrans !!}</td>
            <td>{!! $customerInvoiceDirect->bookingAmountLocal !!}</td>
            <td>{!! $customerInvoiceDirect->bookingAmountRpt !!}</td>
            <td>{!! $customerInvoiceDirect->confirmedYN !!}</td>
            <td>{!! $customerInvoiceDirect->confirmedByEmpSystemID !!}</td>
            <td>{!! $customerInvoiceDirect->confirmedByEmpID !!}</td>
            <td>{!! $customerInvoiceDirect->confirmedByName !!}</td>
            <td>{!! $customerInvoiceDirect->confirmedDate !!}</td>
            <td>{!! $customerInvoiceDirect->approved !!}</td>
            <td>{!! $customerInvoiceDirect->approvedDate !!}</td>
            <td>{!! $customerInvoiceDirect->postedDate !!}</td>
            <td>{!! $customerInvoiceDirect->servicePeriod !!}</td>
            <td>{!! $customerInvoiceDirect->paymentInDaysForJob !!}</td>
            <td>{!! $customerInvoiceDirect->serviceStartDate !!}</td>
            <td>{!! $customerInvoiceDirect->serviceEndDate !!}</td>
            <td>{!! $customerInvoiceDirect->isPerforma !!}</td>
            <td>{!! $customerInvoiceDirect->documentType !!}</td>
            <td>{!! $customerInvoiceDirect->secondaryLogoCompID !!}</td>
            <td>{!! $customerInvoiceDirect->secondaryLogo !!}</td>
            <td>{!! $customerInvoiceDirect->timesReferred !!}</td>
            <td>{!! $customerInvoiceDirect->RollLevForApp_curr !!}</td>
            <td>{!! $customerInvoiceDirect->selectedForTracking !!}</td>
            <td>{!! $customerInvoiceDirect->customerInvoiceTrackingID !!}</td>
            <td>{!! $customerInvoiceDirect->interCompanyTransferYN !!}</td>
            <td>{!! $customerInvoiceDirect->canceledYN !!}</td>
            <td>{!! $customerInvoiceDirect->canceledByEmpSystemID !!}</td>
            <td>{!! $customerInvoiceDirect->canceledByEmpID !!}</td>
            <td>{!! $customerInvoiceDirect->canceledByEmpName !!}</td>
            <td>{!! $customerInvoiceDirect->vatOutputGLCodeSystemID !!}</td>
            <td>{!! $customerInvoiceDirect->vatOutputGLCode !!}</td>
            <td>{!! $customerInvoiceDirect->VATPercentage !!}</td>
            <td>{!! $customerInvoiceDirect->VATAmount !!}</td>
            <td>{!! $customerInvoiceDirect->VATAmountLocal !!}</td>
            <td>{!! $customerInvoiceDirect->VATAmountRpt !!}</td>
            <td>{!! $customerInvoiceDirect->discountLocalAmount !!}</td>
            <td>{!! $customerInvoiceDirect->discountAmount !!}</td>
            <td>{!! $customerInvoiceDirect->discountRptAmount !!}</td>
            <td>{!! $customerInvoiceDirect->canceledDateTime !!}</td>
            <td>{!! $customerInvoiceDirect->canceledComments !!}</td>
            <td>{!! $customerInvoiceDirect->createdUserGroup !!}</td>
            <td>{!! $customerInvoiceDirect->createdUserSystemID !!}</td>
            <td>{!! $customerInvoiceDirect->createdUserID !!}</td>
            <td>{!! $customerInvoiceDirect->createdPcID !!}</td>
            <td>{!! $customerInvoiceDirect->modifiedUserSystemID !!}</td>
            <td>{!! $customerInvoiceDirect->modifiedUser !!}</td>
            <td>{!! $customerInvoiceDirect->modifiedPc !!}</td>
            <td>{!! $customerInvoiceDirect->createdDateTime !!}</td>
            <td>{!! $customerInvoiceDirect->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['customerInvoiceDirects.destroy', $customerInvoiceDirect->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('customerInvoiceDirects.show', [$customerInvoiceDirect->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('customerInvoiceDirects.edit', [$customerInvoiceDirect->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>