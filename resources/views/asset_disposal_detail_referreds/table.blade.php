<table class="table table-responsive" id="assetDisposalDetailReferreds-table">
    <thead>
        <tr>
            <th>Assetdisposaldetailautoid</th>
        <th>Assetdisposalmasterautoid</th>
        <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Servicelinesystemid</th>
        <th>Servicelinecode</th>
        <th>Itemcode</th>
        <th>Faid</th>
        <th>Facode</th>
        <th>Faunitserialno</th>
        <th>Assetdescription</th>
        <th>Costunit</th>
        <th>Costunitrpt</th>
        <th>Netbookvaluelocal</th>
        <th>Depamountlocal</th>
        <th>Depamountrpt</th>
        <th>Netbookvaluerpt</th>
        <th>Costglcodesystemid</th>
        <th>Costglcode</th>
        <th>Accdepglcodesystemid</th>
        <th>Accdepglcode</th>
        <th>Dispoglcodesystemid</th>
        <th>Dispoglcode</th>
        <th>Timesreferred</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($assetDisposalDetailReferreds as $assetDisposalDetailReferred)
        <tr>
            <td>{!! $assetDisposalDetailReferred->assetDisposalDetailAutoID !!}</td>
            <td>{!! $assetDisposalDetailReferred->assetdisposalMasterAutoID !!}</td>
            <td>{!! $assetDisposalDetailReferred->companySystemID !!}</td>
            <td>{!! $assetDisposalDetailReferred->companyID !!}</td>
            <td>{!! $assetDisposalDetailReferred->serviceLineSystemID !!}</td>
            <td>{!! $assetDisposalDetailReferred->serviceLineCode !!}</td>
            <td>{!! $assetDisposalDetailReferred->itemCode !!}</td>
            <td>{!! $assetDisposalDetailReferred->faID !!}</td>
            <td>{!! $assetDisposalDetailReferred->faCode !!}</td>
            <td>{!! $assetDisposalDetailReferred->faUnitSerialNo !!}</td>
            <td>{!! $assetDisposalDetailReferred->assetDescription !!}</td>
            <td>{!! $assetDisposalDetailReferred->COSTUNIT !!}</td>
            <td>{!! $assetDisposalDetailReferred->costUnitRpt !!}</td>
            <td>{!! $assetDisposalDetailReferred->netBookValueLocal !!}</td>
            <td>{!! $assetDisposalDetailReferred->depAmountLocal !!}</td>
            <td>{!! $assetDisposalDetailReferred->depAmountRpt !!}</td>
            <td>{!! $assetDisposalDetailReferred->netBookValueRpt !!}</td>
            <td>{!! $assetDisposalDetailReferred->COSTGLCODESystemID !!}</td>
            <td>{!! $assetDisposalDetailReferred->COSTGLCODE !!}</td>
            <td>{!! $assetDisposalDetailReferred->ACCDEPGLCODESystemID !!}</td>
            <td>{!! $assetDisposalDetailReferred->ACCDEPGLCODE !!}</td>
            <td>{!! $assetDisposalDetailReferred->DISPOGLCODESystemID !!}</td>
            <td>{!! $assetDisposalDetailReferred->DISPOGLCODE !!}</td>
            <td>{!! $assetDisposalDetailReferred->timesReferred !!}</td>
            <td>{!! $assetDisposalDetailReferred->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['assetDisposalDetailReferreds.destroy', $assetDisposalDetailReferred->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('assetDisposalDetailReferreds.show', [$assetDisposalDetailReferred->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('assetDisposalDetailReferreds.edit', [$assetDisposalDetailReferred->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>