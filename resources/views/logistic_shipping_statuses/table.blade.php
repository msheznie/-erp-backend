<table class="table table-responsive" id="logisticShippingStatuses-table">
    <thead>
        <tr>
            <th>Logisticmasterid</th>
        <th>Shippingstatusid</th>
        <th>Statusdate</th>
        <th>Statuscomment</th>
        <th>Createduserid</th>
        <th>Createdpcid</th>
        <th>Createddatetime</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($logisticShippingStatuses as $logisticShippingStatus)
        <tr>
            <td>{!! $logisticShippingStatus->logisticMasterID !!}</td>
            <td>{!! $logisticShippingStatus->shippingStatusID !!}</td>
            <td>{!! $logisticShippingStatus->statusDate !!}</td>
            <td>{!! $logisticShippingStatus->statusComment !!}</td>
            <td>{!! $logisticShippingStatus->createdUserID !!}</td>
            <td>{!! $logisticShippingStatus->createdPCID !!}</td>
            <td>{!! $logisticShippingStatus->createdDateTime !!}</td>
            <td>{!! $logisticShippingStatus->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['logisticShippingStatuses.destroy', $logisticShippingStatus->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('logisticShippingStatuses.show', [$logisticShippingStatus->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('logisticShippingStatuses.edit', [$logisticShippingStatus->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>