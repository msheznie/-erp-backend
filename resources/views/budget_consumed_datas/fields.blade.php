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

<!-- Documentsystemid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('documentSystemID', 'Documentsystemid:') !!}
    {!! Form::number('documentSystemID', null, ['class' => 'form-control']) !!}
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

<!-- Chartofaccountid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('chartOfAccountID', 'Chartofaccountid:') !!}
    {!! Form::number('chartOfAccountID', null, ['class' => 'form-control']) !!}
</div>

<!-- Glcode Field -->
<div class="form-group col-sm-6">
    {!! Form::label('GLCode', 'Glcode:') !!}
    {!! Form::text('GLCode', null, ['class' => 'form-control']) !!}
</div>

<!-- Year Field -->
<div class="form-group col-sm-6">
    {!! Form::label('year', 'Year:') !!}
    {!! Form::number('year', null, ['class' => 'form-control']) !!}
</div>

<!-- Month Field -->
<div class="form-group col-sm-6">
    {!! Form::label('month', 'Month:') !!}
    {!! Form::number('month', null, ['class' => 'form-control']) !!}
</div>

<!-- Consumedlocalcurrencyid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('consumedLocalCurrencyID', 'Consumedlocalcurrencyid:') !!}
    {!! Form::number('consumedLocalCurrencyID', null, ['class' => 'form-control']) !!}
</div>

<!-- Consumedlocalamount Field -->
<div class="form-group col-sm-6">
    {!! Form::label('consumedLocalAmount', 'Consumedlocalamount:') !!}
    {!! Form::number('consumedLocalAmount', null, ['class' => 'form-control']) !!}
</div>

<!-- Consumedrptcurrencyid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('consumedRptCurrencyID', 'Consumedrptcurrencyid:') !!}
    {!! Form::number('consumedRptCurrencyID', null, ['class' => 'form-control']) !!}
</div>

<!-- Consumedrptamount Field -->
<div class="form-group col-sm-6">
    {!! Form::label('consumedRptAmount', 'Consumedrptamount:') !!}
    {!! Form::number('consumedRptAmount', null, ['class' => 'form-control']) !!}
</div>

<!-- Consumeyn Field -->
<div class="form-group col-sm-6">
    {!! Form::label('consumeYN', 'Consumeyn:') !!}
    {!! Form::number('consumeYN', null, ['class' => 'form-control']) !!}
</div>

<!-- Timestamp Field -->
<div class="form-group col-sm-6">
    {!! Form::label('timestamp', 'Timestamp:') !!}
    {!! Form::text('timestamp', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('budgetConsumedDatas.index') !!}" class="btn btn-default">Cancel</a>
</div>
