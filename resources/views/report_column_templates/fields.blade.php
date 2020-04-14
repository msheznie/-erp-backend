<!-- Templatename Field -->
<div class="form-group col-sm-6">
    {!! Form::label('templateName', 'Templatename:') !!}
    {!! Form::text('templateName', null, ['class' => 'form-control']) !!}
</div>

<!-- Templateimage Field -->
<div class="form-group col-sm-6">
    {!! Form::label('templateImage', 'Templateimage:') !!}
    {!! Form::text('templateImage', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('reportColumnTemplates.index') !!}" class="btn btn-default">Cancel</a>
</div>
