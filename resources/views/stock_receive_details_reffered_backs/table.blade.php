<table class="table table-responsive" id="stockReceiveDetailsRefferedBacks-table">
    <thead>
        <tr>
            <th>Stockreceivedetailsid</th>
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
        <th>Financeglcodebbssystemid</th>
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
    @foreach($stockReceiveDetailsRefferedBacks as $stockReceiveDetailsRefferedBack)
        <tr>
            <td>{!! $stockReceiveDetailsRefferedBack->stockReceiveDetailsID !!}</td>
            <td>{!! $stockReceiveDetailsRefferedBack->stockReceiveAutoID !!}</td>
            <td>{!! $stockReceiveDetailsRefferedBack->stockReceiveCode !!}</td>
            <td>{!! $stockReceiveDetailsRefferedBack->stockTransferAutoID !!}</td>
            <td>{!! $stockReceiveDetailsRefferedBack->stockTransferCode !!}</td>
            <td>{!! $stockReceiveDetailsRefferedBack->stockTransferDate !!}</td>
            <td>{!! $stockReceiveDetailsRefferedBack->itemCodeSystem !!}</td>
            <td>{!! $stockReceiveDetailsRefferedBack->itemPrimaryCode !!}</td>
            <td>{!! $stockReceiveDetailsRefferedBack->itemDescription !!}</td>
            <td>{!! $stockReceiveDetailsRefferedBack->unitOfMeasure !!}</td>
            <td>{!! $stockReceiveDetailsRefferedBack->itemFinanceCategoryID !!}</td>
            <td>{!! $stockReceiveDetailsRefferedBack->itemFinanceCategorySubID !!}</td>
            <td>{!! $stockReceiveDetailsRefferedBack->financeGLcodebBS !!}</td>
            <td>{!! $stockReceiveDetailsRefferedBack->financeGLcodebBSSystemID !!}</td>
            <td>{!! $stockReceiveDetailsRefferedBack->localCurrencyID !!}</td>
            <td>{!! $stockReceiveDetailsRefferedBack->unitCostLocal !!}</td>
            <td>{!! $stockReceiveDetailsRefferedBack->reportingCurrencyID !!}</td>
            <td>{!! $stockReceiveDetailsRefferedBack->unitCostRpt !!}</td>
            <td>{!! $stockReceiveDetailsRefferedBack->qty !!}</td>
            <td>{!! $stockReceiveDetailsRefferedBack->comments !!}</td>
            <td>{!! $stockReceiveDetailsRefferedBack->timesReferred !!}</td>
            <td>{!! $stockReceiveDetailsRefferedBack->timeStamp !!}</td>
            <td>
                {!! Form::open(['route' => ['stockReceiveDetailsRefferedBacks.destroy', $stockReceiveDetailsRefferedBack->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('stockReceiveDetailsRefferedBacks.show', [$stockReceiveDetailsRefferedBack->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('stockReceiveDetailsRefferedBacks.edit', [$stockReceiveDetailsRefferedBack->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>