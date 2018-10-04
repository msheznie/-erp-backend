<!-- Capitalizationid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('capitalizationID', 'Capitalizationid:') !!}
    {!! Form::number('capitalizationID', null, ['class' => 'form-control']) !!}
</div>

<!-- Faid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('faID', 'Faid:') !!}
    {!! Form::number('faID', null, ['class' => 'form-control']) !!}
</div>

<!-- Facode Field -->
<div class="form-group col-sm-6">
    {!! Form::label('faCode', 'Facode:') !!}
    {!! Form::text('faCode', null, ['class' => 'form-control']) !!}
</div>

<!-- Assetdescription Field -->
<div class="form-group col-sm-6">
    {!! Form::label('assetDescription', 'Assetdescription:') !!}
    {!! Form::text('assetDescription', null, ['class' => 'form-control']) !!}
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

<!-- Dateaq Field -->
<div class="form-group col-sm-6">
    {!! Form::label('dateAQ', 'Dateaq:') !!}
    {!! Form::date('dateAQ', null, ['class' => 'form-control']) !!}
</div>

<!-- Assetnbvlocal Field -->
<div class="form-group col-sm-6">
    {!! Form::label('assetNBVLocal', 'Assetnbvlocal:') !!}
    {!! Form::number('assetNBVLocal', null, ['class' => 'form-control']) !!}
</div>

<!-- Assetnbvrpt Field -->
<div class="form-group col-sm-6">
    {!! Form::label('assetNBVRpt', 'Assetnbvrpt:') !!}
    {!! Form::number('assetNBVRpt', null, ['class' => 'form-control']) !!}
</div>

<!-- Allocatedamountlocal Field -->
<div class="form-group col-sm-6">
    {!! Form::label('allocatedAmountLocal', 'Allocatedamountlocal:') !!}
    {!! Form::number('allocatedAmountLocal', null, ['class' => 'form-control']) !!}
</div>

<!-- Allocatedamountrpt Field -->
<div class="form-group col-sm-6">
    {!! Form::label('allocatedAmountRpt', 'Allocatedamountrpt:') !!}
    {!! Form::number('allocatedAmountRpt', null, ['class' => 'form-control']) !!}
</div>

<!-- Createdusergroup Field -->
<div class="form-group col-sm-6">
    {!! Form::label('createdUserGroup', 'Createdusergroup:') !!}
    {!! Form::text('createdUserGroup', null, ['class' => 'form-control']) !!}
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

<!-- Createdpcid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('createdPcID', 'Createdpcid:') !!}
    {!! Form::text('createdPcID', null, ['class' => 'form-control']) !!}
</div>

<!-- Modifiedusersystemid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('modifiedUserSystemID', 'Modifiedusersystemid:') !!}
    {!! Form::number('modifiedUserSystemID', null, ['class' => 'form-control']) !!}
</div>

<!-- Modifieduser Field -->
<div class="form-group col-sm-6">
    {!! Form::label('modifiedUser', 'Modifieduser:') !!}
    {!! Form::text('modifiedUser', null, ['class' => 'form-control']) !!}
</div>

<!-- Modifiedpc Field -->
<div class="form-group col-sm-6">
    {!! Form::label('modifiedPc', 'Modifiedpc:') !!}
    {!! Form::text('modifiedPc', null, ['class' => 'form-control']) !!}
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
    <a href="{!! route('assetCapitalizationDetails.index') !!}" class="btn btn-default">Cancel</a>
</div>
