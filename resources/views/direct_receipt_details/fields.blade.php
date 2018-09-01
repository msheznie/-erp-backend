<!-- Directreceiptautoid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('directReceiptAutoID', 'Directreceiptautoid:') !!}
    {!! Form::number('directReceiptAutoID', null, ['class' => 'form-control']) !!}
</div>

<!-- Companyid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('companyID', 'Companyid:') !!}
    {!! Form::text('companyID', null, ['class' => 'form-control']) !!}
</div>

<!-- Servicelinecode Field -->
<div class="form-group col-sm-6">
    {!! Form::label('serviceLineCode', 'Servicelinecode:') !!}
    {!! Form::text('serviceLineCode', null, ['class' => 'form-control']) !!}
</div>

<!-- Glcode Field -->
<div class="form-group col-sm-6">
    {!! Form::label('glCode', 'Glcode:') !!}
    {!! Form::text('glCode', null, ['class' => 'form-control']) !!}
</div>

<!-- Glcodedes Field -->
<div class="form-group col-sm-6">
    {!! Form::label('glCodeDes', 'Glcodedes:') !!}
    {!! Form::text('glCodeDes', null, ['class' => 'form-control']) !!}
</div>

<!-- Contractid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('contractID', 'Contractid:') !!}
    {!! Form::text('contractID', null, ['class' => 'form-control']) !!}
</div>

<!-- Comments Field -->
<div class="form-group col-sm-6">
    {!! Form::label('comments', 'Comments:') !!}
    {!! Form::text('comments', null, ['class' => 'form-control']) !!}
</div>

<!-- Dramountcurrency Field -->
<div class="form-group col-sm-6">
    {!! Form::label('DRAmountCurrency', 'Dramountcurrency:') !!}
    {!! Form::number('DRAmountCurrency', null, ['class' => 'form-control']) !!}
</div>

<!-- Ddramountcurrencyer Field -->
<div class="form-group col-sm-6">
    {!! Form::label('DDRAmountCurrencyER', 'Ddramountcurrencyer:') !!}
    {!! Form::number('DDRAmountCurrencyER', null, ['class' => 'form-control']) !!}
</div>

<!-- Dramount Field -->
<div class="form-group col-sm-6">
    {!! Form::label('DRAmount', 'Dramount:') !!}
    {!! Form::number('DRAmount', null, ['class' => 'form-control']) !!}
</div>

<!-- Localcurrency Field -->
<div class="form-group col-sm-6">
    {!! Form::label('localCurrency', 'Localcurrency:') !!}
    {!! Form::number('localCurrency', null, ['class' => 'form-control']) !!}
</div>

<!-- Localcurrencyer Field -->
<div class="form-group col-sm-6">
    {!! Form::label('localCurrencyER', 'Localcurrencyer:') !!}
    {!! Form::number('localCurrencyER', null, ['class' => 'form-control']) !!}
</div>

<!-- Localamount Field -->
<div class="form-group col-sm-6">
    {!! Form::label('localAmount', 'Localamount:') !!}
    {!! Form::number('localAmount', null, ['class' => 'form-control']) !!}
</div>

<!-- Comrptcurrency Field -->
<div class="form-group col-sm-6">
    {!! Form::label('comRptCurrency', 'Comrptcurrency:') !!}
    {!! Form::number('comRptCurrency', null, ['class' => 'form-control']) !!}
</div>

<!-- Comrptcurrencyer Field -->
<div class="form-group col-sm-6">
    {!! Form::label('comRptCurrencyER', 'Comrptcurrencyer:') !!}
    {!! Form::number('comRptCurrencyER', null, ['class' => 'form-control']) !!}
</div>

<!-- Comrptamount Field -->
<div class="form-group col-sm-6">
    {!! Form::label('comRptAmount', 'Comrptamount:') !!}
    {!! Form::number('comRptAmount', null, ['class' => 'form-control']) !!}
</div>

<!-- Timestamp Field -->
<div class="form-group col-sm-6">
    {!! Form::label('timeStamp', 'Timestamp:') !!}
    {!! Form::date('timeStamp', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('directReceiptDetails.index') !!}" class="btn btn-default">Cancel</a>
</div>
