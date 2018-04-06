<!-- Employeesystemid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('employeeSystemID', 'Employeesystemid:') !!}
    {!! Form::number('employeeSystemID', null, ['class' => 'form-control']) !!}
</div>

<!-- Employeeid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('employeeID', 'Employeeid:') !!}
    {!! Form::text('employeeID', null, ['class' => 'form-control']) !!}
</div>

<!-- Employeegroupid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('employeeGroupID', 'Employeegroupid:') !!}
    {!! Form::number('employeeGroupID', null, ['class' => 'form-control']) !!}
</div>

<!-- Companysystemid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('companySystemID', 'Companysystemid:') !!}
    {!! Form::number('companySystemID', null, ['class' => 'form-control']) !!}
</div>

<!-- Companyid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('companyId', 'Companyid:') !!}
    {!! Form::text('companyId', null, ['class' => 'form-control']) !!}
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

<!-- Departmentid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('departmentID', 'Departmentid:') !!}
    {!! Form::text('departmentID', null, ['class' => 'form-control']) !!}
</div>

<!-- Servicelinesystemid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('ServiceLineSystemID', 'Servicelinesystemid:') !!}
    {!! Form::number('ServiceLineSystemID', null, ['class' => 'form-control']) !!}
</div>

<!-- Servicelineid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('ServiceLineID', 'Servicelineid:') !!}
    {!! Form::text('ServiceLineID', null, ['class' => 'form-control']) !!}
</div>

<!-- Warehousesystemcode Field -->
<div class="form-group col-sm-6">
    {!! Form::label('warehouseSystemCode', 'Warehousesystemcode:') !!}
    {!! Form::number('warehouseSystemCode', null, ['class' => 'form-control']) !!}
</div>

<!-- Reportingmanagerid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('reportingManagerID', 'Reportingmanagerid:') !!}
    {!! Form::text('reportingManagerID', null, ['class' => 'form-control']) !!}
</div>

<!-- Isdefault Field -->
<div class="form-group col-sm-6">
    {!! Form::label('isDefault', 'Isdefault:') !!}
    {!! Form::number('isDefault', null, ['class' => 'form-control']) !!}
</div>

<!-- Dischargedyn Field -->
<div class="form-group col-sm-6">
    {!! Form::label('dischargedYN', 'Dischargedyn:') !!}
    {!! Form::number('dischargedYN', null, ['class' => 'form-control']) !!}
</div>

<!-- Approvaldeligated Field -->
<div class="form-group col-sm-6">
    {!! Form::label('approvalDeligated', 'Approvaldeligated:') !!}
    {!! Form::number('approvalDeligated', null, ['class' => 'form-control']) !!}
</div>

<!-- Approvaldeligatedfromempid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('approvalDeligatedFromEmpID', 'Approvaldeligatedfromempid:') !!}
    {!! Form::text('approvalDeligatedFromEmpID', null, ['class' => 'form-control']) !!}
</div>

<!-- Approvaldeligatedfrom Field -->
<div class="form-group col-sm-6">
    {!! Form::label('approvalDeligatedFrom', 'Approvaldeligatedfrom:') !!}
    {!! Form::text('approvalDeligatedFrom', null, ['class' => 'form-control']) !!}
</div>

<!-- Approvaldeligatedto Field -->
<div class="form-group col-sm-6">
    {!! Form::label('approvalDeligatedTo', 'Approvaldeligatedto:') !!}
    {!! Form::text('approvalDeligatedTo', null, ['class' => 'form-control']) !!}
</div>

<!-- Dmsisuploadenable Field -->
<div class="form-group col-sm-6">
    {!! Form::label('dmsIsUploadEnable', 'Dmsisuploadenable:') !!}
    {!! Form::number('dmsIsUploadEnable', null, ['class' => 'form-control']) !!}
</div>

<!-- Timestamp Field -->
<div class="form-group col-sm-6">
    {!! Form::label('timeStamp', 'Timestamp:') !!}
    {!! Form::date('timeStamp', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('employeesDepartments.index') !!}" class="btn btn-default">Cancel</a>
</div>
