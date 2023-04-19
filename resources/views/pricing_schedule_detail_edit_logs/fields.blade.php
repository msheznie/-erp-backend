<!-- Bid Format Detail Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('bid_format_detail_id', 'Bid Format Detail Id:') !!}
    {!! Form::number('bid_format_detail_id', null, ['class' => 'form-control']) !!}
</div>

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

<!-- Deleted By Field -->
<div class="form-group col-sm-6">
    {!! Form::label('deleted_by', 'Deleted By:') !!}
    {!! Form::number('deleted_by', null, ['class' => 'form-control']) !!}
</div>

<!-- Description Field -->
<div class="form-group col-sm-6">
    {!! Form::label('description', 'Description:') !!}
    {!! Form::text('description', null, ['class' => 'form-control']) !!}
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

<!-- Modify Type Field -->
<div class="form-group col-sm-6">
    {!! Form::label('modify_type', 'Modify Type:') !!}
    {!! Form::number('modify_type', null, ['class' => 'form-control']) !!}
</div>

<!-- Pricing Schedule Master Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('pricing_schedule_master_id', 'Pricing Schedule Master Id:') !!}
    {!! Form::number('pricing_schedule_master_id', null, ['class' => 'form-control']) !!}
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

<!-- Tender Ranking Line Item Field -->
<div class="form-group col-sm-6">
    {!! Form::label('tender_ranking_line_item', 'Tender Ranking Line Item:') !!}
    {!! Form::number('tender_ranking_line_item', null, ['class' => 'form-control']) !!}
</div>

<!-- Updated By Field -->
<div class="form-group col-sm-6">
    {!! Form::label('updated_by', 'Updated By:') !!}
    {!! Form::number('updated_by', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{{ route('pricingScheduleDetailEditLogs.index') }}" class="btn btn-default">Cancel</a>
</div>
