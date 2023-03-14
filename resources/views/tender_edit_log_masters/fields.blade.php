<!-- Approved Field -->
<div class="form-group col-sm-6">
    {!! Form::label('approved', 'Approved:') !!}
    <label class="checkbox-inline">
        {!! Form::hidden('approved', 0) !!}
        {!! Form::checkbox('approved', '1', null) !!}
    </label>
</div>


<!-- Approved By User System Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('approved_by_user_system_id', 'Approved By User System Id:') !!}
    {!! Form::number('approved_by_user_system_id', null, ['class' => 'form-control']) !!}
</div>

<!-- Approved Date Field -->
<div class="form-group col-sm-6">
    {!! Form::label('approved_date', 'Approved Date:') !!}
    {!! Form::date('approved_date', null, ['class' => 'form-control','id'=>'approved_date']) !!}
</div>

@section('scripts')
    <script type="text/javascript">
        $('#approved_date').datetimepicker({
            format: 'YYYY-MM-DD HH:mm:ss',
            useCurrent: false
        })
    </script>
@endsection

<!-- Companyid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('companyID', 'Companyid:') !!}
    {!! Form::text('companyID', null, ['class' => 'form-control']) !!}
</div>

<!-- Companysystemid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('companySystemID', 'Companysystemid:') !!}
    {!! Form::number('companySystemID', null, ['class' => 'form-control']) !!}
</div>

<!-- Departmentid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('departmentID', 'Departmentid:') !!}
    {!! Form::text('departmentID', null, ['class' => 'form-control']) !!}
</div>

<!-- Departmentsystemid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('departmentSystemID', 'Departmentsystemid:') !!}
    {!! Form::number('departmentSystemID', null, ['class' => 'form-control']) !!}
</div>

<!-- Description Field -->
<div class="form-group col-sm-6">
    {!! Form::label('description', 'Description:') !!}
    {!! Form::text('description', null, ['class' => 'form-control']) !!}
</div>

<!-- Documentcode Field -->
<div class="form-group col-sm-6">
    {!! Form::label('documentCode', 'Documentcode:') !!}
    {!! Form::text('documentCode', null, ['class' => 'form-control']) !!}
</div>

<!-- Documentsystemcode Field -->
<div class="form-group col-sm-6">
    {!! Form::label('documentSystemCode', 'Documentsystemcode:') !!}
    {!! Form::number('documentSystemCode', null, ['class' => 'form-control']) !!}
</div>

<!-- Employeeid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('employeeID', 'Employeeid:') !!}
    {!! Form::text('employeeID', null, ['class' => 'form-control']) !!}
</div>

<!-- Employeesystemid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('employeeSystemID', 'Employeesystemid:') !!}
    {!! Form::number('employeeSystemID', null, ['class' => 'form-control']) !!}
</div>

<!-- Status Field -->
<div class="form-group col-sm-6">
    {!! Form::label('status', 'Status:') !!}
    <label class="checkbox-inline">
        {!! Form::hidden('status', 0) !!}
        {!! Form::checkbox('status', '1', null) !!}
    </label>
</div>


<!-- Type Field -->
<div class="form-group col-sm-6">
    {!! Form::label('type', 'Type:') !!}
    <label class="checkbox-inline">
        {!! Form::hidden('type', 0) !!}
        {!! Form::checkbox('type', '1', null) !!}
    </label>
</div>


<!-- Version Field -->
<div class="form-group col-sm-6">
    {!! Form::label('version', 'Version:') !!}
    {!! Form::number('version', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{{ route('tenderEditLogMasters.index') }}" class="btn btn-default">Cancel</a>
</div>
