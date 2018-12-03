<!-- Debitnotedetailsid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('debitNoteDetailsID', 'Debitnotedetailsid:') !!}
    {!! Form::number('debitNoteDetailsID', null, ['class' => 'form-control']) !!}
</div>

<!-- Debitnoteautoid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('debitNoteAutoID', 'Debitnoteautoid:') !!}
    {!! Form::number('debitNoteAutoID', null, ['class' => 'form-control']) !!}
</div>

<!-- Companysystemid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('companySystemID', 'Companysystemid:') !!}
    {!! Form::number('companySystemID', null, ['class' => 'form-control']) !!}
</div>

<!-- Companyid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('companyID', 'Companyid:') !!}
    {!! Form::text('companyID', null, ['class' => 'form-control']) !!}
</div>

<!-- Servicelinesystemid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('serviceLineSystemID', 'Servicelinesystemid:') !!}
    {!! Form::number('serviceLineSystemID', null, ['class' => 'form-control']) !!}
</div>

<!-- Servicelinecode Field -->
<div class="form-group col-sm-6">
    {!! Form::label('serviceLineCode', 'Servicelinecode:') !!}
    {!! Form::text('serviceLineCode', null, ['class' => 'form-control']) !!}
</div>

<!-- Contractid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('contractID', 'Contractid:') !!}
    {!! Form::text('contractID', null, ['class' => 'form-control']) !!}
</div>

<!-- Supplierid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('supplierID', 'Supplierid:') !!}
    {!! Form::number('supplierID', null, ['class' => 'form-control']) !!}
</div>

<!-- Chartofaccountsystemid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('chartOfAccountSystemID', 'Chartofaccountsystemid:') !!}
    {!! Form::number('chartOfAccountSystemID', null, ['class' => 'form-control']) !!}
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

<!-- Debitamountcurrency Field -->
<div class="form-group col-sm-6">
    {!! Form::label('debitAmountCurrency', 'Debitamountcurrency:') !!}
    {!! Form::number('debitAmountCurrency', null, ['class' => 'form-control']) !!}
</div>

<!-- Debitamountcurrencyer Field -->
<div class="form-group col-sm-6">
    {!! Form::label('debitAmountCurrencyER', 'Debitamountcurrencyer:') !!}
    {!! Form::number('debitAmountCurrencyER', null, ['class' => 'form-control']) !!}
</div>

<!-- Debitamount Field -->
<div class="form-group col-sm-6">
    {!! Form::label('debitAmount', 'Debitamount:') !!}
    {!! Form::number('debitAmount', null, ['class' => 'form-control']) !!}
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
    <a href="{!! route('debitNoteDetailsRefferedbacks.index') !!}" class="btn btn-default">Cancel</a>
</div>
