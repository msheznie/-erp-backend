<table class="table table-responsive" id="stockTransferDetailsRefferedBacks-table">
    <thead>
        <tr>
            <th>Stocktransferdetailsid</th>
        <th>Stocktransferautoid</th>
        <th>Stocktransfercode</th>
        <th>Itemcodesystem</th>
        <th>Itemprimarycode</th>
        <th>Itemdescription</th>
        <th>Unitofmeasure</th>
        <th>Itemfinancecategoryid</th>
        <th>Itemfinancecategorysubid</th>
        <th>Financeglcodebbs</th>
        <th>Financeglcodebbssystemid</th>
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
        <th>Createdusergroup</th>
        <th>Createdpcid</th>
        <th>Createduserid</th>
        <th>Modifiedpc</th>
        <th>Modifieduser</th>
        <th>Createddatetime</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($stockTransferDetailsRefferedBacks as $stockTransferDetailsRefferedBack)
        <tr>
            <td>{!! $stockTransferDetailsRefferedBack->stockTransferDetailsID !!}</td>
            <td>{!! $stockTransferDetailsRefferedBack->stockTransferAutoID !!}</td>
            <td>{!! $stockTransferDetailsRefferedBack->stockTransferCode !!}</td>
            <td>{!! $stockTransferDetailsRefferedBack->itemCodeSystem !!}</td>
            <td>{!! $stockTransferDetailsRefferedBack->itemPrimaryCode !!}</td>
            <td>{!! $stockTransferDetailsRefferedBack->itemDescription !!}</td>
            <td>{!! $stockTransferDetailsRefferedBack->unitOfMeasure !!}</td>
            <td>{!! $stockTransferDetailsRefferedBack->itemFinanceCategoryID !!}</td>
            <td>{!! $stockTransferDetailsRefferedBack->itemFinanceCategorySubID !!}</td>
            <td>{!! $stockTransferDetailsRefferedBack->financeGLcodebBS !!}</td>
            <td>{!! $stockTransferDetailsRefferedBack->financeGLcodebBSSystemID !!}</td>
            <td>{!! $stockTransferDetailsRefferedBack->qty !!}</td>
            <td>{!! $stockTransferDetailsRefferedBack->currentStockQty !!}</td>
            <td>{!! $stockTransferDetailsRefferedBack->warehouseStockQty !!}</td>
            <td>{!! $stockTransferDetailsRefferedBack->localCurrencyID !!}</td>
            <td>{!! $stockTransferDetailsRefferedBack->unitCostLocal !!}</td>
            <td>{!! $stockTransferDetailsRefferedBack->reportingCurrencyID !!}</td>
            <td>{!! $stockTransferDetailsRefferedBack->unitCostRpt !!}</td>
            <td>{!! $stockTransferDetailsRefferedBack->comments !!}</td>
            <td>{!! $stockTransferDetailsRefferedBack->addedToRecieved !!}</td>
            <td>{!! $stockTransferDetailsRefferedBack->stockRecieved !!}</td>
            <td>{!! $stockTransferDetailsRefferedBack->timesReferred !!}</td>
            <td>{!! $stockTransferDetailsRefferedBack->createdUserGroup !!}</td>
            <td>{!! $stockTransferDetailsRefferedBack->createdPcID !!}</td>
            <td>{!! $stockTransferDetailsRefferedBack->createdUserID !!}</td>
            <td>{!! $stockTransferDetailsRefferedBack->modifiedPc !!}</td>
            <td>{!! $stockTransferDetailsRefferedBack->modifiedUser !!}</td>
            <td>{!! $stockTransferDetailsRefferedBack->createdDateTime !!}</td>
            <td>{!! $stockTransferDetailsRefferedBack->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['stockTransferDetailsRefferedBacks.destroy', $stockTransferDetailsRefferedBack->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('stockTransferDetailsRefferedBacks.show', [$stockTransferDetailsRefferedBack->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('stockTransferDetailsRefferedBacks.edit', [$stockTransferDetailsRefferedBack->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>