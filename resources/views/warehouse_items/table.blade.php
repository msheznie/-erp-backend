<table class="table table-responsive" id="warehouseItems-table">
    <thead>
        <tr>
            <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Warehousesystemcode</th>
        <th>Itemsystemcode</th>
        <th>Itemprimarycode</th>
        <th>Itemdescription</th>
        <th>Unitofmeasure</th>
        <th>Stockqty</th>
        <th>Maximunqty</th>
        <th>Minimumqty</th>
        <th>Rolquantity</th>
        <th>Wacvaluelocalcurrencyid</th>
        <th>Wacvaluelocal</th>
        <th>Wacvaluereportingcurrencyid</th>
        <th>Wacvaluereporting</th>
        <th>Totalqty</th>
        <th>Totalvaluelocal</th>
        <th>Totalvaluerpt</th>
        <th>Financecategorymaster</th>
        <th>Financecategorysub</th>
        <th>Binnumber</th>
        <th>Todelete</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($warehouseItems as $warehouseItems)
        <tr>
            <td>{!! $warehouseItems->companySystemID !!}</td>
            <td>{!! $warehouseItems->companyID !!}</td>
            <td>{!! $warehouseItems->warehouseSystemCode !!}</td>
            <td>{!! $warehouseItems->itemSystemCode !!}</td>
            <td>{!! $warehouseItems->itemPrimaryCode !!}</td>
            <td>{!! $warehouseItems->itemDescription !!}</td>
            <td>{!! $warehouseItems->unitOfMeasure !!}</td>
            <td>{!! $warehouseItems->stockQty !!}</td>
            <td>{!! $warehouseItems->maximunQty !!}</td>
            <td>{!! $warehouseItems->minimumQty !!}</td>
            <td>{!! $warehouseItems->rolQuantity !!}</td>
            <td>{!! $warehouseItems->wacValueLocalCurrencyID !!}</td>
            <td>{!! $warehouseItems->wacValueLocal !!}</td>
            <td>{!! $warehouseItems->wacValueReportingCurrencyID !!}</td>
            <td>{!! $warehouseItems->wacValueReporting !!}</td>
            <td>{!! $warehouseItems->totalQty !!}</td>
            <td>{!! $warehouseItems->totalValueLocal !!}</td>
            <td>{!! $warehouseItems->totalValueRpt !!}</td>
            <td>{!! $warehouseItems->financeCategoryMaster !!}</td>
            <td>{!! $warehouseItems->financeCategorySub !!}</td>
            <td>{!! $warehouseItems->binNumber !!}</td>
            <td>{!! $warehouseItems->toDelete !!}</td>
            <td>{!! $warehouseItems->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['warehouseItems.destroy', $warehouseItems->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('warehouseItems.show', [$warehouseItems->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('warehouseItems.edit', [$warehouseItems->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>