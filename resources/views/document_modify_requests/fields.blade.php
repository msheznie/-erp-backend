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

<!-- Companysystemid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('companySystemID', 'Companysystemid:') !!}
    {!! Form::number('companySystemID', null, ['class' => 'form-control']) !!}
</div>

<!-- Document Master Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('document_master_id', 'Document Master Id:') !!}
    {!! Form::number('document_master_id', null, ['class' => 'form-control']) !!}
</div>

<!-- Documentsystemcode Field -->
<div class="form-group col-sm-6">
    {!! Form::label('documentSystemCode', 'Documentsystemcode:') !!}
    {!! Form::number('documentSystemCode', null, ['class' => 'form-control']) !!}
</div>

<!-- Rejected Field -->
<div class="form-group col-sm-6">
    {!! Form::label('rejected', 'Rejected:') !!}
    <label class="checkbox-inline">
        {!! Form::hidden('rejected', 0) !!}
        {!! Form::checkbox('rejected', '1', null) !!}
    </label>
</div>


<!-- Rejected By User System Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('rejected_by_user_system_id', 'Rejected By User System Id:') !!}
    {!! Form::number('rejected_by_user_system_id', null, ['class' => 'form-control']) !!}
</div>

<!-- Rejected Date Field -->
<div class="form-group col-sm-6">
    {!! Form::label('rejected_date', 'Rejected Date:') !!}
    {!! Form::date('rejected_date', null, ['class' => 'form-control','id'=>'rejected_date']) !!}
</div>

@section('scripts')
    <script type="text/javascript">
        $('#rejected_date').datetimepicker({
            format: 'YYYY-MM-DD HH:mm:ss',
            useCurrent: false
        })
    </script>
@endsection

<!-- Requested Date Field -->
<div class="form-group col-sm-6">
    {!! Form::label('requested_date', 'Requested Date:') !!}
    {!! Form::date('requested_date', null, ['class' => 'form-control','id'=>'requested_date']) !!}
</div>

@section('scripts')
    <script type="text/javascript">
        $('#requested_date').datetimepicker({
            format: 'YYYY-MM-DD HH:mm:ss',
            useCurrent: false
        })
    </script>
@endsection

<!-- Requested Document Master Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('requested_document_master_id', 'Requested Document Master Id:') !!}
    {!! Form::number('requested_document_master_id', null, ['class' => 'form-control']) !!}
</div>

<!-- Requested Employeesystemid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('requested_employeeSystemID', 'Requested Employeesystemid:') !!}
    {!! Form::number('requested_employeeSystemID', null, ['class' => 'form-control']) !!}
</div>

<!-- Rolllevforapp Curr Field -->
<div class="form-group col-sm-6">
    {!! Form::label('RollLevForApp_curr', 'Rolllevforapp Curr:') !!}
    {!! Form::number('RollLevForApp_curr', null, ['class' => 'form-control']) !!}
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
    <a href="{{ route('documentModifyRequests.index') }}" class="btn btn-default">Cancel</a>
</div>
