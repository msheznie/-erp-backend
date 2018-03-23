<!-- Warehousecode Field -->
<div class="form-group col-sm-6">
    {!! Form::label('wareHouseCode', 'Warehousecode:') !!}
    {!! Form::text('wareHouseCode', null, ['class' => 'form-control']) !!}
</div>

<!-- Warehousedescription Field -->
<div class="form-group col-sm-6">
    {!! Form::label('wareHouseDescription', 'Warehousedescription:') !!}
    {!! Form::text('wareHouseDescription', null, ['class' => 'form-control']) !!}
</div>

<!-- Warehouselocation Field -->
<div class="form-group col-sm-6">
    {!! Form::label('wareHouseLocation', 'Warehouselocation:') !!}
    {!! Form::number('wareHouseLocation', null, ['class' => 'form-control']) !!}
</div>

<!-- Isactive Field -->
<div class="form-group col-sm-6">
    {!! Form::label('isActive', 'Isactive:') !!}
    {!! Form::number('isActive', null, ['class' => 'form-control']) !!}
</div>

<!-- Companyid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('companyID', 'Companyid:') !!}
    {!! Form::text('companyID', null, ['class' => 'form-control']) !!}
</div>

<!-- Companysystemid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('companySystemID', 'Companysystemid:') !!}
    {!! Form::number('companySystemID', null, ['class' => 'form-control']) !!}
</div>

<!-- Timestamp Field -->
<div class="form-group col-sm-6">
    {!! Form::label('timestamp', 'Timestamp:') !!}
    {!! Form::date('timestamp', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('warehouseMasters.index') !!}" class="btn btn-default">Cancel</a>
</div>
