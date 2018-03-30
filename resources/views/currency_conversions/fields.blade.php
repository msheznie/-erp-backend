<!-- Mastercurrencyid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('masterCurrencyID', 'Mastercurrencyid:') !!}
    {!! Form::number('masterCurrencyID', null, ['class' => 'form-control']) !!}
</div>

<!-- Subcurrencyid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('subCurrencyID', 'Subcurrencyid:') !!}
    {!! Form::number('subCurrencyID', null, ['class' => 'form-control']) !!}
</div>

<!-- Conversion Field -->
<div class="form-group col-sm-6">
    {!! Form::label('conversion', 'Conversion:') !!}
    {!! Form::number('conversion', null, ['class' => 'form-control']) !!}
</div>

<!-- Timestamp Field -->
<div class="form-group col-sm-6">
    {!! Form::label('timestamp', 'Timestamp:') !!}
    {!! Form::date('timestamp', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('currencyConversions.index') !!}" class="btn btn-default">Cancel</a>
</div>
