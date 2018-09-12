<table class="table table-responsive" id="logisticStatuses-table">
    <thead>
        <tr>
            <th>Statusdescriptions</th>
        <th>Createduserid</th>
        <th>Createddatetime</th>
        <th>Createdpcid</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($logisticStatuses as $logisticStatus)
        <tr>
            <td>{!! $logisticStatus->statusDescriptions !!}</td>
            <td>{!! $logisticStatus->createdUserID !!}</td>
            <td>{!! $logisticStatus->createdDateTime !!}</td>
            <td>{!! $logisticStatus->createdPCID !!}</td>
            <td>{!! $logisticStatus->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['logisticStatuses.destroy', $logisticStatus->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('logisticStatuses.show', [$logisticStatus->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('logisticStatuses.edit', [$logisticStatus->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>