<!-- Employeeid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('employeeID', 'Employeeid:') !!}
    {!! Form::text('employeeID', null, ['class' => 'form-control']) !!}
</div>

<!-- Groupmasterid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('groupMasterID', 'Groupmasterid:') !!}
    {!! Form::number('groupMasterID', null, ['class' => 'form-control']) !!}
</div>

<!-- Pagemasterid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('pageMasterID', 'Pagemasterid:') !!}
    {!! Form::number('pageMasterID', null, ['class' => 'form-control']) !!}
</div>

<!-- Modulemasterid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('moduleMasterID', 'Modulemasterid:') !!}
    {!! Form::number('moduleMasterID', null, ['class' => 'form-control']) !!}
</div>

<!-- Companyid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('companyID', 'Companyid:') !!}
    {!! Form::text('companyID', null, ['class' => 'form-control']) !!}
</div>

<!-- V Field -->
<div class="form-group col-sm-6">
    {!! Form::label('V', 'V:') !!}
    {!! Form::number('V', null, ['class' => 'form-control']) !!}
</div>

<!-- A Field -->
<div class="form-group col-sm-6">
    {!! Form::label('A', 'A:') !!}
    {!! Form::number('A', null, ['class' => 'form-control']) !!}
</div>

<!-- E Field -->
<div class="form-group col-sm-6">
    {!! Form::label('E', 'E:') !!}
    {!! Form::number('E', null, ['class' => 'form-control']) !!}
</div>

<!-- D Field -->
<div class="form-group col-sm-6">
    {!! Form::label('D', 'D:') !!}
    {!! Form::number('D', null, ['class' => 'form-control']) !!}
</div>

<!-- P Field -->
<div class="form-group col-sm-6">
    {!! Form::label('P', 'P:') !!}
    {!! Form::number('P', null, ['class' => 'form-control']) !!}
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
    <a href="{!! route('userRights.index') !!}" class="btn btn-default">Cancel</a>
</div>
