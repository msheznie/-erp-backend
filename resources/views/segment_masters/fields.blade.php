<!-- Servicelinecode Field -->
<div class="form-group col-sm-6">
    {!! Form::label('ServiceLineCode', 'Servicelinecode:') !!}
    {!! Form::text('ServiceLineCode', null, ['class' => 'form-control']) !!}
</div>

<!-- Servicelinemastercode Field -->
<div class="form-group col-sm-6">
    {!! Form::label('serviceLineMasterCode', 'Servicelinemastercode:') !!}
    {!! Form::text('serviceLineMasterCode', null, ['class' => 'form-control']) !!}
</div>

<!-- Companyid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('companyID', 'Companyid:') !!}
    {!! Form::text('companyID', null, ['class' => 'form-control']) !!}
</div>

<!-- Servicelinedes Field -->
<div class="form-group col-sm-6">
    {!! Form::label('ServiceLineDes', 'Servicelinedes:') !!}
    {!! Form::text('ServiceLineDes', null, ['class' => 'form-control']) !!}
</div>

<!-- Locationid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('locationID', 'Locationid:') !!}
    {!! Form::number('locationID', null, ['class' => 'form-control']) !!}
</div>

<!-- Isactive Field -->
<div class="form-group col-sm-6">
    {!! Form::label('isActive', 'Isactive:') !!}
    {!! Form::number('isActive', null, ['class' => 'form-control']) !!}
</div>

<!-- Ispublic Field -->
<div class="form-group col-sm-6">
    {!! Form::label('isPublic', 'Ispublic:') !!}
    {!! Form::number('isPublic', null, ['class' => 'form-control']) !!}
</div>

<!-- Isserviceline Field -->
<div class="form-group col-sm-6">
    {!! Form::label('isServiceLine', 'Isserviceline:') !!}
    {!! Form::number('isServiceLine', null, ['class' => 'form-control']) !!}
</div>

<!-- Isdepartment Field -->
<div class="form-group col-sm-6">
    {!! Form::label('isDepartment', 'Isdepartment:') !!}
    {!! Form::number('isDepartment', null, ['class' => 'form-control']) !!}
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
    <a href="{!! route('segmentMasters.index') !!}" class="btn btn-default">Cancel</a>
</div>
