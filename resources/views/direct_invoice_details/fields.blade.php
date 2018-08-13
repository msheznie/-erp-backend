<!-- Directinvoiceautoid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('directInvoiceAutoID', 'Directinvoiceautoid:') !!}
    {!! Form::number('directInvoiceAutoID', null, ['class' => 'form-control']) !!}
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

<!-- Comments Field -->
<div class="form-group col-sm-6">
    {!! Form::label('comments', 'Comments:') !!}
    {!! Form::text('comments', null, ['class' => 'form-control']) !!}
</div>

<!-- Percentage Field -->
<div class="form-group col-sm-6">
    {!! Form::label('percentage', 'Percentage:') !!}
    {!! Form::number('percentage', null, ['class' => 'form-control']) !!}
</div>

<!-- Diamountcurrency Field -->
<div class="form-group col-sm-6">
    {!! Form::label('DIAmountCurrency', 'Diamountcurrency:') !!}
    {!! Form::number('DIAmountCurrency', null, ['class' => 'form-control']) !!}
</div>

<!-- Diamountcurrencyer Field -->
<div class="form-group col-sm-6">
    {!! Form::label('DIAmountCurrencyER', 'Diamountcurrencyer:') !!}
    {!! Form::number('DIAmountCurrencyER', null, ['class' => 'form-control']) !!}
</div>

<!-- Diamount Field -->
<div class="form-group col-sm-6">
    {!! Form::label('DIAmount', 'Diamount:') !!}
    {!! Form::number('DIAmount', null, ['class' => 'form-control']) !!}
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

<!-- Budgetyear Field -->
<div class="form-group col-sm-6">
    {!! Form::label('budgetYear', 'Budgetyear:') !!}
    {!! Form::number('budgetYear', null, ['class' => 'form-control']) !!}
</div>

<!-- Isextraaddon Field -->
<div class="form-group col-sm-6">
    {!! Form::label('isExtraAddon', 'Isextraaddon:') !!}
    {!! Form::number('isExtraAddon', null, ['class' => 'form-control']) !!}
</div>

<!-- Timesreferred Field -->
<div class="form-group col-sm-6">
    {!! Form::label('timesReferred', 'Timesreferred:') !!}
    {!! Form::number('timesReferred', null, ['class' => 'form-control']) !!}
</div>

<!-- Timestamp Field -->
<div class="form-group col-sm-6">
    {!! Form::label('timeStamp', 'Timestamp:') !!}
    {!! Form::date('timeStamp', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('directInvoiceDetails.index') !!}" class="btn btn-default">Cancel</a>
</div>
