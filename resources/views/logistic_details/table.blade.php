<table class="table table-responsive" id="logisticDetails-table">
    <thead>
        <tr>
            <th>Logisticmasterid</th>
        <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Supplierid</th>
        <th>Poid</th>
        <th>Podetailid</th>
        <th>Itemcodesystem</th>
        <th>Itemprimarycode</th>
        <th>Itemdescription</th>
        <th>Partno</th>
        <th>Itemuom</th>
        <th>Itempoqtry</th>
        <th>Itemshippingqty</th>
        <th>Podeliverywarehouslocation</th>
        <th>Grvstatus</th>
        <th>Grvsystemcode</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($logisticDetails as $logisticDetails)
        <tr>
            <td>{!! $logisticDetails->logisticMasterID !!}</td>
            <td>{!! $logisticDetails->companySystemID !!}</td>
            <td>{!! $logisticDetails->companyID !!}</td>
            <td>{!! $logisticDetails->supplierID !!}</td>
            <td>{!! $logisticDetails->POid !!}</td>
            <td>{!! $logisticDetails->POdetailID !!}</td>
            <td>{!! $logisticDetails->itemcodeSystem !!}</td>
            <td>{!! $logisticDetails->itemPrimaryCode !!}</td>
            <td>{!! $logisticDetails->itemDescription !!}</td>
            <td>{!! $logisticDetails->partNo !!}</td>
            <td>{!! $logisticDetails->itemUOM !!}</td>
            <td>{!! $logisticDetails->itemPOQtry !!}</td>
            <td>{!! $logisticDetails->itemShippingQty !!}</td>
            <td>{!! $logisticDetails->POdeliveryWarehousLocation !!}</td>
            <td>{!! $logisticDetails->GRVStatus !!}</td>
            <td>{!! $logisticDetails->GRVsystemCode !!}</td>
            <td>{!! $logisticDetails->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['logisticDetails.destroy', $logisticDetails->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('logisticDetails.show', [$logisticDetails->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('logisticDetails.edit', [$logisticDetails->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>