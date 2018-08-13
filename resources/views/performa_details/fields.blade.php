<!-- Companyid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('companyID', 'Companyid:') !!}
    {!! Form::text('companyID', null, ['class' => 'form-control']) !!}
</div>

<!-- Serviceline Field -->
<div class="form-group col-sm-6">
    {!! Form::label('serviceLine', 'Serviceline:') !!}
    {!! Form::text('serviceLine', null, ['class' => 'form-control']) !!}
</div>

<!-- Customerid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('customerID', 'Customerid:') !!}
    {!! Form::number('customerID', null, ['class' => 'form-control']) !!}
</div>

<!-- Contractid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('contractID', 'Contractid:') !!}
    {!! Form::text('contractID', null, ['class' => 'form-control']) !!}
</div>

<!-- Performamasterid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('performaMasterID', 'Performamasterid:') !!}
    {!! Form::number('performaMasterID', null, ['class' => 'form-control']) !!}
</div>

<!-- Performacode Field -->
<div class="form-group col-sm-6">
    {!! Form::label('performaCode', 'Performacode:') !!}
    {!! Form::text('performaCode', null, ['class' => 'form-control']) !!}
</div>

<!-- Ticketno Field -->
<div class="form-group col-sm-6">
    {!! Form::label('ticketNo', 'Ticketno:') !!}
    {!! Form::number('ticketNo', null, ['class' => 'form-control']) !!}
</div>

<!-- Currencyid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('currencyID', 'Currencyid:') !!}
    {!! Form::number('currencyID', null, ['class' => 'form-control']) !!}
</div>

<!-- Totamount Field -->
<div class="form-group col-sm-6">
    {!! Form::label('totAmount', 'Totamount:') !!}
    {!! Form::number('totAmount', null, ['class' => 'form-control']) !!}
</div>

<!-- Financeglcode Field -->
<div class="form-group col-sm-6">
    {!! Form::label('financeGLcode', 'Financeglcode:') !!}
    {!! Form::text('financeGLcode', null, ['class' => 'form-control']) !!}
</div>

<!-- Invoicessytemcode Field -->
<div class="form-group col-sm-6">
    {!! Form::label('invoiceSsytemCode', 'Invoicessytemcode:') !!}
    {!! Form::number('invoiceSsytemCode', null, ['class' => 'form-control']) !!}
</div>

<!-- Vendorcode Field -->
<div class="form-group col-sm-6">
    {!! Form::label('vendorCode', 'Vendorcode:') !!}
    {!! Form::text('vendorCode', null, ['class' => 'form-control']) !!}
</div>

<!-- Bankid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('bankID', 'Bankid:') !!}
    {!! Form::number('bankID', null, ['class' => 'form-control']) !!}
</div>

<!-- Accountid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('accountID', 'Accountid:') !!}
    {!! Form::number('accountID', null, ['class' => 'form-control']) !!}
</div>

<!-- Paymentperioddays Field -->
<div class="form-group col-sm-6">
    {!! Form::label('paymentPeriodDays', 'Paymentperioddays:') !!}
    {!! Form::number('paymentPeriodDays', null, ['class' => 'form-control']) !!}
</div>

<!-- Timestamp Field -->
<div class="form-group col-sm-6">
    {!! Form::label('timestamp', 'Timestamp:') !!}
    {!! Form::date('timestamp', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('performaDetails.index') !!}" class="btn btn-default">Cancel</a>
</div>
