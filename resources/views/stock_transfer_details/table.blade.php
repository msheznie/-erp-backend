<table class="table table-responsive" id="stockTransferDetails-table">
    <thead>
        <tr>
            <th>Stocktransferautoid</th>
        <th>Stocktransfercode</th>
        <th>Itemcodesystem</th>
        <th>Itemprimarycode</th>
        <th>Itemdescription</th>
        <th>Unitofmeasure</th>
        <th>Itemfinancecategoryid</th>
        <th>Itemfinancecategorysubid</th>
        <th>Financeglcodebbs</th>
        <th>Qty</th>
        <th>Currentstockqty</th>
        <th>Warehousestockqty</th>
        <th>Localcurrencyid</th>
        <th>Unitcostlocal</th>
        <th>Reportingcurrencyid</th>
        <th>Unitcostrpt</th>
        <th>Comments</th>
        <th>Addedtorecieved</th>
        <th>Stockrecieved</th>
        <th>Timesreferred</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($stockTransferDetails as $stockTransferDetails)
        <tr>
            <td>{!! $stockTransferDetails->stockTransferAutoID !!}</td>
            <td>{!! $stockTransferDetails->stockTransferCode !!}</td>
            <td>{!! $stockTransferDetails->itemCodeSystem !!}</td>
            <td>{!! $stockTransferDetails->itemPrimaryCode !!}</td>
            <td>{!! $stockTransferDetails->itemDescription !!}</td>
            <td>{!! $stockTransferDetails->unitOfMeasure !!}</td>
            <td>{!! $stockTransferDetails->itemFinanceCategoryID !!}</td>
            <td>{!! $stockTransferDetails->itemFinanceCategorySubID !!}</td>
            <td>{!! $stockTransferDetails->financeGLcodebBS !!}</td>
            <td>{!! $stockTransferDetails->qty !!}</td>
            <td>{!! $stockTransferDetails->currentStockQty !!}</td>
            <td>{!! $stockTransferDetails->warehouseStockQty !!}</td>
            <td>{!! $stockTransferDetails->localCurrencyID !!}</td>
            <td>{!! $stockTransferDetails->unitCostLocal !!}</td>
            <td>{!! $stockTransferDetails->reportingCurrencyID !!}</td>
            <td>{!! $stockTransferDetails->unitCostRpt !!}</td>
            <td>{!! $stockTransferDetails->comments !!}</td>
            <td>{!! $stockTransferDetails->addedToRecieved !!}</td>
            <td>{!! $stockTransferDetails->stockRecieved !!}</td>
            <td>{!! $stockTransferDetails->timesReferred !!}</td>
            <td>{!! $stockTransferDetails->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['stockTransferDetails.destroy', $stockTransferDetails->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('stockTransferDetails.show', [$stockTransferDetails->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('stockTransferDetails.edit', [$stockTransferDetails->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>