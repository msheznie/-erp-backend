<!-- Suppliercontactdescription Field -->
<div class="form-group col-sm-6">
    {!! Form::label('supplierContactDescription', 'Suppliercontactdescription:') !!}
    {!! Form::text('supplierContactDescription', null, ['class' => 'form-control']) !!}
</div>

<!-- Timestamp Field -->
<div class="form-group col-sm-6">
    {!! Form::label('timestamp', 'Timestamp:') !!}
    {!! Form::date('timestamp', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('supplierContactTypes.index') !!}" class="btn btn-default">Cancel</a>
</div>
