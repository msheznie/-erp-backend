<!-- Attribute Field -->
<div class="form-group col-sm-6">
    {!! Form::label('attribute', 'Attribute:') !!}
    {!! Form::text('attribute', null, ['class' => 'form-control']) !!}
</div>

<!-- New Value Field -->
<div class="form-group col-sm-6">
    {!! Form::label('new_value', 'New Value:') !!}
    {!! Form::text('new_value', null, ['class' => 'form-control']) !!}
</div>

<!-- Old Value Field -->
<div class="form-group col-sm-6">
    {!! Form::label('old_value', 'Old Value:') !!}
    {!! Form::text('old_value', null, ['class' => 'form-control']) !!}
</div>

<!-- Tender Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('tender_id', 'Tender Id:') !!}
    {!! Form::number('tender_id', null, ['class' => 'form-control']) !!}
</div>

<!-- Version Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('version_id', 'Version Id:') !!}
    {!! Form::number('version_id', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{{ route('documentModifyRequestDetails.index') }}" class="btn btn-default">Cancel</a>
</div>
