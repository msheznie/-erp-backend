<table class="table table-responsive" id="itemAssigneds-table">
    <thead>
        <tr>
            <th>Itemcodesystem</th>
        <th>Itemprimarycode</th>
        <th>Secondaryitemcode</th>
        <th>Barcode</th>
        <th>Itemdescription</th>
        <th>Itemunitofmeasure</th>
        <th>Itemurl</th>
        <th>Companysystemid</th>
        <th>Companyid</th>
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
        <th>Categorysub1</th>
        <th>Categorysub2</th>
        <th>Categorysub3</th>
        <th>Categorysub4</th>
        <th>Categorysub5</th>
        <th>Isactive</th>
        <th>Isassigned</th>
        <th>Selectedforwarehouse</th>
        <th>Itemmovementcategory</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($itemAssigneds as $itemAssigned)
        <tr>
            <td>{!! $itemAssigned->itemCodeSystem !!}</td>
            <td>{!! $itemAssigned->itemPrimaryCode !!}</td>
            <td>{!! $itemAssigned->secondaryItemCode !!}</td>
            <td>{!! $itemAssigned->barcode !!}</td>
            <td>{!! $itemAssigned->itemDescription !!}</td>
            <td>{!! $itemAssigned->itemUnitOfMeasure !!}</td>
            <td>{!! $itemAssigned->itemUrl !!}</td>
            <td>{!! $itemAssigned->companySystemID !!}</td>
            <td>{!! $itemAssigned->companyID !!}</td>
            <td>{!! $itemAssigned->maximunQty !!}</td>
            <td>{!! $itemAssigned->minimumQty !!}</td>
            <td>{!! $itemAssigned->rolQuantity !!}</td>
            <td>{!! $itemAssigned->wacValueLocalCurrencyID !!}</td>
            <td>{!! $itemAssigned->wacValueLocal !!}</td>
            <td>{!! $itemAssigned->wacValueReportingCurrencyID !!}</td>
            <td>{!! $itemAssigned->wacValueReporting !!}</td>
            <td>{!! $itemAssigned->totalQty !!}</td>
            <td>{!! $itemAssigned->totalValueLocal !!}</td>
            <td>{!! $itemAssigned->totalValueRpt !!}</td>
            <td>{!! $itemAssigned->financeCategoryMaster !!}</td>
            <td>{!! $itemAssigned->financeCategorySub !!}</td>
            <td>{!! $itemAssigned->categorySub1 !!}</td>
            <td>{!! $itemAssigned->categorySub2 !!}</td>
            <td>{!! $itemAssigned->categorySub3 !!}</td>
            <td>{!! $itemAssigned->categorySub4 !!}</td>
            <td>{!! $itemAssigned->categorySub5 !!}</td>
            <td>{!! $itemAssigned->isActive !!}</td>
            <td>{!! $itemAssigned->isAssigned !!}</td>
            <td>{!! $itemAssigned->selectedForWarehouse !!}</td>
            <td>{!! $itemAssigned->itemMovementCategory !!}</td>
            <td>{!! $itemAssigned->timeStamp !!}</td>
            <td>
                {!! Form::open(['route' => ['itemAssigneds.destroy', $itemAssigned->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('itemAssigneds.show', [$itemAssigned->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('itemAssigneds.edit', [$itemAssigned->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>