<!-- Company Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('company_id', 'Company Id:') !!}
    {!! Form::number('company_id', null, ['class' => 'form-control']) !!}
</div>

<!-- Created By Field -->
<div class="form-group col-sm-6">
    {!! Form::label('created_by', 'Created By:') !!}
    {!! Form::number('created_by', null, ['class' => 'form-control']) !!}
</div>

<!-- Items Mandatory Field -->
<div class="form-group col-sm-6">
    {!! Form::label('items_mandatory', 'Items Mandatory:') !!}
    {!! Form::number('items_mandatory', null, ['class' => 'form-control']) !!}
</div>

<!-- Modify Type Field -->
<div class="form-group col-sm-6">
    {!! Form::label('modify_type', 'Modify Type:') !!}
    {!! Form::number('modify_type', null, ['class' => 'form-control']) !!}
</div>

<!-- Price Bid Format Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('price_bid_format_id', 'Price Bid Format Id:') !!}
    {!! Form::number('price_bid_format_id', null, ['class' => 'form-control']) !!}
</div>

<!-- Schedule Mandatory Field -->
<div class="form-group col-sm-6">
    {!! Form::label('schedule_mandatory', 'Schedule Mandatory:') !!}
    {!! Form::number('schedule_mandatory', null, ['class' => 'form-control']) !!}
</div>

<!-- Scheduler Name Field -->
<div class="form-group col-sm-6">
    {!! Form::label('scheduler_name', 'Scheduler Name:') !!}
    {!! Form::text('scheduler_name', null, ['class' => 'form-control']) !!}
</div>

<!-- Status Field -->
<div class="form-group col-sm-6">
    {!! Form::label('status', 'Status:') !!}
    {!! Form::number('status', null, ['class' => 'form-control']) !!}
</div>

<!-- Tender Edit Version Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('tender_edit_version_id', 'Tender Edit Version Id:') !!}
    {!! Form::number('tender_edit_version_id', null, ['class' => 'form-control']) !!}
</div>

<!-- Tender Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('tender_id', 'Tender Id:') !!}
    {!! Form::number('tender_id', null, ['class' => 'form-control']) !!}
</div>

<!-- Updated By Field -->
<div class="form-group col-sm-6">
    {!! Form::label('updated_by', 'Updated By:') !!}
    {!! Form::number('updated_by', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{{ route('pricingScheduleMasterEditLogs.index') }}" class="btn btn-default">Cancel</a>
</div>
