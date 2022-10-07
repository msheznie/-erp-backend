<div class="table-responsive">
    <table class="table" id="pricingScheduleDetails-table">
        <thead>
            <tr>
                <th>Bid Format Id</th>
        <th>Boq Applicable</th>
        <th>Created By</th>
        <th>Field Type</th>
        <th>Formula String</th>
        <th>Is Disabled</th>
        <th>Label</th>
        <th>Pricing Schedule Master Id</th>
        <th>Tender Id</th>
        <th>Updated By</th>
                <th colspan="3">Action</th>
            </tr>
        </thead>
        <tbody>
        @foreach($pricingScheduleDetails as $pricingScheduleDetail)
            <tr>
                <td>{{ $pricingScheduleDetail->bid_format_id }}</td>
            <td>{{ $pricingScheduleDetail->boq_applicable }}</td>
            <td>{{ $pricingScheduleDetail->created_by }}</td>
            <td>{{ $pricingScheduleDetail->field_type }}</td>
            <td>{{ $pricingScheduleDetail->formula_string }}</td>
            <td>{{ $pricingScheduleDetail->is_disabled }}</td>
            <td>{{ $pricingScheduleDetail->label }}</td>
            <td>{{ $pricingScheduleDetail->pricing_schedule_master_id }}</td>
            <td>{{ $pricingScheduleDetail->tender_id }}</td>
            <td>{{ $pricingScheduleDetail->updated_by }}</td>
                <td>
                    {!! Form::open(['route' => ['pricingScheduleDetails.destroy', $pricingScheduleDetail->id], 'method' => 'delete']) !!}
                    <div class='btn-group'>
                        <a href="{{ route('pricingScheduleDetails.show', [$pricingScheduleDetail->id]) }}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                        <a href="{{ route('pricingScheduleDetails.edit', [$pricingScheduleDetail->id]) }}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                        {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                    </div>
                    {!! Form::close() !!}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
