<table class="table table-responsive" id="customerInvoices-table">
    <thead>
        <tr>
            <th>Transactionmode</th>
        <th>Companyid</th>
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
        <th>Canceledbyempid</th>
        <th>Canceledbyempname</th>
        <th>Vatoutputglcodesystemid</th>
        <th>Vatoutputglcode</th>
        <th>Vatpercentage</th>
        <th>Vatamount</th>
        <th>Vatamountlocal</th>
        <th>Vatamountrpt</th>
        <th>Canceleddatetime</th>
        <th>Canceledcomments</th>
        <th>Createdusergroup</th>
        <th>Createduserid</th>
        <th>Createdpcid</th>
        <th>Modifieduser</th>
        <th>Modifiedpc</th>
        <th>Createddatetime</th>
        <th>Timestamp</th>
        <th>Discountlocalamount</th>
        <th>Discountamount</th>
        <th>Discountrptamount</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($customerInvoices as $customerInvoice)
        <tr>
            <td>{!! $customerInvoice->transactionMode !!}</td>
            <td>{!! $customerInvoice->companyID !!}</td>
            <td>{!! $customerInvoice->documentID !!}</td>
            <td>{!! $customerInvoice->serialNo !!}</td>
            <td>{!! $customerInvoice->companyFinanceYearID !!}</td>
            <td>{!! $customerInvoice->FYBiggin !!}</td>
            <td>{!! $customerInvoice->FYEnd !!}</td>
            <td>{!! $customerInvoice->companyFinancePeriodID !!}</td>
            <td>{!! $customerInvoice->FYPeriodDateFrom !!}</td>
            <td>{!! $customerInvoice->FYPeriodDateTo !!}</td>
            <td>{!! $customerInvoice->serviceLineSystemID !!}</td>
            <td>{!! $customerInvoice->serviceLineCode !!}</td>
            <td>{!! $customerInvoice->wareHouseSystemCode !!}</td>
            <td>{!! $customerInvoice->bookingInvCode !!}</td>
            <td>{!! $customerInvoice->bookingDate !!}</td>
            <td>{!! $customerInvoice->comments !!}</td>
            <td>{!! $customerInvoice->invoiceDueDate !!}</td>
            <td>{!! $customerInvoice->customerGRVAutoID !!}</td>
            <td>{!! $customerInvoice->bankID !!}</td>
            <td>{!! $customerInvoice->bankAccountID !!}</td>
            <td>{!! $customerInvoice->performaDate !!}</td>
            <td>{!! $customerInvoice->wanNO !!}</td>
            <td>{!! $customerInvoice->PONumber !!}</td>
            <td>{!! $customerInvoice->rigNo !!}</td>
            <td>{!! $customerInvoice->customerID !!}</td>
            <td>{!! $customerInvoice->customerGLCode !!}</td>
            <td>{!! $customerInvoice->customerInvoiceNo !!}</td>
            <td>{!! $customerInvoice->customerInvoiceDate !!}</td>
            <td>{!! $customerInvoice->custTransactionCurrencyID !!}</td>
            <td>{!! $customerInvoice->custTransactionCurrencyER !!}</td>
            <td>{!! $customerInvoice->companyReportingCurrencyID !!}</td>
            <td>{!! $customerInvoice->companyReportingER !!}</td>
            <td>{!! $customerInvoice->localCurrencyID !!}</td>
            <td>{!! $customerInvoice->localCurrencyER !!}</td>
            <td>{!! $customerInvoice->bookingAmountTrans !!}</td>
            <td>{!! $customerInvoice->bookingAmountLocal !!}</td>
            <td>{!! $customerInvoice->bookingAmountRpt !!}</td>
            <td>{!! $customerInvoice->confirmedYN !!}</td>
            <td>{!! $customerInvoice->confirmedByEmpID !!}</td>
            <td>{!! $customerInvoice->confirmedByName !!}</td>
            <td>{!! $customerInvoice->confirmedDate !!}</td>
            <td>{!! $customerInvoice->approved !!}</td>
            <td>{!! $customerInvoice->approvedDate !!}</td>
            <td>{!! $customerInvoice->postedDate !!}</td>
            <td>{!! $customerInvoice->servicePeriod !!}</td>
            <td>{!! $customerInvoice->paymentInDaysForJob !!}</td>
            <td>{!! $customerInvoice->serviceStartDate !!}</td>
            <td>{!! $customerInvoice->serviceEndDate !!}</td>
            <td>{!! $customerInvoice->isPerforma !!}</td>
            <td>{!! $customerInvoice->documentType !!}</td>
            <td>{!! $customerInvoice->secondaryLogoCompID !!}</td>
            <td>{!! $customerInvoice->secondaryLogo !!}</td>
            <td>{!! $customerInvoice->timesReferred !!}</td>
            <td>{!! $customerInvoice->RollLevForApp_curr !!}</td>
            <td>{!! $customerInvoice->selectedForTracking !!}</td>
            <td>{!! $customerInvoice->customerInvoiceTrackingID !!}</td>
            <td>{!! $customerInvoice->interCompanyTransferYN !!}</td>
            <td>{!! $customerInvoice->canceledYN !!}</td>
            <td>{!! $customerInvoice->canceledByEmpID !!}</td>
            <td>{!! $customerInvoice->canceledByEmpName !!}</td>
            <td>{!! $customerInvoice->vatOutputGLCodeSystemID !!}</td>
            <td>{!! $customerInvoice->vatOutputGLCode !!}</td>
            <td>{!! $customerInvoice->VATPercentage !!}</td>
            <td>{!! $customerInvoice->VATAmount !!}</td>
            <td>{!! $customerInvoice->VATAmountLocal !!}</td>
            <td>{!! $customerInvoice->VATAmountRpt !!}</td>
            <td>{!! $customerInvoice->canceledDateTime !!}</td>
            <td>{!! $customerInvoice->canceledComments !!}</td>
            <td>{!! $customerInvoice->createdUserGroup !!}</td>
            <td>{!! $customerInvoice->createdUserID !!}</td>
            <td>{!! $customerInvoice->createdPcID !!}</td>
            <td>{!! $customerInvoice->modifiedUser !!}</td>
            <td>{!! $customerInvoice->modifiedPc !!}</td>
            <td>{!! $customerInvoice->createdDateTime !!}</td>
            <td>{!! $customerInvoice->timestamp !!}</td>
            <td>{!! $customerInvoice->discountLocalAmount !!}</td>
            <td>{!! $customerInvoice->discountAmount !!}</td>
            <td>{!! $customerInvoice->discountRptAmount !!}</td>
            <td>
                {!! Form::open(['route' => ['customerInvoices.destroy', $customerInvoice->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('customerInvoices.show', [$customerInvoice->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('customerInvoices.edit', [$customerInvoice->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>