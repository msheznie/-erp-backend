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

<!-- Employeesystemid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('employeeSystemID', 'Employeesystemid:') !!}
    {!! Form::number('employeeSystemID', null, ['class' => 'form-control']) !!}
</div>

<!-- Empid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('empID', 'Empid:') !!}
    {!! Form::text('empID', null, ['class' => 'form-control']) !!}
</div>

<!-- Sendyn Field -->
<div class="form-group col-sm-6">
    {!! Form::label('sendYN', 'Sendyn:') !!}
    {!! Form::number('sendYN', null, ['class' => 'form-control']) !!}
</div>

<!-- Emailnotificationid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('emailNotificationID', 'Emailnotificationid:') !!}
    {!! Form::number('emailNotificationID', null, ['class' => 'form-control']) !!}
</div>

<!-- Timestamp Field -->
<div class="form-group col-sm-6">
    {!! Form::label('timestamp', 'Timestamp:') !!}
    {!! Form::date('timestamp', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('documentEmailNotificationDetails.index') !!}" class="btn btn-default">Cancel</a>
</div>
