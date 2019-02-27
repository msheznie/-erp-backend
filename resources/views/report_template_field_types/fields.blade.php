<!-- Fieldtype Field -->
<div class="form-group col-sm-6">
    {!! Form::label('fieldType', 'Fieldtype:') !!}
    {!! Form::text('fieldType', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('reportTemplateFieldTypes.index') !!}" class="btn btn-default">Cancel</a>
</div>
