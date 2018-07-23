<!-- Paymenttermid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('paymentTermID', 'Paymenttermid:') !!}
    {!! Form::number('paymentTermID', null, ['class' => 'form-control']) !!}
</div>

<!-- Paymenttermscategory Field -->
<div class="form-group col-sm-6">
    {!! Form::label('paymentTermsCategory', 'Paymenttermscategory:') !!}
    {!! Form::number('paymentTermsCategory', null, ['class' => 'form-control']) !!}
</div>

<!-- Poid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('poID', 'Poid:') !!}
    {!! Form::number('poID', null, ['class' => 'form-control']) !!}
</div>

<!-- Paymenttemdes Field -->
<div class="form-group col-sm-6">
    {!! Form::label('paymentTemDes', 'Paymenttemdes:') !!}
    {!! Form::text('paymentTemDes', null, ['class' => 'form-control']) !!}
</div>

<!-- Comamount Field -->
<div class="form-group col-sm-6">
    {!! Form::label('comAmount', 'Comamount:') !!}
    {!! Form::number('comAmount', null, ['class' => 'form-control']) !!}
</div>

<!-- Compercentage Field -->
<div class="form-group col-sm-6">
    {!! Form::label('comPercentage', 'Compercentage:') !!}
    {!! Form::number('comPercentage', null, ['class' => 'form-control']) !!}
</div>

<!-- Indays Field -->
<div class="form-group col-sm-6">
    {!! Form::label('inDays', 'Indays:') !!}
    {!! Form::number('inDays', null, ['class' => 'form-control']) !!}
</div>

<!-- Comdate Field -->
<div class="form-group col-sm-6">
    {!! Form::label('comDate', 'Comdate:') !!}
    {!! Form::date('comDate', null, ['class' => 'form-control']) !!}
</div>

<!-- Lcpaymentyn Field -->
<div class="form-group col-sm-6">
    {!! Form::label('LCPaymentYN', 'Lcpaymentyn:') !!}
    {!! Form::number('LCPaymentYN', null, ['class' => 'form-control']) !!}
</div>

<!-- Isrequested Field -->
<div class="form-group col-sm-6">
    {!! Form::label('isRequested', 'Isrequested:') !!}
    {!! Form::number('isRequested', null, ['class' => 'form-control']) !!}
</div>

<!-- Timesreferred Field -->
<div class="form-group col-sm-6">
    {!! Form::label('timesReferred', 'Timesreferred:') !!}
    {!! Form::number('timesReferred', null, ['class' => 'form-control']) !!}
</div>

<!-- Createddatetime Field -->
<div class="form-group col-sm-6">
    {!! Form::label('createdDateTime', 'Createddatetime:') !!}
    {!! Form::date('createdDateTime', null, ['class' => 'form-control']) !!}
</div>

<!-- Timestamp Field -->
<div class="form-group col-sm-6">
    {!! Form::label('timestamp', 'Timestamp:') !!}
    {!! Form::date('timestamp', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('poPaymentTermsRefferedbacks.index') !!}" class="btn btn-default">Cancel</a>
</div>
