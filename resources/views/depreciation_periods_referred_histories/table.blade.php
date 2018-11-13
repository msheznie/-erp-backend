<table class="table table-responsive" id="depreciationPeriodsReferredHistories-table">
    <thead>
        <tr>
            <th>Depreciationperiodsid</th>
        <th>Depmasterautoid</th>
        <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Servicelinesystemid</th>
        <th>Servicelinecode</th>
        <th>Fafinancecatid</th>
        <th>Famaincategory</th>
        <th>Fasubcategory</th>
        <th>Faid</th>
        <th>Facode</th>
        <th>Assetdescription</th>
        <th>Depmonth</th>
        <th>Deppercent</th>
        <th>Costunit</th>
        <th>Costunitrpt</th>
        <th>Fyid</th>
        <th>Depforfystartdate</th>
        <th>Depforfyenddate</th>
        <th>Fyperiodid</th>
        <th>Depforfyperiodstartdate</th>
        <th>Depforfyperiodenddate</th>
        <th>Depmonthyear</th>
        <th>Depamountlocalcurr</th>
        <th>Depamountlocal</th>
        <th>Depamountrptcurr</th>
        <th>Depamountrpt</th>
        <th>Depdoneyn</th>
        <th>Timesreferred</th>
        <th>Createdusersystemid</th>
        <th>Createdby</th>
        <th>Createdpcid</th>
        <th>Createddatetime</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($depreciationPeriodsReferredHistories as $depreciationPeriodsReferredHistory)
        <tr>
            <td>{!! $depreciationPeriodsReferredHistory->DepreciationPeriodsID !!}</td>
            <td>{!! $depreciationPeriodsReferredHistory->depMasterAutoID !!}</td>
            <td>{!! $depreciationPeriodsReferredHistory->companySystemID !!}</td>
            <td>{!! $depreciationPeriodsReferredHistory->companyID !!}</td>
            <td>{!! $depreciationPeriodsReferredHistory->serviceLineSystemID !!}</td>
            <td>{!! $depreciationPeriodsReferredHistory->serviceLineCode !!}</td>
            <td>{!! $depreciationPeriodsReferredHistory->faFinanceCatID !!}</td>
            <td>{!! $depreciationPeriodsReferredHistory->faMainCategory !!}</td>
            <td>{!! $depreciationPeriodsReferredHistory->faSubCategory !!}</td>
            <td>{!! $depreciationPeriodsReferredHistory->faID !!}</td>
            <td>{!! $depreciationPeriodsReferredHistory->faCode !!}</td>
            <td>{!! $depreciationPeriodsReferredHistory->assetDescription !!}</td>
            <td>{!! $depreciationPeriodsReferredHistory->depMonth !!}</td>
            <td>{!! $depreciationPeriodsReferredHistory->depPercent !!}</td>
            <td>{!! $depreciationPeriodsReferredHistory->COSTUNIT !!}</td>
            <td>{!! $depreciationPeriodsReferredHistory->costUnitRpt !!}</td>
            <td>{!! $depreciationPeriodsReferredHistory->FYID !!}</td>
            <td>{!! $depreciationPeriodsReferredHistory->depForFYStartDate !!}</td>
            <td>{!! $depreciationPeriodsReferredHistory->depForFYEndDate !!}</td>
            <td>{!! $depreciationPeriodsReferredHistory->FYperiodID !!}</td>
            <td>{!! $depreciationPeriodsReferredHistory->depForFYperiodStartDate !!}</td>
            <td>{!! $depreciationPeriodsReferredHistory->depForFYperiodEndDate !!}</td>
            <td>{!! $depreciationPeriodsReferredHistory->depMonthYear !!}</td>
            <td>{!! $depreciationPeriodsReferredHistory->depAmountLocalCurr !!}</td>
            <td>{!! $depreciationPeriodsReferredHistory->depAmountLocal !!}</td>
            <td>{!! $depreciationPeriodsReferredHistory->depAmountRptCurr !!}</td>
            <td>{!! $depreciationPeriodsReferredHistory->depAmountRpt !!}</td>
            <td>{!! $depreciationPeriodsReferredHistory->depDoneYN !!}</td>
            <td>{!! $depreciationPeriodsReferredHistory->timesReferred !!}</td>
            <td>{!! $depreciationPeriodsReferredHistory->createdUserSystemID !!}</td>
            <td>{!! $depreciationPeriodsReferredHistory->createdBy !!}</td>
            <td>{!! $depreciationPeriodsReferredHistory->createdPCid !!}</td>
            <td>{!! $depreciationPeriodsReferredHistory->createdDateTime !!}</td>
            <td>{!! $depreciationPeriodsReferredHistory->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['depreciationPeriodsReferredHistories.destroy', $depreciationPeriodsReferredHistory->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('depreciationPeriodsReferredHistories.show', [$depreciationPeriodsReferredHistory->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('depreciationPeriodsReferredHistories.edit', [$depreciationPeriodsReferredHistory->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>