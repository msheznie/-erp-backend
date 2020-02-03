<!-- Empid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('empID', 'Empid:') !!}
    {!! Form::text('empID', null, ['class' => 'form-control']) !!}
</div>

<!-- Employeesystemid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('employeeSystemID', 'Employeesystemid:') !!}
    {!! Form::number('employeeSystemID', null, ['class' => 'form-control']) !!}
</div>

<!-- Companyid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('companyID', 'Companyid:') !!}
    {!! Form::text('companyID', null, ['class' => 'form-control']) !!}
</div>

<!-- Islptreview Field -->
<div class="form-group col-sm-6">
    {!! Form::label('isLPTReview', 'Islptreview:') !!}
    {!! Form::number('isLPTReview', null, ['class' => 'form-control']) !!}
</div>

<!-- Islptclose Field -->
<div class="form-group col-sm-6">
    {!! Form::label('isLPTClose', 'Islptclose:') !!}
    {!! Form::number('isLPTClose', null, ['class' => 'form-control']) !!}
</div>

<!-- Createdby Field -->
<div class="form-group col-sm-6">
    {!! Form::label('createdBy', 'Createdby:') !!}
    {!! Form::text('createdBy', null, ['class' => 'form-control']) !!}
</div>

<!-- Timestamp Field -->
<div class="form-group col-sm-6">
    {!! Form::label('timestamp', 'Timestamp:') !!}
    {!! Form::date('timestamp', null, ['class' => 'form-control','id'=>'timestamp']) !!}
</div>

@section('scripts')
    <script type="text/javascript">
        $('#timestamp').datetimepicker({
            format: 'YYYY-MM-DD HH:mm:ss',
            useCurrent: false
        })
    </script>
@endsection

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('lptPermissions.index') !!}" class="btn btn-default">Cancel</a>
</div>
