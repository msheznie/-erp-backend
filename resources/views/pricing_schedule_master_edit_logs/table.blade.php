<div class="table-responsive">
    <table class="table" id="pricingScheduleMasterEditLogs-table">
        <thead>
            <tr>
                <th>Company Id</th>
        <th>Created By</th>
        <th>Items Mandatory</th>
        <th>Modify Type</th>
        <th>Price Bid Format Id</th>
        <th>Schedule Mandatory</th>
        <th>Scheduler Name</th>
        <th>Status</th>
        <th>Tender Edit Version Id</th>
        <th>Tender Id</th>
        <th>Updated By</th>
                <th colspan="3">Action</th>
            </tr>
        </thead>
        <tbody>
        @foreach($pricingScheduleMasterEditLogs as $pricingScheduleMasterEditLog)
            <tr>
                <td>{{ $pricingScheduleMasterEditLog->company_id }}</td>
            <td>{{ $pricingScheduleMasterEditLog->created_by }}</td>
            <td>{{ $pricingScheduleMasterEditLog->items_mandatory }}</td>
            <td>{{ $pricingScheduleMasterEditLog->modify_type }}</td>
            <td>{{ $pricingScheduleMasterEditLog->price_bid_format_id }}</td>
            <td>{{ $pricingScheduleMasterEditLog->schedule_mandatory }}</td>
            <td>{{ $pricingScheduleMasterEditLog->scheduler_name }}</td>
            <td>{{ $pricingScheduleMasterEditLog->status }}</td>
            <td>{{ $pricingScheduleMasterEditLog->tender_edit_version_id }}</td>
            <td>{{ $pricingScheduleMasterEditLog->tender_id }}</td>
            <td>{{ $pricingScheduleMasterEditLog->updated_by }}</td>
                <td>
                    {!! Form::open(['route' => ['pricingScheduleMasterEditLogs.destroy', $pricingScheduleMasterEditLog->id], 'method' => 'delete']) !!}
                    <div class='btn-group'>
                        <a href="{{ route('pricingScheduleMasterEditLogs.show', [$pricingScheduleMasterEditLog->id]) }}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                        <a href="{{ route('pricingScheduleMasterEditLogs.edit', [$pricingScheduleMasterEditLog->id]) }}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                        {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                    </div>
                    {!! Form::close() !!}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
