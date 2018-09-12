<table class="table table-responsive" id="logisticShippingModes-table">
    <thead>
        <tr>
            <th>Modeshippingdescription</th>
        <th>Createduserid</th>
        <th>Createdpcid</th>
        <th>Createddatetime</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($logisticShippingModes as $logisticShippingMode)
        <tr>
            <td>{!! $logisticShippingMode->modeShippingDescription !!}</td>
            <td>{!! $logisticShippingMode->createdUserID !!}</td>
            <td>{!! $logisticShippingMode->createdPCID !!}</td>
            <td>{!! $logisticShippingMode->createdDateTime !!}</td>
            <td>{!! $logisticShippingMode->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['logisticShippingModes.destroy', $logisticShippingMode->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('logisticShippingModes.show', [$logisticShippingMode->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('logisticShippingModes.edit', [$logisticShippingMode->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>