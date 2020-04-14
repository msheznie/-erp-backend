<!-- Reportcolumntemplateid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('reportColumnTemplateID', 'Reportcolumntemplateid:') !!}
    {!! Form::number('reportColumnTemplateID', null, ['class' => 'form-control']) !!}
</div>

<!-- Columnid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('columnID', 'Columnid:') !!}
    {!! Form::number('columnID', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('reportColumnTemplateDetails.index') !!}" class="btn btn-default">Cancel</a>
</div>
