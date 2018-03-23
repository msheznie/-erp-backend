<!-- Rolldescription Field -->
<div class="form-group col-sm-6">
    {!! Form::label('rollDescription', 'Rolldescription:') !!}
    {!! Form::text('rollDescription', null, ['class' => 'form-control']) !!}
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

<!-- Departmentsystemid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('departmentSystemID', 'Departmentsystemid:') !!}
    {!! Form::number('departmentSystemID', null, ['class' => 'form-control']) !!}
</div>

<!-- Departmentid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('departmentID', 'Departmentid:') !!}
    {!! Form::text('departmentID', null, ['class' => 'form-control']) !!}
</div>

<!-- Servicelinesystemid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('serviceLineSystemID', 'Servicelinesystemid:') !!}
    {!! Form::number('serviceLineSystemID', null, ['class' => 'form-control']) !!}
</div>

<!-- Servicelineid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('serviceLineID', 'Servicelineid:') !!}
    {!! Form::text('serviceLineID', null, ['class' => 'form-control']) !!}
</div>

<!-- Rolllevel Field -->
<div class="form-group col-sm-6">
    {!! Form::label('rollLevel', 'Rolllevel:') !!}
    {!! Form::number('rollLevel', null, ['class' => 'form-control']) !!}
</div>

<!-- Approvallevelid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('approvalLevelID', 'Approvallevelid:') !!}
    {!! Form::number('approvalLevelID', null, ['class' => 'form-control']) !!}
</div>

<!-- Approvalgroupid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('approvalGroupID', 'Approvalgroupid:') !!}
    {!! Form::number('approvalGroupID', null, ['class' => 'form-control']) !!}
</div>

<!-- Timestamp Field -->
<div class="form-group col-sm-6">
    {!! Form::label('timeStamp', 'Timestamp:') !!}
    {!! Form::date('timeStamp', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('approvalRoles.index') !!}" class="btn btn-default">Cancel</a>
</div>
