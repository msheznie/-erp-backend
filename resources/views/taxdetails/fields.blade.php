<!-- Taxmasterautoid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('taxMasterAutoID', 'Taxmasterautoid:') !!}
    {!! Form::number('taxMasterAutoID', null, ['class' => 'form-control']) !!}
</div>

<!-- Companyid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('companyID', 'Companyid:') !!}
    {!! Form::text('companyID', null, ['class' => 'form-control']) !!}
</div>

<!-- Documentid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('documentID', 'Documentid:') !!}
    {!! Form::text('documentID', null, ['class' => 'form-control']) !!}
</div>

<!-- Documentsystemcode Field -->
<div class="form-group col-sm-6">
    {!! Form::label('documentSystemCode', 'Documentsystemcode:') !!}
    {!! Form::number('documentSystemCode', null, ['class' => 'form-control']) !!}
</div>

<!-- Documentcode Field -->
<div class="form-group col-sm-6">
    {!! Form::label('documentCode', 'Documentcode:') !!}
    {!! Form::text('documentCode', null, ['class' => 'form-control']) !!}
</div>

<!-- Taxshortcode Field -->
<div class="form-group col-sm-6">
    {!! Form::label('taxShortCode', 'Taxshortcode:') !!}
    {!! Form::text('taxShortCode', null, ['class' => 'form-control']) !!}
</div>

<!-- Taxdescription Field -->
<div class="form-group col-sm-6">
    {!! Form::label('taxDescription', 'Taxdescription:') !!}
    {!! Form::text('taxDescription', null, ['class' => 'form-control']) !!}
</div>

<!-- Taxpercent Field -->
<div class="form-group col-sm-6">
    {!! Form::label('taxPercent', 'Taxpercent:') !!}
    {!! Form::number('taxPercent', null, ['class' => 'form-control']) !!}
</div>

<!-- Payeesystemcode Field -->
<div class="form-group col-sm-6">
    {!! Form::label('payeeSystemCode', 'Payeesystemcode:') !!}
    {!! Form::number('payeeSystemCode', null, ['class' => 'form-control']) !!}
</div>

<!-- Payeecode Field -->
<div class="form-group col-sm-6">
    {!! Form::label('payeeCode', 'Payeecode:') !!}
    {!! Form::text('payeeCode', null, ['class' => 'form-control']) !!}
</div>

<!-- Payeename Field -->
<div class="form-group col-sm-6">
    {!! Form::label('payeeName', 'Payeename:') !!}
    {!! Form::text('payeeName', null, ['class' => 'form-control']) !!}
</div>

<!-- Currency Field -->
<div class="form-group col-sm-6">
    {!! Form::label('currency', 'Currency:') !!}
    {!! Form::number('currency', null, ['class' => 'form-control']) !!}
</div>

<!-- Currencyer Field -->
<div class="form-group col-sm-6">
    {!! Form::label('currencyER', 'Currencyer:') !!}
    {!! Form::number('currencyER', null, ['class' => 'form-control']) !!}
</div>

<!-- Amount Field -->
<div class="form-group col-sm-6">
    {!! Form::label('amount', 'Amount:') !!}
    {!! Form::number('amount', null, ['class' => 'form-control']) !!}
</div>

<!-- Payeedefaultcurrencyid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('payeeDefaultCurrencyID', 'Payeedefaultcurrencyid:') !!}
    {!! Form::number('payeeDefaultCurrencyID', null, ['class' => 'form-control']) !!}
</div>

<!-- Payeedefaultcurrencyer Field -->
<div class="form-group col-sm-6">
    {!! Form::label('payeeDefaultCurrencyER', 'Payeedefaultcurrencyer:') !!}
    {!! Form::number('payeeDefaultCurrencyER', null, ['class' => 'form-control']) !!}
</div>

<!-- Payeedefaultamount Field -->
<div class="form-group col-sm-6">
    {!! Form::label('payeeDefaultAmount', 'Payeedefaultamount:') !!}
    {!! Form::number('payeeDefaultAmount', null, ['class' => 'form-control']) !!}
</div>

<!-- Localcurrencyid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('localCurrencyID', 'Localcurrencyid:') !!}
    {!! Form::number('localCurrencyID', null, ['class' => 'form-control']) !!}
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

<!-- Rptcurrencyid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('rptCurrencyID', 'Rptcurrencyid:') !!}
    {!! Form::number('rptCurrencyID', null, ['class' => 'form-control']) !!}
</div>

<!-- Rptcurrencyer Field -->
<div class="form-group col-sm-6">
    {!! Form::label('rptCurrencyER', 'Rptcurrencyer:') !!}
    {!! Form::number('rptCurrencyER', null, ['class' => 'form-control']) !!}
</div>

<!-- Rptamount Field -->
<div class="form-group col-sm-6">
    {!! Form::label('rptAmount', 'Rptamount:') !!}
    {!! Form::number('rptAmount', null, ['class' => 'form-control']) !!}
</div>

<!-- Timestamp Field -->
<div class="form-group col-sm-6">
    {!! Form::label('timestamp', 'Timestamp:') !!}
    {!! Form::date('timestamp', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('taxdetails.index') !!}" class="btn btn-default">Cancel</a>
</div>
