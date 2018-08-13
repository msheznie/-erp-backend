<!-- Iditemassigned Field -->
<div class="form-group col-sm-6">
    {!! Form::label('idItemAssigned', 'Iditemassigned:') !!}
    {!! Form::number('idItemAssigned', null, ['class' => 'form-control']) !!}
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

<!-- Customerid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('customerID', 'Customerid:') !!}
    {!! Form::number('customerID', null, ['class' => 'form-control']) !!}
</div>

<!-- Contractuiid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('contractUIID', 'Contractuiid:') !!}
    {!! Form::number('contractUIID', null, ['class' => 'form-control']) !!}
</div>

<!-- Contractid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('contractID', 'Contractid:') !!}
    {!! Form::text('contractID', null, ['class' => 'form-control']) !!}
</div>

<!-- Clientreferencenumber Field -->
<div class="form-group col-sm-6">
    {!! Form::label('clientReferenceNumber', 'Clientreferencenumber:') !!}
    {!! Form::text('clientReferenceNumber', null, ['class' => 'form-control']) !!}
</div>

<!-- Createdbyuserid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('createdByUserID', 'Createdbyuserid:') !!}
    {!! Form::text('createdByUserID', null, ['class' => 'form-control']) !!}
</div>

<!-- Createddatetime Field -->
<div class="form-group col-sm-6">
    {!! Form::label('createdDateTime', 'Createddatetime:') !!}
    {!! Form::date('createdDateTime', null, ['class' => 'form-control']) !!}
</div>

<!-- Modifiedbyuserid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('modifiedByUserID', 'Modifiedbyuserid:') !!}
    {!! Form::text('modifiedByUserID', null, ['class' => 'form-control']) !!}
</div>

<!-- Modifieddatetime Field -->
<div class="form-group col-sm-6">
    {!! Form::label('modifiedDateTime', 'Modifieddatetime:') !!}
    {!! Form::text('modifiedDateTime', null, ['class' => 'form-control']) !!}
</div>

<!-- Timestamp Field -->
<div class="form-group col-sm-6">
    {!! Form::label('timestamp', 'Timestamp:') !!}
    {!! Form::date('timestamp', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('itemClientReferenceNumberMasters.index') !!}" class="btn btn-default">Cancel</a>
</div>
