<table class="table table-responsive" id="debitNotes-table">
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
        <th>Debitnotecode</th>
        <th>Debitnotedate</th>
        <th>Comments</th>
        <th>Supplierid</th>
        <th>Supplierglcode</th>
        <th>Suppliertransactioncurrencyid</th>
        <th>Suppliertransactioncurrencyer</th>
        <th>Companyreportingcurrencyid</th>
        <th>Companyreportinger</th>
        <th>Localcurrencyid</th>
        <th>Localcurrencyer</th>
        <th>Debitamounttrans</th>
        <th>Debitamountlocal</th>
        <th>Debitamountrpt</th>
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
        <th>Matchinvoice</th>
        <th>Matchingconfirmedyn</th>
        <th>Matchingconfirmedbyempsystemid</th>
        <th>Matchingconfirmedbyempid</th>
        <th>Matchingconfirmedbyname</th>
        <th>Matchingconfirmeddate</th>
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
    @foreach($debitNotes as $debitNote)
        <tr>
            <td>{!! $debitNote->companySystemID !!}</td>
            <td>{!! $debitNote->companyID !!}</td>
            <td>{!! $debitNote->documentSystemID !!}</td>
            <td>{!! $debitNote->documentID !!}</td>
            <td>{!! $debitNote->serialNo !!}</td>
            <td>{!! $debitNote->companyFinanceYearID !!}</td>
            <td>{!! $debitNote->FYBiggin !!}</td>
            <td>{!! $debitNote->FYEnd !!}</td>
            <td>{!! $debitNote->companyFinancePeriodID !!}</td>
            <td>{!! $debitNote->FYPeriodDateFrom !!}</td>
            <td>{!! $debitNote->FYPeriodDateTo !!}</td>
            <td>{!! $debitNote->debitNoteCode !!}</td>
            <td>{!! $debitNote->debitNoteDate !!}</td>
            <td>{!! $debitNote->comments !!}</td>
            <td>{!! $debitNote->supplierID !!}</td>
            <td>{!! $debitNote->supplierGLCode !!}</td>
            <td>{!! $debitNote->supplierTransactionCurrencyID !!}</td>
            <td>{!! $debitNote->supplierTransactionCurrencyER !!}</td>
            <td>{!! $debitNote->companyReportingCurrencyID !!}</td>
            <td>{!! $debitNote->companyReportingER !!}</td>
            <td>{!! $debitNote->localCurrencyID !!}</td>
            <td>{!! $debitNote->localCurrencyER !!}</td>
            <td>{!! $debitNote->debitAmountTrans !!}</td>
            <td>{!! $debitNote->debitAmountLocal !!}</td>
            <td>{!! $debitNote->debitAmountRpt !!}</td>
            <td>{!! $debitNote->confirmedYN !!}</td>
            <td>{!! $debitNote->confirmedByEmpSystemID !!}</td>
            <td>{!! $debitNote->confirmedByEmpID !!}</td>
            <td>{!! $debitNote->confirmedByName !!}</td>
            <td>{!! $debitNote->confirmedDate !!}</td>
            <td>{!! $debitNote->approved !!}</td>
            <td>{!! $debitNote->approvedDate !!}</td>
            <td>{!! $debitNote->postedDate !!}</td>
            <td>{!! $debitNote->documentType !!}</td>
            <td>{!! $debitNote->timesReferred !!}</td>
            <td>{!! $debitNote->RollLevForApp_curr !!}</td>
            <td>{!! $debitNote->matchInvoice !!}</td>
            <td>{!! $debitNote->matchingConfirmedYN !!}</td>
            <td>{!! $debitNote->matchingConfirmedByEmpSystemID !!}</td>
            <td>{!! $debitNote->matchingConfirmedByEmpID !!}</td>
            <td>{!! $debitNote->matchingConfirmedByName !!}</td>
            <td>{!! $debitNote->matchingConfirmedDate !!}</td>
            <td>{!! $debitNote->createdUserGroup !!}</td>
            <td>{!! $debitNote->createdUserSystemID !!}</td>
            <td>{!! $debitNote->createdUserID !!}</td>
            <td>{!! $debitNote->createdPcID !!}</td>
            <td>{!! $debitNote->modifiedUserSystemID !!}</td>
            <td>{!! $debitNote->modifiedUser !!}</td>
            <td>{!! $debitNote->modifiedPc !!}</td>
            <td>{!! $debitNote->createdDateTime !!}</td>
            <td>{!! $debitNote->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['debitNotes.destroy', $debitNote->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('debitNotes.show', [$debitNote->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('debitNotes.edit', [$debitNote->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>