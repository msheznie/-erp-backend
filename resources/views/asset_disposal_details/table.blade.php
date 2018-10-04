<table class="table table-responsive" id="assetDisposalDetails-table">
    <thead>
        <tr>
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
        <th>Costglcode</th>
        <th>Accdepglcode</th>
        <th>Dispoglcode</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($assetDisposalDetails as $assetDisposalDetail)
        <tr>
            <td>{!! $assetDisposalDetail->assetdisposalMasterAutoID !!}</td>
            <td>{!! $assetDisposalDetail->companySystemID !!}</td>
            <td>{!! $assetDisposalDetail->companyID !!}</td>
            <td>{!! $assetDisposalDetail->serviceLineSystemID !!}</td>
            <td>{!! $assetDisposalDetail->serviceLineCode !!}</td>
            <td>{!! $assetDisposalDetail->itemCode !!}</td>
            <td>{!! $assetDisposalDetail->faID !!}</td>
            <td>{!! $assetDisposalDetail->faCode !!}</td>
            <td>{!! $assetDisposalDetail->faUnitSerialNo !!}</td>
            <td>{!! $assetDisposalDetail->assetDescription !!}</td>
            <td>{!! $assetDisposalDetail->COSTUNIT !!}</td>
            <td>{!! $assetDisposalDetail->costUnitRpt !!}</td>
            <td>{!! $assetDisposalDetail->netBookValueLocal !!}</td>
            <td>{!! $assetDisposalDetail->depAmountLocal !!}</td>
            <td>{!! $assetDisposalDetail->depAmountRpt !!}</td>
            <td>{!! $assetDisposalDetail->netBookValueRpt !!}</td>
            <td>{!! $assetDisposalDetail->COSTGLCODE !!}</td>
            <td>{!! $assetDisposalDetail->ACCDEPGLCODE !!}</td>
            <td>{!! $assetDisposalDetail->DISPOGLCODE !!}</td>
            <td>{!! $assetDisposalDetail->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['assetDisposalDetails.destroy', $assetDisposalDetail->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('assetDisposalDetails.show', [$assetDisposalDetail->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('assetDisposalDetails.edit', [$assetDisposalDetail->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>