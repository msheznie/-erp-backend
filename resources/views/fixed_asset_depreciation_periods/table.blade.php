<table class="table table-responsive" id="fixedAssetDepreciationPeriods-table">
    <thead>
        <tr>
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
        <th>Createdby</th>
        <th>Createdpcid</th>
        <th>Createddatetime</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($fixedAssetDepreciationPeriods as $fixedAssetDepreciationPeriod)
        <tr>
            <td>{!! $fixedAssetDepreciationPeriod->depMasterAutoID !!}</td>
            <td>{!! $fixedAssetDepreciationPeriod->companySystemID !!}</td>
            <td>{!! $fixedAssetDepreciationPeriod->companyID !!}</td>
            <td>{!! $fixedAssetDepreciationPeriod->serviceLineSystemID !!}</td>
            <td>{!! $fixedAssetDepreciationPeriod->serviceLineCode !!}</td>
            <td>{!! $fixedAssetDepreciationPeriod->faFinanceCatID !!}</td>
            <td>{!! $fixedAssetDepreciationPeriod->faMainCategory !!}</td>
            <td>{!! $fixedAssetDepreciationPeriod->faSubCategory !!}</td>
            <td>{!! $fixedAssetDepreciationPeriod->faID !!}</td>
            <td>{!! $fixedAssetDepreciationPeriod->faCode !!}</td>
            <td>{!! $fixedAssetDepreciationPeriod->assetDescription !!}</td>
            <td>{!! $fixedAssetDepreciationPeriod->depMonth !!}</td>
            <td>{!! $fixedAssetDepreciationPeriod->depPercent !!}</td>
            <td>{!! $fixedAssetDepreciationPeriod->COSTUNIT !!}</td>
            <td>{!! $fixedAssetDepreciationPeriod->costUnitRpt !!}</td>
            <td>{!! $fixedAssetDepreciationPeriod->FYID !!}</td>
            <td>{!! $fixedAssetDepreciationPeriod->depForFYStartDate !!}</td>
            <td>{!! $fixedAssetDepreciationPeriod->depForFYEndDate !!}</td>
            <td>{!! $fixedAssetDepreciationPeriod->FYperiodID !!}</td>
            <td>{!! $fixedAssetDepreciationPeriod->depForFYperiodStartDate !!}</td>
            <td>{!! $fixedAssetDepreciationPeriod->depForFYperiodEndDate !!}</td>
            <td>{!! $fixedAssetDepreciationPeriod->depMonthYear !!}</td>
            <td>{!! $fixedAssetDepreciationPeriod->depAmountLocalCurr !!}</td>
            <td>{!! $fixedAssetDepreciationPeriod->depAmountLocal !!}</td>
            <td>{!! $fixedAssetDepreciationPeriod->depAmountRptCurr !!}</td>
            <td>{!! $fixedAssetDepreciationPeriod->depAmountRpt !!}</td>
            <td>{!! $fixedAssetDepreciationPeriod->depDoneYN !!}</td>
            <td>{!! $fixedAssetDepreciationPeriod->createdBy !!}</td>
            <td>{!! $fixedAssetDepreciationPeriod->createdPCid !!}</td>
            <td>{!! $fixedAssetDepreciationPeriod->createdDateTime !!}</td>
            <td>{!! $fixedAssetDepreciationPeriod->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['fixedAssetDepreciationPeriods.destroy', $fixedAssetDepreciationPeriod->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('fixedAssetDepreciationPeriods.show', [$fixedAssetDepreciationPeriod->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('fixedAssetDepreciationPeriods.edit', [$fixedAssetDepreciationPeriod->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>