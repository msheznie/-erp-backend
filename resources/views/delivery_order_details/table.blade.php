<div class="table-responsive">
    <table class="table" id="deliveryOrderDetails-table">
        <thead>
            <tr>
                <th>Deliveryorderid</th>
        <th>Companysystemid</th>
        <th>Documentsystemid</th>
        <th>Itemcodesystem</th>
        <th>Itemprimarycode</th>
        <th>Itemdescription</th>
        <th>Itemunitofmeasure</th>
        <th>Unitofmeasureissued</th>
        <th>Convertionmeasureval</th>
        <th>Qtyissued</th>
        <th>Qtyissueddefaultmeasure</th>
        <th>Currentstockqty</th>
        <th>Currentwarehousestockqty</th>
        <th>Currentstockqtyindamagereturn</th>
        <th>Wacvaluelocal</th>
        <th>Wacvaluereporting</th>
        <th>Unittransactionamount</th>
        <th>Discountpercentage</th>
        <th>Discountamount</th>
        <th>Transactioncurrencyid</th>
        <th>Transactioncurrencyer</th>
        <th>Transactionamount</th>
        <th>Companylocalcurrencyid</th>
        <th>Companylocalcurrencyer</th>
        <th>Companylocalamount</th>
        <th>Companyreportingcurrencyid</th>
        <th>Companyreportingcurrencyer</th>
        <th>Companyreportingamount</th>
        <th>Timestamp</th>
                <th colspan="3">Action</th>
            </tr>
        </thead>
        <tbody>
        @foreach($deliveryOrderDetails as $deliveryOrderDetail)
            <tr>
                <td>{{ $deliveryOrderDetail->deliveryOrderID }}</td>
            <td>{{ $deliveryOrderDetail->companySystemID }}</td>
            <td>{{ $deliveryOrderDetail->documentSystemID }}</td>
            <td>{{ $deliveryOrderDetail->itemCodeSystem }}</td>
            <td>{{ $deliveryOrderDetail->itemPrimaryCode }}</td>
            <td>{{ $deliveryOrderDetail->itemDescription }}</td>
            <td>{{ $deliveryOrderDetail->itemUnitOfMeasure }}</td>
            <td>{{ $deliveryOrderDetail->unitOfMeasureIssued }}</td>
            <td>{{ $deliveryOrderDetail->convertionMeasureVal }}</td>
            <td>{{ $deliveryOrderDetail->qtyIssued }}</td>
            <td>{{ $deliveryOrderDetail->qtyIssuedDefaultMeasure }}</td>
            <td>{{ $deliveryOrderDetail->currentStockQty }}</td>
            <td>{{ $deliveryOrderDetail->currentWareHouseStockQty }}</td>
            <td>{{ $deliveryOrderDetail->currentStockQtyInDamageReturn }}</td>
            <td>{{ $deliveryOrderDetail->wacValueLocal }}</td>
            <td>{{ $deliveryOrderDetail->wacValueReporting }}</td>
            <td>{{ $deliveryOrderDetail->unitTransactionAmount }}</td>
            <td>{{ $deliveryOrderDetail->discountPercentage }}</td>
            <td>{{ $deliveryOrderDetail->discountAmount }}</td>
            <td>{{ $deliveryOrderDetail->transactionCurrencyID }}</td>
            <td>{{ $deliveryOrderDetail->transactionCurrencyER }}</td>
            <td>{{ $deliveryOrderDetail->transactionAmount }}</td>
            <td>{{ $deliveryOrderDetail->companyLocalCurrencyID }}</td>
            <td>{{ $deliveryOrderDetail->companyLocalCurrencyER }}</td>
            <td>{{ $deliveryOrderDetail->companyLocalAmount }}</td>
            <td>{{ $deliveryOrderDetail->companyReportingCurrencyID }}</td>
            <td>{{ $deliveryOrderDetail->companyReportingCurrencyER }}</td>
            <td>{{ $deliveryOrderDetail->companyReportingAmount }}</td>
            <td>{{ $deliveryOrderDetail->timestamp }}</td>
                <td>
                    {!! Form::open(['route' => ['deliveryOrderDetails.destroy', $deliveryOrderDetail->id], 'method' => 'delete']) !!}
                    <div class='btn-group'>
                        <a href="{{ route('deliveryOrderDetails.show', [$deliveryOrderDetail->id]) }}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                        <a href="{{ route('deliveryOrderDetails.edit', [$deliveryOrderDetail->id]) }}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                        {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                    </div>
                    {!! Form::close() !!}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
