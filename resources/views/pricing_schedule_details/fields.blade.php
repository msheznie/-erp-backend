<!-- Bid Format Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('bid_format_id', 'Bid Format Id:') !!}
    {!! Form::number('bid_format_id', null, ['class' => 'form-control']) !!}
</div>

<!-- Boq Applicable Field -->
<div class="form-group col-sm-6">
    {!! Form::label('boq_applicable', 'Boq Applicable:') !!}
    {!! Form::number('boq_applicable', null, ['class' => 'form-control']) !!}
</div>

<!-- Created By Field -->
<div class="form-group col-sm-6">
    {!! Form::label('created_by', 'Created By:') !!}
    {!! Form::number('created_by', null, ['class' => 'form-control']) !!}
</div>

<!-- Field Type Field -->
<div class="form-group col-sm-6">
    {!! Form::label('field_type', 'Field Type:') !!}
    {!! Form::number('field_type', null, ['class' => 'form-control']) !!}
</div>

<!-- Formula String Field -->
<div class="form-group col-sm-12 col-lg-12">
    {!! Form::label('formula_string', 'Formula String:') !!}
    {!! Form::textarea('formula_string', null, ['class' => 'form-control']) !!}
</div>

<!-- Is Disabled Field -->
<div class="form-group col-sm-6">
    {!! Form::label('is_disabled', 'Is Disabled:') !!}
    {!! Form::number('is_disabled', null, ['class' => 'form-control']) !!}
</div>

<!-- Label Field -->
<div class="form-group col-sm-6">
    {!! Form::label('label', 'Label:') !!}
    {!! Form::text('label', null, ['class' => 'form-control']) !!}
</div>

<!-- Pricing Schedule Master Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('pricing_schedule_master_id', 'Pricing Schedule Master Id:') !!}
    {!! Form::number('pricing_schedule_master_id', null, ['class' => 'form-control']) !!}
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
    <a href="{{ route('pricingScheduleDetails.index') }}" class="btn btn-default">Cancel</a>
</div>
