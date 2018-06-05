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

<!-- Referencenumber Field -->
<div class="form-group col-sm-6">
    {!! Form::label('referenceNumber', 'Referencenumber:') !!}
    {!! Form::text('referenceNumber', null, ['class' => 'form-control']) !!}
</div>

<!-- Warehousesystemcode Field -->
<div class="form-group col-sm-6">
    {!! Form::label('wareHouseSystemCode', 'Warehousesystemcode:') !!}
    {!! Form::number('wareHouseSystemCode', null, ['class' => 'form-control']) !!}
</div>

<!-- Itemsystemcode Field -->
<div class="form-group col-sm-6">
    {!! Form::label('itemSystemCode', 'Itemsystemcode:') !!}
    {!! Form::number('itemSystemCode', null, ['class' => 'form-control']) !!}
</div>

<!-- Itemprimarycode Field -->
<div class="form-group col-sm-6">
    {!! Form::label('itemPrimaryCode', 'Itemprimarycode:') !!}
    {!! Form::text('itemPrimaryCode', null, ['class' => 'form-control']) !!}
</div>

<!-- Itemdescription Field -->
<div class="form-group col-sm-12 col-lg-12">
    {!! Form::label('itemDescription', 'Itemdescription:') !!}
    {!! Form::textarea('itemDescription', null, ['class' => 'form-control']) !!}
</div>

<!-- Unitofmeasure Field -->
<div class="form-group col-sm-6">
    {!! Form::label('unitOfMeasure', 'Unitofmeasure:') !!}
    {!! Form::number('unitOfMeasure', null, ['class' => 'form-control']) !!}
</div>

<!-- Inoutqty Field -->
<div class="form-group col-sm-6">
    {!! Form::label('inOutQty', 'Inoutqty:') !!}
    {!! Form::number('inOutQty', null, ['class' => 'form-control']) !!}
</div>

<!-- Waclocalcurrencyid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('wacLocalCurrencyID', 'Waclocalcurrencyid:') !!}
    {!! Form::number('wacLocalCurrencyID', null, ['class' => 'form-control']) !!}
</div>

<!-- Waclocal Field -->
<div class="form-group col-sm-6">
    {!! Form::label('wacLocal', 'Waclocal:') !!}
    {!! Form::number('wacLocal', null, ['class' => 'form-control']) !!}
</div>

<!-- Wacrptcurrencyid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('wacRptCurrencyID', 'Wacrptcurrencyid:') !!}
    {!! Form::number('wacRptCurrencyID', null, ['class' => 'form-control']) !!}
</div>

<!-- Wacrpt Field -->
<div class="form-group col-sm-6">
    {!! Form::label('wacRpt', 'Wacrpt:') !!}
    {!! Form::number('wacRpt', null, ['class' => 'form-control']) !!}
</div>

<!-- Comments Field -->
<div class="form-group col-sm-12 col-lg-12">
    {!! Form::label('comments', 'Comments:') !!}
    {!! Form::textarea('comments', null, ['class' => 'form-control']) !!}
</div>

<!-- Transactiondate Field -->
<div class="form-group col-sm-6">
    {!! Form::label('transactionDate', 'Transactiondate:') !!}
    {!! Form::date('transactionDate', null, ['class' => 'form-control']) !!}
</div>

<!-- Fromdamagedtransactionyn Field -->
<div class="form-group col-sm-6">
    {!! Form::label('fromDamagedTransactionYN', 'Fromdamagedtransactionyn:') !!}
    {!! Form::number('fromDamagedTransactionYN', null, ['class' => 'form-control']) !!}
</div>

<!-- Createdusersystemid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('createdUserSystemID', 'Createdusersystemid:') !!}
    {!! Form::number('createdUserSystemID', null, ['class' => 'form-control']) !!}
</div>

<!-- Createduserid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('createdUserID', 'Createduserid:') !!}
    {!! Form::text('createdUserID', null, ['class' => 'form-control']) !!}
</div>

<!-- Timestamp Field -->
<div class="form-group col-sm-6">
    {!! Form::label('timestamp', 'Timestamp:') !!}
    {!! Form::date('timestamp', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('erpItemLedgers.index') !!}" class="btn btn-default">Cancel</a>
</div>
