<table class="table table-responsive" id="creditNoteReferredbacks-table">
    <thead>
        <tr>
            <th>Creditnoteautoid</th>
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
        <th>Approvedbyuserid</th>
        <th>Approvedbyusersystemid</th>
        <th>Posteddate</th>
        <th>Secondarylogocompanysystemid</th>
        <th>Secondarylogocompid</th>
        <th>Secondarylogo</th>
        <th>Matchinvoice</th>
        <th>Matchingconfirmedyn</th>
        <th>Matchingconfirmedbyempsystemid</th>
        <th>Matchingconfirmedbyempid</th>
        <th>Matchingconfirmedbyname</th>
        <th>Matchingconfirmeddate</th>
        <th>Documenttype</th>
        <th>Refferedbackyn</th>
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
        <th>Createddateandtime</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($creditNoteReferredbacks as $creditNoteReferredback)
        <tr>
            <td>{!! $creditNoteReferredback->creditNoteAutoID !!}</td>
            <td>{!! $creditNoteReferredback->companySystemID !!}</td>
            <td>{!! $creditNoteReferredback->companyID !!}</td>
            <td>{!! $creditNoteReferredback->documentSystemiD !!}</td>
            <td>{!! $creditNoteReferredback->documentID !!}</td>
            <td>{!! $creditNoteReferredback->serialNo !!}</td>
            <td>{!! $creditNoteReferredback->companyFinanceYearID !!}</td>
            <td>{!! $creditNoteReferredback->FYBiggin !!}</td>
            <td>{!! $creditNoteReferredback->FYEnd !!}</td>
            <td>{!! $creditNoteReferredback->companyFinancePeriodID !!}</td>
            <td>{!! $creditNoteReferredback->FYPeriodDateFrom !!}</td>
            <td>{!! $creditNoteReferredback->FYPeriodDateTo !!}</td>
            <td>{!! $creditNoteReferredback->creditNoteCode !!}</td>
            <td>{!! $creditNoteReferredback->creditNoteDate !!}</td>
            <td>{!! $creditNoteReferredback->comments !!}</td>
            <td>{!! $creditNoteReferredback->customerID !!}</td>
            <td>{!! $creditNoteReferredback->customerGLCodeSystemID !!}</td>
            <td>{!! $creditNoteReferredback->customerGLCode !!}</td>
            <td>{!! $creditNoteReferredback->customerCurrencyID !!}</td>
            <td>{!! $creditNoteReferredback->customerCurrencyER !!}</td>
            <td>{!! $creditNoteReferredback->companyReportingCurrencyID !!}</td>
            <td>{!! $creditNoteReferredback->companyReportingER !!}</td>
            <td>{!! $creditNoteReferredback->localCurrencyID !!}</td>
            <td>{!! $creditNoteReferredback->localCurrencyER !!}</td>
            <td>{!! $creditNoteReferredback->creditAmountTrans !!}</td>
            <td>{!! $creditNoteReferredback->creditAmountLocal !!}</td>
            <td>{!! $creditNoteReferredback->creditAmountRpt !!}</td>
            <td>{!! $creditNoteReferredback->confirmedYN !!}</td>
            <td>{!! $creditNoteReferredback->confirmedByEmpSystemID !!}</td>
            <td>{!! $creditNoteReferredback->confirmedByEmpID !!}</td>
            <td>{!! $creditNoteReferredback->confirmedByName !!}</td>
            <td>{!! $creditNoteReferredback->confirmedDate !!}</td>
            <td>{!! $creditNoteReferredback->approved !!}</td>
            <td>{!! $creditNoteReferredback->approvedDate !!}</td>
            <td>{!! $creditNoteReferredback->approvedByUserID !!}</td>
            <td>{!! $creditNoteReferredback->approvedByUserSystemID !!}</td>
            <td>{!! $creditNoteReferredback->postedDate !!}</td>
            <td>{!! $creditNoteReferredback->secondaryLogoCompanySystemID !!}</td>
            <td>{!! $creditNoteReferredback->secondaryLogoCompID !!}</td>
            <td>{!! $creditNoteReferredback->secondaryLogo !!}</td>
            <td>{!! $creditNoteReferredback->matchInvoice !!}</td>
            <td>{!! $creditNoteReferredback->matchingConfirmedYN !!}</td>
            <td>{!! $creditNoteReferredback->matchingConfirmedByEmpSystemID !!}</td>
            <td>{!! $creditNoteReferredback->matchingConfirmedByEmpID !!}</td>
            <td>{!! $creditNoteReferredback->matchingConfirmedByName !!}</td>
            <td>{!! $creditNoteReferredback->matchingConfirmedDate !!}</td>
            <td>{!! $creditNoteReferredback->documentType !!}</td>
            <td>{!! $creditNoteReferredback->refferedBackYN !!}</td>
            <td>{!! $creditNoteReferredback->timesReferred !!}</td>
            <td>{!! $creditNoteReferredback->RollLevForApp_curr !!}</td>
            <td>{!! $creditNoteReferredback->createdUserGroup !!}</td>
            <td>{!! $creditNoteReferredback->createdUserSystemID !!}</td>
            <td>{!! $creditNoteReferredback->createdUserID !!}</td>
            <td>{!! $creditNoteReferredback->createdPcID !!}</td>
            <td>{!! $creditNoteReferredback->modifiedUserSystemID !!}</td>
            <td>{!! $creditNoteReferredback->modifiedUser !!}</td>
            <td>{!! $creditNoteReferredback->modifiedPc !!}</td>
            <td>{!! $creditNoteReferredback->createdDateTime !!}</td>
            <td>{!! $creditNoteReferredback->createdDateAndTime !!}</td>
            <td>{!! $creditNoteReferredback->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['creditNoteReferredbacks.destroy', $creditNoteReferredback->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('creditNoteReferredbacks.show', [$creditNoteReferredback->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('creditNoteReferredbacks.edit', [$creditNoteReferredback->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>