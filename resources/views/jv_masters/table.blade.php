<table class="table table-responsive" id="jvMasters-table">
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
        <th>Posteddate</th>
        <th>Jvtype</th>
        <th>Isreverseaccyn</th>
        <th>Timesreferred</th>
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
    @foreach($jvMasters as $jvMaster)
        <tr>
            <td>{!! $jvMaster->companySystemID !!}</td>
            <td>{!! $jvMaster->companyID !!}</td>
            <td>{!! $jvMaster->documentSystemID !!}</td>
            <td>{!! $jvMaster->documentID !!}</td>
            <td>{!! $jvMaster->serialNo !!}</td>
            <td>{!! $jvMaster->companyFinanceYearID !!}</td>
            <td>{!! $jvMaster->FYBiggin !!}</td>
            <td>{!! $jvMaster->FYEnd !!}</td>
            <td>{!! $jvMaster->companyFinancePeriodID !!}</td>
            <td>{!! $jvMaster->FYPeriodDateFrom !!}</td>
            <td>{!! $jvMaster->FYPeriodDateTo !!}</td>
            <td>{!! $jvMaster->JVcode !!}</td>
            <td>{!! $jvMaster->JVdate !!}</td>
            <td>{!! $jvMaster->recurringjvMasterAutoId !!}</td>
            <td>{!! $jvMaster->recurringMonth !!}</td>
            <td>{!! $jvMaster->recurringYear !!}</td>
            <td>{!! $jvMaster->JVNarration !!}</td>
            <td>{!! $jvMaster->currencyID !!}</td>
            <td>{!! $jvMaster->currencyER !!}</td>
            <td>{!! $jvMaster->rptCurrencyID !!}</td>
            <td>{!! $jvMaster->rptCurrencyER !!}</td>
            <td>{!! $jvMaster->empID !!}</td>
            <td>{!! $jvMaster->confirmedYN !!}</td>
            <td>{!! $jvMaster->confirmedByEmpSystemID !!}</td>
            <td>{!! $jvMaster->confirmedByEmpID !!}</td>
            <td>{!! $jvMaster->confirmedByName !!}</td>
            <td>{!! $jvMaster->confirmedDate !!}</td>
            <td>{!! $jvMaster->approved !!}</td>
            <td>{!! $jvMaster->approvedDate !!}</td>
            <td>{!! $jvMaster->postedDate !!}</td>
            <td>{!! $jvMaster->jvType !!}</td>
            <td>{!! $jvMaster->isReverseAccYN !!}</td>
            <td>{!! $jvMaster->timesReferred !!}</td>
            <td>{!! $jvMaster->isRelatedPartyYN !!}</td>
            <td>{!! $jvMaster->createdUserGroup !!}</td>
            <td>{!! $jvMaster->createdUserSystemID !!}</td>
            <td>{!! $jvMaster->createdUserID !!}</td>
            <td>{!! $jvMaster->createdPcID !!}</td>
            <td>{!! $jvMaster->modifiedUserSystemID !!}</td>
            <td>{!! $jvMaster->modifiedUser !!}</td>
            <td>{!! $jvMaster->modifiedPc !!}</td>
            <td>{!! $jvMaster->createdDateTime !!}</td>
            <td>{!! $jvMaster->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['jvMasters.destroy', $jvMaster->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('jvMasters.show', [$jvMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('jvMasters.edit', [$jvMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>