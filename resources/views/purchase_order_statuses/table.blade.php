<table class="table table-responsive" id="purchaseOrderStatuses-table">
    <thead>
        <tr>
            <th>Purchaseorderid</th>
        <th>Purchaseordercode</th>
        <th>Pocategoryid</th>
        <th>Comments</th>
        <th>Updatedbyempsystemid</th>
        <th>Updatedbyempid</th>
        <th>Updatedbyempname</th>
        <th>Updateddate</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($purchaseOrderStatuses as $purchaseOrderStatus)
        <tr>
            <td>{!! $purchaseOrderStatus->purchaseOrderID !!}</td>
            <td>{!! $purchaseOrderStatus->purchaseOrderCode !!}</td>
            <td>{!! $purchaseOrderStatus->POCategoryID !!}</td>
            <td>{!! $purchaseOrderStatus->comments !!}</td>
            <td>{!! $purchaseOrderStatus->updatedByEmpSystemID !!}</td>
            <td>{!! $purchaseOrderStatus->updatedByEmpID !!}</td>
            <td>{!! $purchaseOrderStatus->updatedByEmpName !!}</td>
            <td>{!! $purchaseOrderStatus->updatedDate !!}</td>
            <td>{!! $purchaseOrderStatus->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['purchaseOrderStatuses.destroy', $purchaseOrderStatus->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('purchaseOrderStatuses.show', [$purchaseOrderStatus->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('purchaseOrderStatuses.edit', [$purchaseOrderStatus->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>