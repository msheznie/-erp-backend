<!-- Importancedescription Field -->
<div class="form-group col-sm-6">
    {!! Form::label('importanceDescription', 'Importancedescription:') !!}
    {!! Form::text('importanceDescription', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('supplierImportances.index') !!}" class="btn btn-default">Cancel</a>
</div>
