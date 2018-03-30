<!-- Currencyconversionautoid Field -->
<div class="form-group">
    {!! Form::label('currencyConversionAutoID', 'Currencyconversionautoid:') !!}
    <p>{!! $currencyConversion->currencyConversionAutoID !!}</p>
</div>

<!-- Mastercurrencyid Field -->
<div class="form-group">
    {!! Form::label('masterCurrencyID', 'Mastercurrencyid:') !!}
    <p>{!! $currencyConversion->masterCurrencyID !!}</p>
</div>

<!-- Subcurrencyid Field -->
<div class="form-group">
    {!! Form::label('subCurrencyID', 'Subcurrencyid:') !!}
    <p>{!! $currencyConversion->subCurrencyID !!}</p>
</div>

<!-- Conversion Field -->
<div class="form-group">
    {!! Form::label('conversion', 'Conversion:') !!}
    <p>{!! $currencyConversion->conversion !!}</p>
</div>

<!-- Timestamp Field -->
<div class="form-group">
    {!! Form::label('timestamp', 'Timestamp:') !!}
    <p>{!! $currencyConversion->timestamp !!}</p>
</div>

