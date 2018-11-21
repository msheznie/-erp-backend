<!-- Advancepaymentdetailautoid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('advancePaymentDetailAutoID', 'Advancepaymentdetailautoid:') !!}
    {!! Form::number('advancePaymentDetailAutoID', null, ['class' => 'form-control']) !!}
</div>

<!-- Paymasterautoid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('PayMasterAutoId', 'Paymasterautoid:') !!}
    {!! Form::number('PayMasterAutoId', null, ['class' => 'form-control']) !!}
</div>

<!-- Poadvpaymentid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('poAdvPaymentID', 'Poadvpaymentid:') !!}
    {!! Form::number('poAdvPaymentID', null, ['class' => 'form-control']) !!}
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

<!-- Purchaseorderid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('purchaseOrderID', 'Purchaseorderid:') !!}
    {!! Form::number('purchaseOrderID', null, ['class' => 'form-control']) !!}
</div>

<!-- Purchaseordercode Field -->
<div class="form-group col-sm-6">
    {!! Form::label('purchaseOrderCode', 'Purchaseordercode:') !!}
    {!! Form::text('purchaseOrderCode', null, ['class' => 'form-control']) !!}
</div>

<!-- Comments Field -->
<div class="form-group col-sm-12 col-lg-12">
    {!! Form::label('comments', 'Comments:') !!}
    {!! Form::textarea('comments', null, ['class' => 'form-control']) !!}
</div>

<!-- Paymentamount Field -->
<div class="form-group col-sm-6">
    {!! Form::label('paymentAmount', 'Paymentamount:') !!}
    {!! Form::number('paymentAmount', null, ['class' => 'form-control']) !!}
</div>

<!-- Suppliertranscurrencyid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('supplierTransCurrencyID', 'Suppliertranscurrencyid:') !!}
    {!! Form::number('supplierTransCurrencyID', null, ['class' => 'form-control']) !!}
</div>

<!-- Suppliertranser Field -->
<div class="form-group col-sm-6">
    {!! Form::label('supplierTransER', 'Suppliertranser:') !!}
    {!! Form::number('supplierTransER', null, ['class' => 'form-control']) !!}
</div>

<!-- Supplierdefaultcurrencyid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('supplierDefaultCurrencyID', 'Supplierdefaultcurrencyid:') !!}
    {!! Form::number('supplierDefaultCurrencyID', null, ['class' => 'form-control']) !!}
</div>

<!-- Supplierdefaultcurrencyer Field -->
<div class="form-group col-sm-6">
    {!! Form::label('supplierDefaultCurrencyER', 'Supplierdefaultcurrencyer:') !!}
    {!! Form::number('supplierDefaultCurrencyER', null, ['class' => 'form-control']) !!}
</div>

<!-- Localcurrencyid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('localCurrencyID', 'Localcurrencyid:') !!}
    {!! Form::number('localCurrencyID', null, ['class' => 'form-control']) !!}
</div>

<!-- Localer Field -->
<div class="form-group col-sm-6">
    {!! Form::label('localER', 'Localer:') !!}
    {!! Form::number('localER', null, ['class' => 'form-control']) !!}
</div>

<!-- Comrptcurrencyid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('comRptCurrencyID', 'Comrptcurrencyid:') !!}
    {!! Form::number('comRptCurrencyID', null, ['class' => 'form-control']) !!}
</div>

<!-- Comrpter Field -->
<div class="form-group col-sm-6">
    {!! Form::label('comRptER', 'Comrpter:') !!}
    {!! Form::number('comRptER', null, ['class' => 'form-control']) !!}
</div>

<!-- Supplierdefaultamount Field -->
<div class="form-group col-sm-6">
    {!! Form::label('supplierDefaultAmount', 'Supplierdefaultamount:') !!}
    {!! Form::number('supplierDefaultAmount', null, ['class' => 'form-control']) !!}
</div>

<!-- Suppliertransamount Field -->
<div class="form-group col-sm-6">
    {!! Form::label('supplierTransAmount', 'Suppliertransamount:') !!}
    {!! Form::number('supplierTransAmount', null, ['class' => 'form-control']) !!}
</div>

<!-- Localamount Field -->
<div class="form-group col-sm-6">
    {!! Form::label('localAmount', 'Localamount:') !!}
    {!! Form::number('localAmount', null, ['class' => 'form-control']) !!}
</div>

<!-- Comrptamount Field -->
<div class="form-group col-sm-6">
    {!! Form::label('comRptAmount', 'Comrptamount:') !!}
    {!! Form::number('comRptAmount', null, ['class' => 'form-control']) !!}
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
    <a href="{!! route('advancePaymentReferbacks.index') !!}" class="btn btn-default">Cancel</a>
</div>
