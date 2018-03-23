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

<!-- Servicelinewise Field -->
<div class="form-group col-sm-6">
    {!! Form::label('serviceLineWise', 'Servicelinewise:') !!}
    {!! Form::number('serviceLineWise', null, ['class' => 'form-control']) !!}
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

<!-- Leveldescription Field -->
<div class="form-group col-sm-6">
    {!! Form::label('levelDescription', 'Leveldescription:') !!}
    {!! Form::text('levelDescription', null, ['class' => 'form-control']) !!}
</div>

<!-- Nooflevels Field -->
<div class="form-group col-sm-6">
    {!! Form::label('noOfLevels', 'Nooflevels:') !!}
    {!! Form::number('noOfLevels', null, ['class' => 'form-control']) !!}
</div>

<!-- Valuewise Field -->
<div class="form-group col-sm-6">
    {!! Form::label('valueWise', 'Valuewise:') !!}
    {!! Form::number('valueWise', null, ['class' => 'form-control']) !!}
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

<!-- Iscategorywiseapproval Field -->
<div class="form-group col-sm-6">
    {!! Form::label('isCategoryWiseApproval', 'Iscategorywiseapproval:') !!}
    {!! Form::number('isCategoryWiseApproval', null, ['class' => 'form-control']) !!}
</div>

<!-- Categoryid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('categoryID', 'Categoryid:') !!}
    {!! Form::number('categoryID', null, ['class' => 'form-control']) !!}
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
    <a href="{!! route('approvalLevels.index') !!}" class="btn btn-default">Cancel</a>
</div>
