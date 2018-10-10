<table class="table table-responsive" id="assetDepreciationPeriods-table">
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
    @foreach($assetDepreciationPeriods as $assetDepreciationPeriod)
        <tr>
            <td>{!! $assetDepreciationPeriod->depMasterAutoID !!}</td>
            <td>{!! $assetDepreciationPeriod->companySystemID !!}</td>
            <td>{!! $assetDepreciationPeriod->companyID !!}</td>
            <td>{!! $assetDepreciationPeriod->serviceLineSystemID !!}</td>
            <td>{!! $assetDepreciationPeriod->serviceLineCode !!}</td>
            <td>{!! $assetDepreciationPeriod->faFinanceCatID !!}</td>
            <td>{!! $assetDepreciationPeriod->faMainCategory !!}</td>
            <td>{!! $assetDepreciationPeriod->faSubCategory !!}</td>
            <td>{!! $assetDepreciationPeriod->faID !!}</td>
            <td>{!! $assetDepreciationPeriod->faCode !!}</td>
            <td>{!! $assetDepreciationPeriod->assetDescription !!}</td>
            <td>{!! $assetDepreciationPeriod->depMonth !!}</td>
            <td>{!! $assetDepreciationPeriod->depPercent !!}</td>
            <td>{!! $assetDepreciationPeriod->COSTUNIT !!}</td>
            <td>{!! $assetDepreciationPeriod->costUnitRpt !!}</td>
            <td>{!! $assetDepreciationPeriod->FYID !!}</td>
            <td>{!! $assetDepreciationPeriod->depForFYStartDate !!}</td>
            <td>{!! $assetDepreciationPeriod->depForFYEndDate !!}</td>
            <td>{!! $assetDepreciationPeriod->FYperiodID !!}</td>
            <td>{!! $assetDepreciationPeriod->depForFYperiodStartDate !!}</td>
            <td>{!! $assetDepreciationPeriod->depForFYperiodEndDate !!}</td>
            <td>{!! $assetDepreciationPeriod->depMonthYear !!}</td>
            <td>{!! $assetDepreciationPeriod->depAmountLocalCurr !!}</td>
            <td>{!! $assetDepreciationPeriod->depAmountLocal !!}</td>
            <td>{!! $assetDepreciationPeriod->depAmountRptCurr !!}</td>
            <td>{!! $assetDepreciationPeriod->depAmountRpt !!}</td>
            <td>{!! $assetDepreciationPeriod->depDoneYN !!}</td>
            <td>{!! $assetDepreciationPeriod->createdBy !!}</td>
            <td>{!! $assetDepreciationPeriod->createdPCid !!}</td>
            <td>{!! $assetDepreciationPeriod->createdDateTime !!}</td>
            <td>{!! $assetDepreciationPeriod->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['assetDepreciationPeriods.destroy', $assetDepreciationPeriod->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('assetDepreciationPeriods.show', [$assetDepreciationPeriod->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('assetDepreciationPeriods.edit', [$assetDepreciationPeriod->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>