<!-- Departmentdescription Field -->
<div class="form-group col-sm-6">
    {!! Form::label('DepartmentDescription', 'Departmentdescription:') !!}
    {!! Form::text('DepartmentDescription', null, ['class' => 'form-control']) !!}
</div>

<!-- Isactive Field -->
<div class="form-group col-sm-6">
    {!! Form::label('isActive', 'Isactive:') !!}
    {!! Form::number('isActive', null, ['class' => 'form-control']) !!}
</div>

<!-- Servicelinecode Field -->
<div class="form-group col-sm-6">
    {!! Form::label('ServiceLineCode', 'Servicelinecode:') !!}
    {!! Form::text('ServiceLineCode', null, ['class' => 'form-control']) !!}
</div>

<!-- Companyid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('CompanyID', 'Companyid:') !!}
    {!! Form::text('CompanyID', null, ['class' => 'form-control']) !!}
</div>

<!-- Showincombo Field -->
<div class="form-group col-sm-6">
    {!! Form::label('showInCombo', 'Showincombo:') !!}
    {!! Form::number('showInCombo', null, ['class' => 'form-control']) !!}
</div>

<!-- Timestamp Field -->
<div class="form-group col-sm-6">
    {!! Form::label('timestamp', 'Timestamp:') !!}
    {!! Form::date('timestamp', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('hRMSDepartmentMasters.index') !!}" class="btn btn-default">Cancel</a>
</div>
