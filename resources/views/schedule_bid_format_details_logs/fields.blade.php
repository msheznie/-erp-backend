<!-- Bid Format Detail Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('bid_format_detail_id', 'Bid Format Detail Id:') !!}
    {!! Form::number('bid_format_detail_id', null, ['class' => 'form-control']) !!}
</div>

<!-- Bid Master Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('bid_master_id', 'Bid Master Id:') !!}
    {!! Form::number('bid_master_id', null, ['class' => 'form-control']) !!}
</div>

<!-- Company Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('company_id', 'Company Id:') !!}
    {!! Form::number('company_id', null, ['class' => 'form-control']) !!}
</div>

<!-- Master Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('master_id', 'Master Id:') !!}
    {!! Form::number('master_id', null, ['class' => 'form-control']) !!}
</div>

<!-- Modify Type Field -->
<div class="form-group col-sm-6">
    {!! Form::label('modify_type', 'Modify Type:') !!}
    {!! Form::number('modify_type', null, ['class' => 'form-control']) !!}
</div>

<!-- Red Log Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('red_log_id', 'Red Log Id:') !!}
    {!! Form::number('red_log_id', null, ['class' => 'form-control']) !!}
</div>

<!-- Schedule Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('schedule_id', 'Schedule Id:') !!}
    {!! Form::number('schedule_id', null, ['class' => 'form-control']) !!}
</div>

<!-- Tender Edit Version Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('tender_edit_version_id', 'Tender Edit Version Id:') !!}
    {!! Form::number('tender_edit_version_id', null, ['class' => 'form-control']) !!}
</div>

<!-- Value Field -->
<div class="form-group col-sm-6">
    {!! Form::label('value', 'Value:') !!}
    {!! Form::text('value', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{{ route('scheduleBidFormatDetailsLogs.index') }}" class="btn btn-default">Cancel</a>
</div>
