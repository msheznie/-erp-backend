<div class="table-responsive">
    <table class="table" id="pricingScheduleDetailEditLogs-table">
        <thead>
            <tr>
                <th>Bid Format Detail Id</th>
        <th>Bid Format Id</th>
        <th>Boq Applicable</th>
        <th>Company Id</th>
        <th>Created By</th>
        <th>Deleted By</th>
        <th>Description</th>
        <th>Field Type</th>
        <th>Formula String</th>
        <th>Is Disabled</th>
        <th>Label</th>
        <th>Modify Type</th>
        <th>Pricing Schedule Master Id</th>
        <th>Tender Edit Version Id</th>
        <th>Tender Id</th>
        <th>Tender Ranking Line Item</th>
        <th>Updated By</th>
                <th colspan="3">Action</th>
            </tr>
        </thead>
        <tbody>
        @foreach($pricingScheduleDetailEditLogs as $pricingScheduleDetailEditLog)
            <tr>
                <td>{{ $pricingScheduleDetailEditLog->bid_format_detail_id }}</td>
            <td>{{ $pricingScheduleDetailEditLog->bid_format_id }}</td>
            <td>{{ $pricingScheduleDetailEditLog->boq_applicable }}</td>
            <td>{{ $pricingScheduleDetailEditLog->company_id }}</td>
            <td>{{ $pricingScheduleDetailEditLog->created_by }}</td>
            <td>{{ $pricingScheduleDetailEditLog->deleted_by }}</td>
            <td>{{ $pricingScheduleDetailEditLog->description }}</td>
            <td>{{ $pricingScheduleDetailEditLog->field_type }}</td>
            <td>{{ $pricingScheduleDetailEditLog->formula_string }}</td>
            <td>{{ $pricingScheduleDetailEditLog->is_disabled }}</td>
            <td>{{ $pricingScheduleDetailEditLog->label }}</td>
            <td>{{ $pricingScheduleDetailEditLog->modify_type }}</td>
            <td>{{ $pricingScheduleDetailEditLog->pricing_schedule_master_id }}</td>
            <td>{{ $pricingScheduleDetailEditLog->tender_edit_version_id }}</td>
            <td>{{ $pricingScheduleDetailEditLog->tender_id }}</td>
            <td>{{ $pricingScheduleDetailEditLog->tender_ranking_line_item }}</td>
            <td>{{ $pricingScheduleDetailEditLog->updated_by }}</td>
                <td>
                    {!! Form::open(['route' => ['pricingScheduleDetailEditLogs.destroy', $pricingScheduleDetailEditLog->id], 'method' => 'delete']) !!}
                    <div class='btn-group'>
                        <a href="{{ route('pricingScheduleDetailEditLogs.show', [$pricingScheduleDetailEditLog->id]) }}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                        <a href="{{ route('pricingScheduleDetailEditLogs.edit', [$pricingScheduleDetailEditLog->id]) }}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                        {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                    </div>
                    {!! Form::close() !!}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
