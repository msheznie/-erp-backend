<table class="table table-responsive" id="inventoryReclassificationDetails-table">
    <thead>
        <tr>
            <th>Inventoryreclassificationid</th>
        <th>Itemsystemcode</th>
        <th>Itemprimarycode</th>
        <th>Itemdescription</th>
        <th>Unitofmeasure</th>
        <th>Itemfinancecategoryid</th>
        <th>Itemfinancecategorysubid</th>
        <th>Financeglcodebbssystemid</th>
        <th>Financeglcodebbs</th>
        <th>Financeglcodeplsystemid</th>
        <th>Financeglcodepl</th>
        <th>Includeplforgrvyn</th>
        <th>Currentstockqty</th>
        <th>Unitcostlocal</th>
        <th>Unitcostrpt</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($inventoryReclassificationDetails as $inventoryReclassificationDetail)
        <tr>
            <td>{!! $inventoryReclassificationDetail->inventoryreclassificationID !!}</td>
            <td>{!! $inventoryReclassificationDetail->itemSystemCode !!}</td>
            <td>{!! $inventoryReclassificationDetail->itemPrimaryCode !!}</td>
            <td>{!! $inventoryReclassificationDetail->itemDescription !!}</td>
            <td>{!! $inventoryReclassificationDetail->unitOfMeasure !!}</td>
            <td>{!! $inventoryReclassificationDetail->itemFinanceCategoryID !!}</td>
            <td>{!! $inventoryReclassificationDetail->itemFinanceCategorySubID !!}</td>
            <td>{!! $inventoryReclassificationDetail->financeGLcodebBSSystemID !!}</td>
            <td>{!! $inventoryReclassificationDetail->financeGLcodebBS !!}</td>
            <td>{!! $inventoryReclassificationDetail->financeGLcodePLSystemID !!}</td>
            <td>{!! $inventoryReclassificationDetail->financeGLcodePL !!}</td>
            <td>{!! $inventoryReclassificationDetail->includePLForGRVYN !!}</td>
            <td>{!! $inventoryReclassificationDetail->currentStockQty !!}</td>
            <td>{!! $inventoryReclassificationDetail->unitCostLocal !!}</td>
            <td>{!! $inventoryReclassificationDetail->unitCostRpt !!}</td>
            <td>{!! $inventoryReclassificationDetail->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['inventoryReclassificationDetails.destroy', $inventoryReclassificationDetail->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('inventoryReclassificationDetails.show', [$inventoryReclassificationDetail->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('inventoryReclassificationDetails.edit', [$inventoryReclassificationDetail->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>