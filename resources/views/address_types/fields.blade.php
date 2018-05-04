<!-- Addresstypedescription Field -->
<div class="form-group col-sm-6">
    {!! Form::label('addressTypeDescription', 'Addresstypedescription:') !!}
    {!! Form::text('addressTypeDescription', null, ['class' => 'form-control']) !!}
</div>

<!-- Timestamp Field -->
<div class="form-group col-sm-6">
    {!! Form::label('timestamp', 'Timestamp:') !!}
    {!! Form::date('timestamp', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('addressTypes.index') !!}" class="btn btn-default">Cancel</a>
</div>
