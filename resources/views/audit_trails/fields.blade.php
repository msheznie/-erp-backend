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

<!-- Valuefrom Field -->
<div class="form-group col-sm-6">
    {!! Form::label('valueFrom', 'Valuefrom:') !!}
    {!! Form::number('valueFrom', null, ['class' => 'form-control']) !!}
</div>

<!-- Valueto Field -->
<div class="form-group col-sm-6">
    {!! Form::label('valueTo', 'Valueto:') !!}
    {!! Form::number('valueTo', null, ['class' => 'form-control']) !!}
</div>

<!-- Valuefromsystemid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('valueFromSystemID', 'Valuefromsystemid:') !!}
    {!! Form::number('valueFromSystemID', null, ['class' => 'form-control']) !!}
</div>

<!-- Valuefromtext Field -->
<div class="form-group col-sm-6">
    {!! Form::label('valueFromText', 'Valuefromtext:') !!}
    {!! Form::text('valueFromText', null, ['class' => 'form-control']) !!}
</div>

<!-- Valuetosystemid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('valueToSystemID', 'Valuetosystemid:') !!}
    {!! Form::number('valueToSystemID', null, ['class' => 'form-control']) !!}
</div>

<!-- Valuetotext Field -->
<div class="form-group col-sm-6">
    {!! Form::label('valueToText', 'Valuetotext:') !!}
    {!! Form::text('valueToText', null, ['class' => 'form-control']) !!}
</div>

<!-- Description Field -->
<div class="form-group col-sm-12 col-lg-12">
    {!! Form::label('description', 'Description:') !!}
    {!! Form::textarea('description', null, ['class' => 'form-control']) !!}
</div>

<!-- Modifiedusersystemid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('modifiedUserSystemID', 'Modifiedusersystemid:') !!}
    {!! Form::number('modifiedUserSystemID', null, ['class' => 'form-control']) !!}
</div>

<!-- Modifieduserid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('modifiedUserID', 'Modifieduserid:') !!}
    {!! Form::text('modifiedUserID', null, ['class' => 'form-control']) !!}
</div>

<!-- Modifieddate Field -->
<div class="form-group col-sm-6">
    {!! Form::label('modifiedDate', 'Modifieddate:') !!}
    {!! Form::date('modifiedDate', null, ['class' => 'form-control']) !!}
</div>

<!-- Timestamp Field -->
<div class="form-group col-sm-6">
    {!! Form::label('timestamp', 'Timestamp:') !!}
    {!! Form::date('timestamp', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('auditTrails.index') !!}" class="btn btn-default">Cancel</a>
</div>
