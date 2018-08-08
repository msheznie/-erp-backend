<table class="table table-responsive" id="bookInvSuppMasters-table">
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
        <th>Bookinginvcode</th>
        <th>Bookingdate</th>
        <th>Comments</th>
        <th>Secondaryrefno</th>
        <th>Supplierid</th>
        <th>Supplierglcode</th>
        <th>Supplierinvoiceno</th>
        <th>Supplierinvoicedate</th>
        <th>Suppliertransactioncurrencyid</th>
        <th>Suppliertransactioncurrencyer</th>
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
        <th>Documenttype</th>
        <th>Timesreferred</th>
        <th>Rolllevforapp Curr</th>
        <th>Intercompanytransferyn</th>
        <th>Createdusergroup</th>
        <th>Createdusersystemid</th>
        <th>Createduserid</th>
        <th>Createdpcid</th>
        <th>Modifieduser</th>
        <th>Modifiedpc</th>
        <th>Createddatetime</th>
        <th>Cancelyn</th>
        <th>Cancelcomment</th>
        <th>Canceldate</th>
        <th>Canceledbyempsystemid</th>
        <th>Canceledbyempid</th>
        <th>Canceledbyempname</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($bookInvSuppMasters as $bookInvSuppMaster)
        <tr>
            <td>{!! $bookInvSuppMaster->companySystemID !!}</td>
            <td>{!! $bookInvSuppMaster->companyID !!}</td>
            <td>{!! $bookInvSuppMaster->documentSystemID !!}</td>
            <td>{!! $bookInvSuppMaster->documentID !!}</td>
            <td>{!! $bookInvSuppMaster->serialNo !!}</td>
            <td>{!! $bookInvSuppMaster->companyFinanceYearID !!}</td>
            <td>{!! $bookInvSuppMaster->FYBiggin !!}</td>
            <td>{!! $bookInvSuppMaster->FYEnd !!}</td>
            <td>{!! $bookInvSuppMaster->companyFinancePeriodID !!}</td>
            <td>{!! $bookInvSuppMaster->FYPeriodDateFrom !!}</td>
            <td>{!! $bookInvSuppMaster->FYPeriodDateTo !!}</td>
            <td>{!! $bookInvSuppMaster->bookingInvCode !!}</td>
            <td>{!! $bookInvSuppMaster->bookingDate !!}</td>
            <td>{!! $bookInvSuppMaster->comments !!}</td>
            <td>{!! $bookInvSuppMaster->secondaryRefNo !!}</td>
            <td>{!! $bookInvSuppMaster->supplierID !!}</td>
            <td>{!! $bookInvSuppMaster->supplierGLCode !!}</td>
            <td>{!! $bookInvSuppMaster->supplierInvoiceNo !!}</td>
            <td>{!! $bookInvSuppMaster->supplierInvoiceDate !!}</td>
            <td>{!! $bookInvSuppMaster->supplierTransactionCurrencyID !!}</td>
            <td>{!! $bookInvSuppMaster->supplierTransactionCurrencyER !!}</td>
            <td>{!! $bookInvSuppMaster->companyReportingCurrencyID !!}</td>
            <td>{!! $bookInvSuppMaster->companyReportingER !!}</td>
            <td>{!! $bookInvSuppMaster->localCurrencyID !!}</td>
            <td>{!! $bookInvSuppMaster->localCurrencyER !!}</td>
            <td>{!! $bookInvSuppMaster->bookingAmountTrans !!}</td>
            <td>{!! $bookInvSuppMaster->bookingAmountLocal !!}</td>
            <td>{!! $bookInvSuppMaster->bookingAmountRpt !!}</td>
            <td>{!! $bookInvSuppMaster->confirmedYN !!}</td>
            <td>{!! $bookInvSuppMaster->confirmedByEmpSystemID !!}</td>
            <td>{!! $bookInvSuppMaster->confirmedByEmpID !!}</td>
            <td>{!! $bookInvSuppMaster->confirmedByName !!}</td>
            <td>{!! $bookInvSuppMaster->confirmedDate !!}</td>
            <td>{!! $bookInvSuppMaster->approved !!}</td>
            <td>{!! $bookInvSuppMaster->approvedDate !!}</td>
            <td>{!! $bookInvSuppMaster->postedDate !!}</td>
            <td>{!! $bookInvSuppMaster->documentType !!}</td>
            <td>{!! $bookInvSuppMaster->timesReferred !!}</td>
            <td>{!! $bookInvSuppMaster->RollLevForApp_curr !!}</td>
            <td>{!! $bookInvSuppMaster->interCompanyTransferYN !!}</td>
            <td>{!! $bookInvSuppMaster->createdUserGroup !!}</td>
            <td>{!! $bookInvSuppMaster->createdUserSystemID !!}</td>
            <td>{!! $bookInvSuppMaster->createdUserID !!}</td>
            <td>{!! $bookInvSuppMaster->createdPcID !!}</td>
            <td>{!! $bookInvSuppMaster->modifiedUser !!}</td>
            <td>{!! $bookInvSuppMaster->modifiedPc !!}</td>
            <td>{!! $bookInvSuppMaster->createdDateTime !!}</td>
            <td>{!! $bookInvSuppMaster->cancelYN !!}</td>
            <td>{!! $bookInvSuppMaster->cancelComment !!}</td>
            <td>{!! $bookInvSuppMaster->cancelDate !!}</td>
            <td>{!! $bookInvSuppMaster->canceledByEmpSystemID !!}</td>
            <td>{!! $bookInvSuppMaster->canceledByEmpID !!}</td>
            <td>{!! $bookInvSuppMaster->canceledByEmpName !!}</td>
            <td>{!! $bookInvSuppMaster->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['bookInvSuppMasters.destroy', $bookInvSuppMaster->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('bookInvSuppMasters.show', [$bookInvSuppMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('bookInvSuppMasters.edit', [$bookInvSuppMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>