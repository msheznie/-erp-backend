<table class="table table-responsive" id="stockReceiveDetails-table">
    <thead>
        <tr>
            <th>Stockreceiveautoid</th>
        <th>Stockreceivecode</th>
        <th>Stocktransferautoid</th>
        <th>Stocktransfercode</th>
        <th>Stocktransferdate</th>
        <th>Itemcodesystem</th>
        <th>Itemprimarycode</th>
        <th>Itemdescription</th>
        <th>Unitofmeasure</th>
        <th>Itemfinancecategoryid</th>
        <th>Itemfinancecategorysubid</th>
        <th>Financeglcodebbs</th>
        <th>Localcurrencyid</th>
        <th>Unitcostlocal</th>
        <th>Reportingcurrencyid</th>
        <th>Unitcostrpt</th>
        <th>Qty</th>
        <th>Comments</th>
        <th>Timesreferred</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($stockReceiveDetails as $stockReceiveDetails)
        <tr>
            <td>{!! $stockReceiveDetails->stockReceiveAutoID !!}</td>
            <td>{!! $stockReceiveDetails->stockReceiveCode !!}</td>
            <td>{!! $stockReceiveDetails->stockTransferAutoID !!}</td>
            <td>{!! $stockReceiveDetails->stockTransferCode !!}</td>
            <td>{!! $stockReceiveDetails->stockTransferDate !!}</td>
            <td>{!! $stockReceiveDetails->itemCodeSystem !!}</td>
            <td>{!! $stockReceiveDetails->itemPrimaryCode !!}</td>
            <td>{!! $stockReceiveDetails->itemDescription !!}</td>
            <td>{!! $stockReceiveDetails->unitOfMeasure !!}</td>
            <td>{!! $stockReceiveDetails->itemFinanceCategoryID !!}</td>
            <td>{!! $stockReceiveDetails->itemFinanceCategorySubID !!}</td>
            <td>{!! $stockReceiveDetails->financeGLcodebBS !!}</td>
            <td>{!! $stockReceiveDetails->localCurrencyID !!}</td>
            <td>{!! $stockReceiveDetails->unitCostLocal !!}</td>
            <td>{!! $stockReceiveDetails->reportingCurrencyID !!}</td>
            <td>{!! $stockReceiveDetails->unitCostRpt !!}</td>
            <td>{!! $stockReceiveDetails->qty !!}</td>
            <td>{!! $stockReceiveDetails->comments !!}</td>
            <td>{!! $stockReceiveDetails->timesReferred !!}</td>
            <td>{!! $stockReceiveDetails->timeStamp !!}</td>
            <td>
                {!! Form::open(['route' => ['stockReceiveDetails.destroy', $stockReceiveDetails->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('stockReceiveDetails.show', [$stockReceiveDetails->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('stockReceiveDetails.edit', [$stockReceiveDetails->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>