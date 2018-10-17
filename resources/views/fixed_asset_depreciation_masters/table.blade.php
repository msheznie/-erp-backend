<table class="table table-responsive" id="fixedAssetDepreciationMasters-table">
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
        <th>Fyperioddatefrom</th>
        <th>Fyperioddateto</th>
        <th>Depcode</th>
        <th>Depdate</th>
        <th>Depmonthyear</th>
        <th>Deplocalcur</th>
        <th>Depamountlocal</th>
        <th>Deprptcur</th>
        <th>Depamountrpt</th>
        <th>Confirmedyn</th>
        <th>Confirmedbyempsystemid</th>
        <th>Confirmedbyempid</th>
        <th>Confirmedbyempname</th>
        <th>Confirmeddate</th>
        <th>Approved</th>
        <th>Approveddate</th>
        <th>Createduserid</th>
        <th>Createdpcid</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($fixedAssetDepreciationMasters as $fixedAssetDepreciationMaster)
        <tr>
            <td>{!! $fixedAssetDepreciationMaster->companySystemID !!}</td>
            <td>{!! $fixedAssetDepreciationMaster->companyID !!}</td>
            <td>{!! $fixedAssetDepreciationMaster->documentSystemID !!}</td>
            <td>{!! $fixedAssetDepreciationMaster->documentID !!}</td>
            <td>{!! $fixedAssetDepreciationMaster->serialNo !!}</td>
            <td>{!! $fixedAssetDepreciationMaster->companyFinanceYearID !!}</td>
            <td>{!! $fixedAssetDepreciationMaster->FYBiggin !!}</td>
            <td>{!! $fixedAssetDepreciationMaster->FYEnd !!}</td>
            <td>{!! $fixedAssetDepreciationMaster->FYPeriodDateFrom !!}</td>
            <td>{!! $fixedAssetDepreciationMaster->FYPeriodDateTo !!}</td>
            <td>{!! $fixedAssetDepreciationMaster->depCode !!}</td>
            <td>{!! $fixedAssetDepreciationMaster->depDate !!}</td>
            <td>{!! $fixedAssetDepreciationMaster->depMonthYear !!}</td>
            <td>{!! $fixedAssetDepreciationMaster->depLocalCur !!}</td>
            <td>{!! $fixedAssetDepreciationMaster->depAmountLocal !!}</td>
            <td>{!! $fixedAssetDepreciationMaster->depRptCur !!}</td>
            <td>{!! $fixedAssetDepreciationMaster->depAmountRpt !!}</td>
            <td>{!! $fixedAssetDepreciationMaster->confirmedYN !!}</td>
            <td>{!! $fixedAssetDepreciationMaster->confirmedByEmpSystemID !!}</td>
            <td>{!! $fixedAssetDepreciationMaster->confirmedByEmpID !!}</td>
            <td>{!! $fixedAssetDepreciationMaster->confirmedByEmpName !!}</td>
            <td>{!! $fixedAssetDepreciationMaster->confirmedDate !!}</td>
            <td>{!! $fixedAssetDepreciationMaster->approved !!}</td>
            <td>{!! $fixedAssetDepreciationMaster->approvedDate !!}</td>
            <td>{!! $fixedAssetDepreciationMaster->createdUserID !!}</td>
            <td>{!! $fixedAssetDepreciationMaster->createdPCID !!}</td>
            <td>{!! $fixedAssetDepreciationMaster->timeStamp !!}</td>
            <td>
                {!! Form::open(['route' => ['fixedAssetDepreciationMasters.destroy', $fixedAssetDepreciationMaster->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('fixedAssetDepreciationMasters.show', [$fixedAssetDepreciationMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('fixedAssetDepreciationMasters.edit', [$fixedAssetDepreciationMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>