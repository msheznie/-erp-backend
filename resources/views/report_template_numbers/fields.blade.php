<!-- Value Field -->
<div class="form-group col-sm-6">
    {!! Form::label('value', 'Value:') !!}
    {!! Form::number('value', null, ['class' => 'form-control']) !!}
</div>

<!-- Timesstamp Field -->
<div class="form-group col-sm-6">
    {!! Form::label('timesStamp', 'Timesstamp:') !!}
    {!! Form::date('timesStamp', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('reportTemplateNumbers.index') !!}" class="btn btn-default">Cancel</a>
</div>
