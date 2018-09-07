<!-- Binlocationdes Field -->
<div class="form-group col-sm-6">
    {!! Form::label('binLocationDes', 'Binlocationdes:') !!}
    {!! Form::text('binLocationDes', null, ['class' => 'form-control']) !!}
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

<!-- Warehousesystemcode Field -->
<div class="form-group col-sm-6">
    {!! Form::label('wareHouseSystemCode', 'Warehousesystemcode:') !!}
    {!! Form::number('wareHouseSystemCode', null, ['class' => 'form-control']) !!}
</div>

<!-- Createdby Field -->
<div class="form-group col-sm-6">
    {!! Form::label('createdBy', 'Createdby:') !!}
    {!! Form::text('createdBy', null, ['class' => 'form-control']) !!}
</div>

<!-- Datecreated Field -->
<div class="form-group col-sm-6">
    {!! Form::label('dateCreated', 'Datecreated:') !!}
    {!! Form::date('dateCreated', null, ['class' => 'form-control']) !!}
</div>

<!-- Isactive Field -->
<div class="form-group col-sm-6">
    {!! Form::label('isActive', 'Isactive:') !!}
    {!! Form::number('isActive', null, ['class' => 'form-control']) !!}
</div>

<!-- Timestamp Field -->
<div class="form-group col-sm-6">
    {!! Form::label('timeStamp', 'Timestamp:') !!}
    {!! Form::date('timeStamp', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('warehouseBinLocations.index') !!}" class="btn btn-default">Cancel</a>
</div>
