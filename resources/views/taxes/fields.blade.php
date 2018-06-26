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

<!-- Taxdescription Field -->
<div class="form-group col-sm-6">
    {!! Form::label('taxDescription', 'Taxdescription:') !!}
    {!! Form::text('taxDescription', null, ['class' => 'form-control']) !!}
</div>

<!-- Taxshortcode Field -->
<div class="form-group col-sm-6">
    {!! Form::label('taxShortCode', 'Taxshortcode:') !!}
    {!! Form::text('taxShortCode', null, ['class' => 'form-control']) !!}
</div>

<!-- Taxtype Field -->
<div class="form-group col-sm-6">
    {!! Form::label('taxType', 'Taxtype:') !!}
    <label class="checkbox-inline">
        {!! Form::hidden('taxType', false) !!}
        {!! Form::checkbox('taxType', '1', null) !!} 1
    </label>
</div>

<!-- Isactive Field -->
<div class="form-group col-sm-6">
    {!! Form::label('isActive', 'Isactive:') !!}
    <label class="checkbox-inline">
        {!! Form::hidden('isActive', false) !!}
        {!! Form::checkbox('isActive', '1', null) !!} 1
    </label>
</div>

<!-- Authorityautoid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('authorityAutoID', 'Authorityautoid:') !!}
    {!! Form::number('authorityAutoID', null, ['class' => 'form-control']) !!}
</div>

<!-- Glautoid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('GLAutoID', 'Glautoid:') !!}
    {!! Form::number('GLAutoID', null, ['class' => 'form-control']) !!}
</div>

<!-- Currencyid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('currencyID', 'Currencyid:') !!}
    {!! Form::number('currencyID', null, ['class' => 'form-control']) !!}
</div>

<!-- Effectivefrom Field -->
<div class="form-group col-sm-6">
    {!! Form::label('effectiveFrom', 'Effectivefrom:') !!}
    {!! Form::date('effectiveFrom', null, ['class' => 'form-control']) !!}
</div>

<!-- Taxreferenceno Field -->
<div class="form-group col-sm-6">
    {!! Form::label('taxReferenceNo', 'Taxreferenceno:') !!}
    {!! Form::text('taxReferenceNo', null, ['class' => 'form-control']) !!}
</div>

<!-- Createdusergroup Field -->
<div class="form-group col-sm-6">
    {!! Form::label('createdUserGroup', 'Createdusergroup:') !!}
    {!! Form::number('createdUserGroup', null, ['class' => 'form-control']) !!}
</div>

<!-- Createdpcid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('createdPCID', 'Createdpcid:') !!}
    {!! Form::text('createdPCID', null, ['class' => 'form-control']) !!}
</div>

<!-- Createduserid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('createdUserID', 'Createduserid:') !!}
    {!! Form::text('createdUserID', null, ['class' => 'form-control']) !!}
</div>

<!-- Createddatetime Field -->
<div class="form-group col-sm-6">
    {!! Form::label('createdDateTime', 'Createddatetime:') !!}
    {!! Form::date('createdDateTime', null, ['class' => 'form-control']) !!}
</div>

<!-- Createdusername Field -->
<div class="form-group col-sm-6">
    {!! Form::label('createdUserName', 'Createdusername:') !!}
    {!! Form::text('createdUserName', null, ['class' => 'form-control']) !!}
</div>

<!-- Modifiedpcid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('modifiedPCID', 'Modifiedpcid:') !!}
    {!! Form::text('modifiedPCID', null, ['class' => 'form-control']) !!}
</div>

<!-- Modifieduserid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('modifiedUserID', 'Modifieduserid:') !!}
    {!! Form::text('modifiedUserID', null, ['class' => 'form-control']) !!}
</div>

<!-- Modifieddatetime Field -->
<div class="form-group col-sm-6">
    {!! Form::label('modifiedDateTime', 'Modifieddatetime:') !!}
    {!! Form::date('modifiedDateTime', null, ['class' => 'form-control']) !!}
</div>

<!-- Modifiedusername Field -->
<div class="form-group col-sm-6">
    {!! Form::label('modifiedUserName', 'Modifiedusername:') !!}
    {!! Form::text('modifiedUserName', null, ['class' => 'form-control']) !!}
</div>

<!-- Timestamp Field -->
<div class="form-group col-sm-6">
    {!! Form::label('timestamp', 'Timestamp:') !!}
    {!! Form::date('timestamp', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('taxes.index') !!}" class="btn btn-default">Cancel</a>
</div>
