<table class="table table-responsive" id="stockAdjustmentDetailsRefferedBacks-table">
    <thead>
        <tr>
            <th>Stockadjustmentdetailsautoid</th>
        <th>Stockadjustmentautoid</th>
        <th>Stockadjustmentautoidcode</th>
        <th>Itemcodesystem</th>
        <th>Itemprimarycode</th>
        <th>Itemdescription</th>
        <th>Itemunitofmeasure</th>
        <th>Partnumber</th>
        <th>Itemfinancecategoryid</th>
        <th>Itemfinancecategorysubid</th>
        <th>Financeglcodebbssystemid</th>
        <th>Financeglcodebbs</th>
        <th>Financeglcodeplsystemid</th>
        <th>Financeglcodepl</th>
        <th>Includeplforgrvyn</th>
        <th>Noqty</th>
        <th>Comments</th>
        <th>Currentwaclocalcurrencyid</th>
        <th>Currentwaclocal</th>
        <th>Currentwacrptcurrencyid</th>
        <th>Currentwacrpt</th>
        <th>Wacadjlocal</th>
        <th>Wacadjrpter</th>
        <th>Wacadjrpt</th>
        <th>Wacadjlocaler</th>
        <th>Currenctstockqty</th>
        <th>Timesreferred</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($stockAdjustmentDetailsRefferedBacks as $stockAdjustmentDetailsRefferedBack)
        <tr>
            <td>{!! $stockAdjustmentDetailsRefferedBack->stockAdjustmentDetailsAutoID !!}</td>
            <td>{!! $stockAdjustmentDetailsRefferedBack->stockAdjustmentAutoID !!}</td>
            <td>{!! $stockAdjustmentDetailsRefferedBack->stockAdjustmentAutoIDCode !!}</td>
            <td>{!! $stockAdjustmentDetailsRefferedBack->itemCodeSystem !!}</td>
            <td>{!! $stockAdjustmentDetailsRefferedBack->itemPrimaryCode !!}</td>
            <td>{!! $stockAdjustmentDetailsRefferedBack->itemDescription !!}</td>
            <td>{!! $stockAdjustmentDetailsRefferedBack->itemUnitOfMeasure !!}</td>
            <td>{!! $stockAdjustmentDetailsRefferedBack->partNumber !!}</td>
            <td>{!! $stockAdjustmentDetailsRefferedBack->itemFinanceCategoryID !!}</td>
            <td>{!! $stockAdjustmentDetailsRefferedBack->itemFinanceCategorySubID !!}</td>
            <td>{!! $stockAdjustmentDetailsRefferedBack->financeGLcodebBSSystemID !!}</td>
            <td>{!! $stockAdjustmentDetailsRefferedBack->financeGLcodebBS !!}</td>
            <td>{!! $stockAdjustmentDetailsRefferedBack->financeGLcodePLSystemID !!}</td>
            <td>{!! $stockAdjustmentDetailsRefferedBack->financeGLcodePL !!}</td>
            <td>{!! $stockAdjustmentDetailsRefferedBack->includePLForGRVYN !!}</td>
            <td>{!! $stockAdjustmentDetailsRefferedBack->noQty !!}</td>
            <td>{!! $stockAdjustmentDetailsRefferedBack->comments !!}</td>
            <td>{!! $stockAdjustmentDetailsRefferedBack->currentWacLocalCurrencyID !!}</td>
            <td>{!! $stockAdjustmentDetailsRefferedBack->currentWaclocal !!}</td>
            <td>{!! $stockAdjustmentDetailsRefferedBack->currentWacRptCurrencyID !!}</td>
            <td>{!! $stockAdjustmentDetailsRefferedBack->currentWacRpt !!}</td>
            <td>{!! $stockAdjustmentDetailsRefferedBack->wacAdjLocal !!}</td>
            <td>{!! $stockAdjustmentDetailsRefferedBack->wacAdjRptER !!}</td>
            <td>{!! $stockAdjustmentDetailsRefferedBack->wacAdjRpt !!}</td>
            <td>{!! $stockAdjustmentDetailsRefferedBack->wacAdjLocalER !!}</td>
            <td>{!! $stockAdjustmentDetailsRefferedBack->currenctStockQty !!}</td>
            <td>{!! $stockAdjustmentDetailsRefferedBack->timesReferred !!}</td>
            <td>{!! $stockAdjustmentDetailsRefferedBack->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['stockAdjustmentDetailsRefferedBacks.destroy', $stockAdjustmentDetailsRefferedBack->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('stockAdjustmentDetailsRefferedBacks.show', [$stockAdjustmentDetailsRefferedBack->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('stockAdjustmentDetailsRefferedBacks.edit', [$stockAdjustmentDetailsRefferedBack->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>