<!-- Printtemplatename Field -->
<div class="form-group col-sm-6">
    {!! Form::label('printTemplateName', 'Printtemplatename:') !!}
    {!! Form::text('printTemplateName', null, ['class' => 'form-control']) !!}
</div>

<!-- Printtemplateblade Field -->
<div class="form-group col-sm-6">
    {!! Form::label('printTemplateBlade', 'Printtemplateblade:') !!}
    {!! Form::text('printTemplateBlade', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('erpPrintTemplateMasters.index') !!}" class="btn btn-default">Cancel</a>
</div>
