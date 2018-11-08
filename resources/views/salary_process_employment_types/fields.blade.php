<!-- Salaryprocessid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('salaryProcessID', 'Salaryprocessid:') !!}
    {!! Form::number('salaryProcessID', null, ['class' => 'form-control']) !!}
</div>

<!-- Emptype Field -->
<div class="form-group col-sm-6">
    {!! Form::label('empType', 'Emptype:') !!}
    {!! Form::number('empType', null, ['class' => 'form-control']) !!}
</div>

<!-- Periodid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('periodID', 'Periodid:') !!}
    {!! Form::number('periodID', null, ['class' => 'form-control']) !!}
</div>

<!-- Companyid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('companyID', 'Companyid:') !!}
    {!! Form::text('companyID', null, ['class' => 'form-control']) !!}
</div>

<!-- Timestamp Field -->
<div class="form-group col-sm-6">
    {!! Form::label('timestamp', 'Timestamp:') !!}
    {!! Form::date('timestamp', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('salaryProcessEmploymentTypes.index') !!}" class="btn btn-default">Cancel</a>
</div>
