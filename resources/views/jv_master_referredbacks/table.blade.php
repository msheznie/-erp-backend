<table class="table table-responsive" id="jvMasterReferredbacks-table">
    <thead>
        <tr>
            <th>Jvmasterautoid</th>
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
        <th>Jvcode</th>
        <th>Jvdate</th>
        <th>Recurringjvmasterautoid</th>
        <th>Recurringmonth</th>
        <th>Recurringyear</th>
        <th>Jvnarration</th>
        <th>Currencyid</th>
        <th>Currencyer</th>
        <th>Rptcurrencyid</th>
        <th>Rptcurrencyer</th>
        <th>Empid</th>
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
        <th>Jvtype</th>
        <th>Isreverseaccyn</th>
        <th>Refferedbackyn</th>
        <th>Timesreferred</th>
        <th>Rolllevforapp Curr</th>
        <th>Isrelatedpartyyn</th>
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
    @foreach($jvMasterReferredbacks as $jvMasterReferredback)
        <tr>
            <td>{!! $jvMasterReferredback->jvMasterAutoId !!}</td>
            <td>{!! $jvMasterReferredback->companySystemID !!}</td>
            <td>{!! $jvMasterReferredback->companyID !!}</td>
            <td>{!! $jvMasterReferredback->documentSystemID !!}</td>
            <td>{!! $jvMasterReferredback->documentID !!}</td>
            <td>{!! $jvMasterReferredback->serialNo !!}</td>
            <td>{!! $jvMasterReferredback->companyFinanceYearID !!}</td>
            <td>{!! $jvMasterReferredback->FYBiggin !!}</td>
            <td>{!! $jvMasterReferredback->FYEnd !!}</td>
            <td>{!! $jvMasterReferredback->companyFinancePeriodID !!}</td>
            <td>{!! $jvMasterReferredback->FYPeriodDateFrom !!}</td>
            <td>{!! $jvMasterReferredback->FYPeriodDateTo !!}</td>
            <td>{!! $jvMasterReferredback->JVcode !!}</td>
            <td>{!! $jvMasterReferredback->JVdate !!}</td>
            <td>{!! $jvMasterReferredback->recurringjvMasterAutoId !!}</td>
            <td>{!! $jvMasterReferredback->recurringMonth !!}</td>
            <td>{!! $jvMasterReferredback->recurringYear !!}</td>
            <td>{!! $jvMasterReferredback->JVNarration !!}</td>
            <td>{!! $jvMasterReferredback->currencyID !!}</td>
            <td>{!! $jvMasterReferredback->currencyER !!}</td>
            <td>{!! $jvMasterReferredback->rptCurrencyID !!}</td>
            <td>{!! $jvMasterReferredback->rptCurrencyER !!}</td>
            <td>{!! $jvMasterReferredback->empID !!}</td>
            <td>{!! $jvMasterReferredback->confirmedYN !!}</td>
            <td>{!! $jvMasterReferredback->confirmedByEmpSystemID !!}</td>
            <td>{!! $jvMasterReferredback->confirmedByEmpID !!}</td>
            <td>{!! $jvMasterReferredback->confirmedByName !!}</td>
            <td>{!! $jvMasterReferredback->confirmedDate !!}</td>
            <td>{!! $jvMasterReferredback->approved !!}</td>
            <td>{!! $jvMasterReferredback->approvedDate !!}</td>
            <td>{!! $jvMasterReferredback->approvedByUserID !!}</td>
            <td>{!! $jvMasterReferredback->approvedByUserSystemID !!}</td>
            <td>{!! $jvMasterReferredback->postedDate !!}</td>
            <td>{!! $jvMasterReferredback->jvType !!}</td>
            <td>{!! $jvMasterReferredback->isReverseAccYN !!}</td>
            <td>{!! $jvMasterReferredback->refferedBackYN !!}</td>
            <td>{!! $jvMasterReferredback->timesReferred !!}</td>
            <td>{!! $jvMasterReferredback->RollLevForApp_curr !!}</td>
            <td>{!! $jvMasterReferredback->isRelatedPartyYN !!}</td>
            <td>{!! $jvMasterReferredback->createdUserGroup !!}</td>
            <td>{!! $jvMasterReferredback->createdUserSystemID !!}</td>
            <td>{!! $jvMasterReferredback->createdUserID !!}</td>
            <td>{!! $jvMasterReferredback->createdPcID !!}</td>
            <td>{!! $jvMasterReferredback->modifiedUserSystemID !!}</td>
            <td>{!! $jvMasterReferredback->modifiedUser !!}</td>
            <td>{!! $jvMasterReferredback->modifiedPc !!}</td>
            <td>{!! $jvMasterReferredback->createdDateTime !!}</td>
            <td>{!! $jvMasterReferredback->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['jvMasterReferredbacks.destroy', $jvMasterReferredback->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('jvMasterReferredbacks.show', [$jvMasterReferredback->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('jvMasterReferredbacks.edit', [$jvMasterReferredback->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>