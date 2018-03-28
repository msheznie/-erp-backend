<!-- Companypolicycategoryid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('companyPolicyCategoryID', 'Companypolicycategoryid:') !!}
    {!! Form::number('companyPolicyCategoryID', null, ['class' => 'form-control']) !!}
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

<!-- Documentid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('documentID', 'Documentid:') !!}
    {!! Form::text('documentID', null, ['class' => 'form-control']) !!}
</div>

<!-- Isyesno Field -->
<div class="form-group col-sm-6">
    {!! Form::label('isYesNO', 'Isyesno:') !!}
    {!! Form::number('isYesNO', null, ['class' => 'form-control']) !!}
</div>

<!-- Policyvalue Field -->
<div class="form-group col-sm-6">
    {!! Form::label('policyValue', 'Policyvalue:') !!}
    {!! Form::number('policyValue', null, ['class' => 'form-control']) !!}
</div>

<!-- Createdbyuserid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('createdByUserID', 'Createdbyuserid:') !!}
    {!! Form::text('createdByUserID', null, ['class' => 'form-control']) !!}
</div>

<!-- Createdbyusername Field -->
<div class="form-group col-sm-6">
    {!! Form::label('createdByUserName', 'Createdbyusername:') !!}
    {!! Form::text('createdByUserName', null, ['class' => 'form-control']) !!}
</div>

<!-- Createdbypcid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('createdByPCID', 'Createdbypcid:') !!}
    {!! Form::text('createdByPCID', null, ['class' => 'form-control']) !!}
</div>

<!-- Modifiedbyuserid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('modifiedByUserID', 'Modifiedbyuserid:') !!}
    {!! Form::text('modifiedByUserID', null, ['class' => 'form-control']) !!}
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
    <a href="{!! route('companyPolicyMasters.index') !!}" class="btn btn-default">Cancel</a>
</div>
