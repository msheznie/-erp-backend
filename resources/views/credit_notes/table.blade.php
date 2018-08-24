<table class="table table-responsive" id="creditNotes-table">
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
        <th>Creditnotecode</th>
        <th>Creditnotedate</th>
        <th>Comments</th>
        <th>Customerid</th>
        <th>Customerglcodesystemid</th>
        <th>Customerglcode</th>
        <th>Customercurrencyid</th>
        <th>Customercurrencyer</th>
        <th>Companyreportingcurrencyid</th>
        <th>Companyreportinger</th>
        <th>Localcurrencyid</th>
        <th>Localcurrencyer</th>
        <th>Creditamounttrans</th>
        <th>Creditamountlocal</th>
        <th>Creditamountrpt</th>
        <th>Confirmedyn</th>
        <th>Confirmedbyempsystemid</th>
        <th>Confirmedbyempid</th>
        <th>Confirmedbyname</th>
        <th>Confirmeddate</th>
        <th>Approved</th>
        <th>Approveddate</th>
        <th>Posteddate</th>
        <th>Secondarylogocompid</th>
        <th>Secondarylogo</th>
        <th>Matchinvoice</th>
        <th>Matchingconfirmedyn</th>
        <th>Matchingconfirmedbyempsystemid</th>
        <th>Matchingconfirmedbyempid</th>
        <th>Matchingconfirmedbyname</th>
        <th>Matchingconfirmeddate</th>
        <th>Documenttype</th>
        <th>Timesreferred</th>
        <th>Rolllevforapp Curr</th>
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
    @foreach($creditNotes as $creditNote)
        <tr>
            <td>{!! $creditNote->companySystemID !!}</td>
            <td>{!! $creditNote->companyID !!}</td>
            <td>{!! $creditNote->documentSystemiD !!}</td>
            <td>{!! $creditNote->documentID !!}</td>
            <td>{!! $creditNote->serialNo !!}</td>
            <td>{!! $creditNote->companyFinanceYearID !!}</td>
            <td>{!! $creditNote->FYBiggin !!}</td>
            <td>{!! $creditNote->FYEnd !!}</td>
            <td>{!! $creditNote->companyFinancePeriodID !!}</td>
            <td>{!! $creditNote->FYPeriodDateFrom !!}</td>
            <td>{!! $creditNote->FYPeriodDateTo !!}</td>
            <td>{!! $creditNote->creditNoteCode !!}</td>
            <td>{!! $creditNote->creditNoteDate !!}</td>
            <td>{!! $creditNote->comments !!}</td>
            <td>{!! $creditNote->customerID !!}</td>
            <td>{!! $creditNote->customerGLCodeSystemID !!}</td>
            <td>{!! $creditNote->customerGLCode !!}</td>
            <td>{!! $creditNote->customerCurrencyID !!}</td>
            <td>{!! $creditNote->customerCurrencyER !!}</td>
            <td>{!! $creditNote->companyReportingCurrencyID !!}</td>
            <td>{!! $creditNote->companyReportingER !!}</td>
            <td>{!! $creditNote->localCurrencyID !!}</td>
            <td>{!! $creditNote->localCurrencyER !!}</td>
            <td>{!! $creditNote->creditAmountTrans !!}</td>
            <td>{!! $creditNote->creditAmountLocal !!}</td>
            <td>{!! $creditNote->creditAmountRpt !!}</td>
            <td>{!! $creditNote->confirmedYN !!}</td>
            <td>{!! $creditNote->confirmedByEmpSystemID !!}</td>
            <td>{!! $creditNote->confirmedByEmpID !!}</td>
            <td>{!! $creditNote->confirmedByName !!}</td>
            <td>{!! $creditNote->confirmedDate !!}</td>
            <td>{!! $creditNote->approved !!}</td>
            <td>{!! $creditNote->approvedDate !!}</td>
            <td>{!! $creditNote->postedDate !!}</td>
            <td>{!! $creditNote->secondaryLogoCompID !!}</td>
            <td>{!! $creditNote->secondaryLogo !!}</td>
            <td>{!! $creditNote->matchInvoice !!}</td>
            <td>{!! $creditNote->matchingConfirmedYN !!}</td>
            <td>{!! $creditNote->matchingConfirmedByEmpSystemID !!}</td>
            <td>{!! $creditNote->matchingConfirmedByEmpID !!}</td>
            <td>{!! $creditNote->matchingConfirmedByName !!}</td>
            <td>{!! $creditNote->matchingConfirmedDate !!}</td>
            <td>{!! $creditNote->documentType !!}</td>
            <td>{!! $creditNote->timesReferred !!}</td>
            <td>{!! $creditNote->RollLevForApp_curr !!}</td>
            <td>{!! $creditNote->createdUserGroup !!}</td>
            <td>{!! $creditNote->createdUserSystemID !!}</td>
            <td>{!! $creditNote->createdUserID !!}</td>
            <td>{!! $creditNote->createdPcID !!}</td>
            <td>{!! $creditNote->modifiedUserSystemID !!}</td>
            <td>{!! $creditNote->modifiedUser !!}</td>
            <td>{!! $creditNote->modifiedPc !!}</td>
            <td>{!! $creditNote->createdDateTime !!}</td>
            <td>{!! $creditNote->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['creditNotes.destroy', $creditNote->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('creditNotes.show', [$creditNote->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('creditNotes.edit', [$creditNote->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>