<!-- Attachment Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('attachment_id', 'Attachment Id:') !!}
    {!! Form::number('attachment_id', null, ['class' => 'form-control']) !!}
</div>

<!-- Circular Name Field -->
<div class="form-group col-sm-6">
    {!! Form::label('circular_name', 'Circular Name:') !!}
    {!! Form::text('circular_name', null, ['class' => 'form-control']) !!}
</div>

<!-- Company Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('company_id', 'Company Id:') !!}
    {!! Form::number('company_id', null, ['class' => 'form-control']) !!}
</div>

<!-- Description Field -->
<div class="form-group col-sm-6">
    {!! Form::label('description', 'Description:') !!}
    {!! Form::text('description', null, ['class' => 'form-control']) !!}
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

<!-- Ref Log Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('ref_log_id', 'Ref Log Id:') !!}
    {!! Form::number('ref_log_id', null, ['class' => 'form-control']) !!}
</div>

<!-- Status Field -->
<div class="form-group col-sm-6">
    {!! Form::label('status', 'Status:') !!}
    {!! Form::number('status', null, ['class' => 'form-control']) !!}
</div>

<!-- Tender Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('tender_id', 'Tender Id:') !!}
    {!! Form::number('tender_id', null, ['class' => 'form-control']) !!}
</div>

<!-- Vesion Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('vesion_id', 'Vesion Id:') !!}
    {!! Form::number('vesion_id', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{{ route('tenderCircularsEditLogs.index') }}" class="btn btn-default">Cancel</a>
</div>
