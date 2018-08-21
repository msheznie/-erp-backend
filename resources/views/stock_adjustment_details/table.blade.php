<table class="table table-responsive" id="stockAdjustmentDetails-table">
    <thead>
        <tr>
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
        <th>Currenctstockqty</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($stockAdjustmentDetails as $stockAdjustmentDetails)
        <tr>
            <td>{!! $stockAdjustmentDetails->stockAdjustmentAutoID !!}</td>
            <td>{!! $stockAdjustmentDetails->stockAdjustmentAutoIDCode !!}</td>
            <td>{!! $stockAdjustmentDetails->itemCodeSystem !!}</td>
            <td>{!! $stockAdjustmentDetails->itemPrimaryCode !!}</td>
            <td>{!! $stockAdjustmentDetails->itemDescription !!}</td>
            <td>{!! $stockAdjustmentDetails->itemUnitOfMeasure !!}</td>
            <td>{!! $stockAdjustmentDetails->partNumber !!}</td>
            <td>{!! $stockAdjustmentDetails->itemFinanceCategoryID !!}</td>
            <td>{!! $stockAdjustmentDetails->itemFinanceCategorySubID !!}</td>
            <td>{!! $stockAdjustmentDetails->financeGLcodebBSSystemID !!}</td>
            <td>{!! $stockAdjustmentDetails->financeGLcodebBS !!}</td>
            <td>{!! $stockAdjustmentDetails->financeGLcodePLSystemID !!}</td>
            <td>{!! $stockAdjustmentDetails->financeGLcodePL !!}</td>
            <td>{!! $stockAdjustmentDetails->includePLForGRVYN !!}</td>
            <td>{!! $stockAdjustmentDetails->noQty !!}</td>
            <td>{!! $stockAdjustmentDetails->comments !!}</td>
            <td>{!! $stockAdjustmentDetails->currentWacLocalCurrencyID !!}</td>
            <td>{!! $stockAdjustmentDetails->currentWaclocal !!}</td>
            <td>{!! $stockAdjustmentDetails->currentWacRptCurrencyID !!}</td>
            <td>{!! $stockAdjustmentDetails->currentWacRpt !!}</td>
            <td>{!! $stockAdjustmentDetails->wacAdjLocal !!}</td>
            <td>{!! $stockAdjustmentDetails->wacAdjRptER !!}</td>
            <td>{!! $stockAdjustmentDetails->wacAdjRpt !!}</td>
            <td>{!! $stockAdjustmentDetails->currenctStockQty !!}</td>
            <td>{!! $stockAdjustmentDetails->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['stockAdjustmentDetails.destroy', $stockAdjustmentDetails->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('stockAdjustmentDetails.show', [$stockAdjustmentDetails->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('stockAdjustmentDetails.edit', [$stockAdjustmentDetails->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>