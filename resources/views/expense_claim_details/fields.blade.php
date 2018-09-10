<!-- Expenseclaimmasterautoid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('expenseClaimMasterAutoID', 'Expenseclaimmasterautoid:') !!}
    {!! Form::number('expenseClaimMasterAutoID', null, ['class' => 'form-control']) !!}
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

<!-- Expenseclaimcategoriesautoid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('expenseClaimCategoriesAutoID', 'Expenseclaimcategoriesautoid:') !!}
    {!! Form::number('expenseClaimCategoriesAutoID', null, ['class' => 'form-control']) !!}
</div>

<!-- Description Field -->
<div class="form-group col-sm-12 col-lg-12">
    {!! Form::label('description', 'Description:') !!}
    {!! Form::textarea('description', null, ['class' => 'form-control']) !!}
</div>

<!-- Docref Field -->
<div class="form-group col-sm-6">
    {!! Form::label('docRef', 'Docref:') !!}
    {!! Form::text('docRef', null, ['class' => 'form-control']) !!}
</div>

<!-- Amount Field -->
<div class="form-group col-sm-6">
    {!! Form::label('amount', 'Amount:') !!}
    {!! Form::number('amount', null, ['class' => 'form-control']) !!}
</div>

<!-- Comments Field -->
<div class="form-group col-sm-12 col-lg-12">
    {!! Form::label('comments', 'Comments:') !!}
    {!! Form::textarea('comments', null, ['class' => 'form-control']) !!}
</div>

<!-- Glcode Field -->
<div class="form-group col-sm-6">
    {!! Form::label('glCode', 'Glcode:') !!}
    {!! Form::text('glCode', null, ['class' => 'form-control']) !!}
</div>

<!-- Glcodedescription Field -->
<div class="form-group col-sm-6">
    {!! Form::label('glCodeDescription', 'Glcodedescription:') !!}
    {!! Form::text('glCodeDescription', null, ['class' => 'form-control']) !!}
</div>

<!-- Currencyid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('currencyID', 'Currencyid:') !!}
    {!! Form::number('currencyID', null, ['class' => 'form-control']) !!}
</div>

<!-- Currencyer Field -->
<div class="form-group col-sm-6">
    {!! Form::label('currencyER', 'Currencyer:') !!}
    {!! Form::number('currencyER', null, ['class' => 'form-control']) !!}
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
    <a href="{!! route('expenseClaimDetails.index') !!}" class="btn btn-default">Cancel</a>
</div>
