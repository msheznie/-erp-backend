<!-- Currencyname Field -->
<div class="form-group col-sm-6">
    {!! Form::label('CurrencyName', 'Currencyname:') !!}
    {!! Form::text('CurrencyName', null, ['class' => 'form-control']) !!}
</div>

<!-- Currencycode Field -->
<div class="form-group col-sm-6">
    {!! Form::label('CurrencyCode', 'Currencycode:') !!}
    {!! Form::text('CurrencyCode', null, ['class' => 'form-control']) !!}
</div>

<!-- Decimalplaces Field -->
<div class="form-group col-sm-6">
    {!! Form::label('DecimalPlaces', 'Decimalplaces:') !!}
    {!! Form::number('DecimalPlaces', null, ['class' => 'form-control']) !!}
</div>

<!-- Exchangerate Field -->
<div class="form-group col-sm-6">
    {!! Form::label('ExchangeRate', 'Exchangerate:') !!}
    {!! Form::number('ExchangeRate', null, ['class' => 'form-control']) !!}
</div>

<!-- Islocal Field -->
<div class="form-group col-sm-6">
    {!! Form::label('isLocal', 'Islocal:') !!}
    {!! Form::number('isLocal', null, ['class' => 'form-control']) !!}
</div>

<!-- Datemodified Field -->
<div class="form-group col-sm-6">
    {!! Form::label('DateModified', 'Datemodified:') !!}
    {!! Form::date('DateModified', null, ['class' => 'form-control']) !!}
</div>

<!-- Modifiedby Field -->
<div class="form-group col-sm-6">
    {!! Form::label('ModifiedBy', 'Modifiedby:') !!}
    {!! Form::text('ModifiedBy', null, ['class' => 'form-control']) !!}
</div>

<!-- Createdusergroup Field -->
<div class="form-group col-sm-6">
    {!! Form::label('createdUserGroup', 'Createdusergroup:') !!}
    {!! Form::text('createdUserGroup', null, ['class' => 'form-control']) !!}
</div>

<!-- Createdpcid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('createdPcID', 'Createdpcid:') !!}
    {!! Form::text('createdPcID', null, ['class' => 'form-control']) !!}
</div>

<!-- Createduserid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('createdUserID', 'Createduserid:') !!}
    {!! Form::text('createdUserID', null, ['class' => 'form-control']) !!}
</div>

<!-- Modifiedpc Field -->
<div class="form-group col-sm-6">
    {!! Form::label('modifiedPc', 'Modifiedpc:') !!}
    {!! Form::text('modifiedPc', null, ['class' => 'form-control']) !!}
</div>

<!-- Modifieduser Field -->
<div class="form-group col-sm-6">
    {!! Form::label('modifiedUser', 'Modifieduser:') !!}
    {!! Form::text('modifiedUser', null, ['class' => 'form-control']) !!}
</div>

<!-- Createddatetime Field -->
<div class="form-group col-sm-6">
    {!! Form::label('createdDateTime', 'Createddatetime:') !!}
    {!! Form::date('createdDateTime', null, ['class' => 'form-control']) !!}
</div>

<!-- Timestamp Field -->
<div class="form-group col-sm-6">
    {!! Form::label('timeStamp', 'Timestamp:') !!}
    {!! Form::date('timeStamp', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('currencyMasters.index') !!}" class="btn btn-default">Cancel</a>
</div>
