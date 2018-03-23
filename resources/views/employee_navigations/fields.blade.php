<!-- Empid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('empID', 'Empid:') !!}
    {!! Form::text('empID', null, ['class' => 'form-control']) !!}
</div>

<!-- Usergroupid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('userGroupID', 'Usergroupid:') !!}
    {!! Form::number('userGroupID', null, ['class' => 'form-control']) !!}
</div>

<!-- Companyid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('companyID', 'Companyid:') !!}
    {!! Form::number('companyID', null, ['class' => 'form-control']) !!}
</div>

<!-- Timestamp Field -->
<div class="form-group col-sm-6">
    {!! Form::label('timestamp', 'Timestamp:') !!}
    {!! Form::date('timestamp', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('employeeNavigations.index') !!}" class="btn btn-default">Cancel</a>
</div>
