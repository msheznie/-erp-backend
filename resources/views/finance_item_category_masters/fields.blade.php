<!-- Categorydescription Field -->
<div class="form-group col-sm-6">
    {!! Form::label('categoryDescription', 'Categorydescription:') !!}
    {!! Form::text('categoryDescription', null, ['class' => 'form-control']) !!}
</div>

<!-- Itemcodedef Field -->
<div class="form-group col-sm-6">
    {!! Form::label('itemCodeDef', 'Itemcodedef:') !!}
    {!! Form::text('itemCodeDef', null, ['class' => 'form-control']) !!}
</div>

<!-- Numberofdigits Field -->
<div class="form-group col-sm-6">
    {!! Form::label('numberOfDigits', 'Numberofdigits:') !!}
    {!! Form::number('numberOfDigits', null, ['class' => 'form-control']) !!}
</div>

<!-- Lastserialorder Field -->
<div class="form-group col-sm-6">
    {!! Form::label('lastSerialOrder', 'Lastserialorder:') !!}
    {!! Form::number('lastSerialOrder', null, ['class' => 'form-control']) !!}
</div>

<!-- Timestamp Field -->
<div class="form-group col-sm-6">
    {!! Form::label('timeStamp', 'Timestamp:') !!}
    {!! Form::date('timeStamp', null, ['class' => 'form-control']) !!}
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

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('financeItemCategoryMasters.index') !!}" class="btn btn-default">Cancel</a>
</div>
