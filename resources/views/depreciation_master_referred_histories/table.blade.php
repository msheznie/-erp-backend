<table class="table table-responsive" id="depreciationMasterReferredHistories-table">
    <thead>
        <tr>
            <th>Depmasterautoid</th>
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
        <th>Depcode</th>
        <th>Depdate</th>
        <th>Depmonthyear</th>
        <th>Deplocalcur</th>
        <th>Depamountlocal</th>
        <th>Deprptcur</th>
        <th>Depamountrpt</th>
        <th>Timesreferred</th>
        <th>Refferedbackyn</th>
        <th>Rolllevforapp Curr</th>
        <th>Isdepprocessingyn</th>
        <th>Confirmedyn</th>
        <th>Confirmedbyempsystemid</th>
        <th>Confirmedbyempid</th>
        <th>Confirmedbyempname</th>
        <th>Confirmeddate</th>
        <th>Approved</th>
        <th>Approveddate</th>
        <th>Approvedbyuserid</th>
        <th>Approvedbyusersystemid</th>
        <th>Createduserid</th>
        <th>Createdusersystemid</th>
        <th>Createdpcid</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($depreciationMasterReferredHistories as $depreciationMasterReferredHistory)
        <tr>
            <td>{!! $depreciationMasterReferredHistory->depMasterAutoID !!}</td>
            <td>{!! $depreciationMasterReferredHistory->companySystemID !!}</td>
            <td>{!! $depreciationMasterReferredHistory->companyID !!}</td>
            <td>{!! $depreciationMasterReferredHistory->documentSystemID !!}</td>
            <td>{!! $depreciationMasterReferredHistory->documentID !!}</td>
            <td>{!! $depreciationMasterReferredHistory->serialNo !!}</td>
            <td>{!! $depreciationMasterReferredHistory->companyFinanceYearID !!}</td>
            <td>{!! $depreciationMasterReferredHistory->FYBiggin !!}</td>
            <td>{!! $depreciationMasterReferredHistory->FYEnd !!}</td>
            <td>{!! $depreciationMasterReferredHistory->companyFinancePeriodID !!}</td>
            <td>{!! $depreciationMasterReferredHistory->FYPeriodDateFrom !!}</td>
            <td>{!! $depreciationMasterReferredHistory->FYPeriodDateTo !!}</td>
            <td>{!! $depreciationMasterReferredHistory->depCode !!}</td>
            <td>{!! $depreciationMasterReferredHistory->depDate !!}</td>
            <td>{!! $depreciationMasterReferredHistory->depMonthYear !!}</td>
            <td>{!! $depreciationMasterReferredHistory->depLocalCur !!}</td>
            <td>{!! $depreciationMasterReferredHistory->depAmountLocal !!}</td>
            <td>{!! $depreciationMasterReferredHistory->depRptCur !!}</td>
            <td>{!! $depreciationMasterReferredHistory->depAmountRpt !!}</td>
            <td>{!! $depreciationMasterReferredHistory->timesReferred !!}</td>
            <td>{!! $depreciationMasterReferredHistory->refferedBackYN !!}</td>
            <td>{!! $depreciationMasterReferredHistory->RollLevForApp_curr !!}</td>
            <td>{!! $depreciationMasterReferredHistory->isDepProcessingYN !!}</td>
            <td>{!! $depreciationMasterReferredHistory->confirmedYN !!}</td>
            <td>{!! $depreciationMasterReferredHistory->confirmedByEmpSystemID !!}</td>
            <td>{!! $depreciationMasterReferredHistory->confirmedByEmpID !!}</td>
            <td>{!! $depreciationMasterReferredHistory->confirmedByEmpName !!}</td>
            <td>{!! $depreciationMasterReferredHistory->confirmedDate !!}</td>
            <td>{!! $depreciationMasterReferredHistory->approved !!}</td>
            <td>{!! $depreciationMasterReferredHistory->approvedDate !!}</td>
            <td>{!! $depreciationMasterReferredHistory->approvedByUserID !!}</td>
            <td>{!! $depreciationMasterReferredHistory->approvedByUserSystemID !!}</td>
            <td>{!! $depreciationMasterReferredHistory->createdUserID !!}</td>
            <td>{!! $depreciationMasterReferredHistory->createdUserSystemID !!}</td>
            <td>{!! $depreciationMasterReferredHistory->createdPCID !!}</td>
            <td>{!! $depreciationMasterReferredHistory->timeStamp !!}</td>
            <td>
                {!! Form::open(['route' => ['depreciationMasterReferredHistories.destroy', $depreciationMasterReferredHistory->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('depreciationMasterReferredHistories.show', [$depreciationMasterReferredHistory->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('depreciationMasterReferredHistories.edit', [$depreciationMasterReferredHistory->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>