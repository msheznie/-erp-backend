<!-- Documentid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('documentID', 'Documentid:') !!}
    {!! Form::number('documentID', null, ['class' => 'form-control']) !!}
</div>

<!-- Companyid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('companyID', 'Companyid:') !!}
    {!! Form::number('companyID', null, ['class' => 'form-control']) !!}
</div>

<!-- Printtemplateid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('printTemplateID', 'Printtemplateid:') !!}
    {!! Form::number('printTemplateID', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('erpDocumentTemplates.index') !!}" class="btn btn-default">Cancel</a>
</div>
