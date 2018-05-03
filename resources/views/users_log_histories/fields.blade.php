<!-- Employee Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('employee_id', 'Employee Id:') !!}
    {!! Form::number('employee_id', null, ['class' => 'form-control']) !!}
</div>

<!-- Empid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('empID', 'Empid:') !!}
    {!! Form::text('empID', null, ['class' => 'form-control']) !!}
</div>

<!-- Loginpcid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('loginPCId', 'Loginpcid:') !!}
    {!! Form::text('loginPCId', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('usersLogHistories.index') !!}" class="btn btn-default">Cancel</a>
</div>
