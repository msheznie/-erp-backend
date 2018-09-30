<table class="table table-responsive" id="bookInvSuppMasterRefferedBacks-table">
    <thead>
        <tr>
            <th>Bookingsuppmasinvautoid</th>
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
        <th>Supplierglcodesystemid</th>
        <th>Supplierglcode</th>
        <th>Unbilledgrvaccountsystemid</th>
        <th>Unbilledgrvaccount</th>
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
        <th>Approvedbyuserid</th>
        <th>Approvedbyusersystemid</th>
        <th>Posteddate</th>
        <th>Documenttype</th>
        <th>Refferedbackyn</th>
        <th>Timesreferred</th>
        <th>Rolllevforapp Curr</th>
        <th>Intercompanytransferyn</th>
        <th>Createdusergroup</th>
        <th>Createdusersystemid</th>
        <th>Createduserid</th>
        <th>Createdpcid</th>
        <th>Modifiedusersystemid</th>
        <th>Modifieduser</th>
        <th>Modifiedpc</th>
        <th>Createddatetime</th>
        <th>Createddateandtime</th>
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
    @foreach($bookInvSuppMasterRefferedBacks as $bookInvSuppMasterRefferedBack)
        <tr>
            <td>{!! $bookInvSuppMasterRefferedBack->bookingSuppMasInvAutoID !!}</td>
            <td>{!! $bookInvSuppMasterRefferedBack->companySystemID !!}</td>
            <td>{!! $bookInvSuppMasterRefferedBack->companyID !!}</td>
            <td>{!! $bookInvSuppMasterRefferedBack->documentSystemID !!}</td>
            <td>{!! $bookInvSuppMasterRefferedBack->documentID !!}</td>
            <td>{!! $bookInvSuppMasterRefferedBack->serialNo !!}</td>
            <td>{!! $bookInvSuppMasterRefferedBack->companyFinanceYearID !!}</td>
            <td>{!! $bookInvSuppMasterRefferedBack->FYBiggin !!}</td>
            <td>{!! $bookInvSuppMasterRefferedBack->FYEnd !!}</td>
            <td>{!! $bookInvSuppMasterRefferedBack->companyFinancePeriodID !!}</td>
            <td>{!! $bookInvSuppMasterRefferedBack->FYPeriodDateFrom !!}</td>
            <td>{!! $bookInvSuppMasterRefferedBack->FYPeriodDateTo !!}</td>
            <td>{!! $bookInvSuppMasterRefferedBack->bookingInvCode !!}</td>
            <td>{!! $bookInvSuppMasterRefferedBack->bookingDate !!}</td>
            <td>{!! $bookInvSuppMasterRefferedBack->comments !!}</td>
            <td>{!! $bookInvSuppMasterRefferedBack->secondaryRefNo !!}</td>
            <td>{!! $bookInvSuppMasterRefferedBack->supplierID !!}</td>
            <td>{!! $bookInvSuppMasterRefferedBack->supplierGLCodeSystemID !!}</td>
            <td>{!! $bookInvSuppMasterRefferedBack->supplierGLCode !!}</td>
            <td>{!! $bookInvSuppMasterRefferedBack->UnbilledGRVAccountSystemID !!}</td>
            <td>{!! $bookInvSuppMasterRefferedBack->UnbilledGRVAccount !!}</td>
            <td>{!! $bookInvSuppMasterRefferedBack->supplierInvoiceNo !!}</td>
            <td>{!! $bookInvSuppMasterRefferedBack->supplierInvoiceDate !!}</td>
            <td>{!! $bookInvSuppMasterRefferedBack->supplierTransactionCurrencyID !!}</td>
            <td>{!! $bookInvSuppMasterRefferedBack->supplierTransactionCurrencyER !!}</td>
            <td>{!! $bookInvSuppMasterRefferedBack->companyReportingCurrencyID !!}</td>
            <td>{!! $bookInvSuppMasterRefferedBack->companyReportingER !!}</td>
            <td>{!! $bookInvSuppMasterRefferedBack->localCurrencyID !!}</td>
            <td>{!! $bookInvSuppMasterRefferedBack->localCurrencyER !!}</td>
            <td>{!! $bookInvSuppMasterRefferedBack->bookingAmountTrans !!}</td>
            <td>{!! $bookInvSuppMasterRefferedBack->bookingAmountLocal !!}</td>
            <td>{!! $bookInvSuppMasterRefferedBack->bookingAmountRpt !!}</td>
            <td>{!! $bookInvSuppMasterRefferedBack->confirmedYN !!}</td>
            <td>{!! $bookInvSuppMasterRefferedBack->confirmedByEmpSystemID !!}</td>
            <td>{!! $bookInvSuppMasterRefferedBack->confirmedByEmpID !!}</td>
            <td>{!! $bookInvSuppMasterRefferedBack->confirmedByName !!}</td>
            <td>{!! $bookInvSuppMasterRefferedBack->confirmedDate !!}</td>
            <td>{!! $bookInvSuppMasterRefferedBack->approved !!}</td>
            <td>{!! $bookInvSuppMasterRefferedBack->approvedDate !!}</td>
            <td>{!! $bookInvSuppMasterRefferedBack->approvedByUserID !!}</td>
            <td>{!! $bookInvSuppMasterRefferedBack->approvedByUserSystemID !!}</td>
            <td>{!! $bookInvSuppMasterRefferedBack->postedDate !!}</td>
            <td>{!! $bookInvSuppMasterRefferedBack->documentType !!}</td>
            <td>{!! $bookInvSuppMasterRefferedBack->refferedBackYN !!}</td>
            <td>{!! $bookInvSuppMasterRefferedBack->timesReferred !!}</td>
            <td>{!! $bookInvSuppMasterRefferedBack->RollLevForApp_curr !!}</td>
            <td>{!! $bookInvSuppMasterRefferedBack->interCompanyTransferYN !!}</td>
            <td>{!! $bookInvSuppMasterRefferedBack->createdUserGroup !!}</td>
            <td>{!! $bookInvSuppMasterRefferedBack->createdUserSystemID !!}</td>
            <td>{!! $bookInvSuppMasterRefferedBack->createdUserID !!}</td>
            <td>{!! $bookInvSuppMasterRefferedBack->createdPcID !!}</td>
            <td>{!! $bookInvSuppMasterRefferedBack->modifiedUserSystemID !!}</td>
            <td>{!! $bookInvSuppMasterRefferedBack->modifiedUser !!}</td>
            <td>{!! $bookInvSuppMasterRefferedBack->modifiedPc !!}</td>
            <td>{!! $bookInvSuppMasterRefferedBack->createdDateTime !!}</td>
            <td>{!! $bookInvSuppMasterRefferedBack->createdDateAndTime !!}</td>
            <td>{!! $bookInvSuppMasterRefferedBack->cancelYN !!}</td>
            <td>{!! $bookInvSuppMasterRefferedBack->cancelComment !!}</td>
            <td>{!! $bookInvSuppMasterRefferedBack->cancelDate !!}</td>
            <td>{!! $bookInvSuppMasterRefferedBack->canceledByEmpSystemID !!}</td>
            <td>{!! $bookInvSuppMasterRefferedBack->canceledByEmpID !!}</td>
            <td>{!! $bookInvSuppMasterRefferedBack->canceledByEmpName !!}</td>
            <td>{!! $bookInvSuppMasterRefferedBack->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['bookInvSuppMasterRefferedBacks.destroy', $bookInvSuppMasterRefferedBack->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('bookInvSuppMasterRefferedBacks.show', [$bookInvSuppMasterRefferedBack->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('bookInvSuppMasterRefferedBacks.edit', [$bookInvSuppMasterRefferedBack->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>