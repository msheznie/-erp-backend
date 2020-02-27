<!-- Jvmasterautoid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('jvMasterAutoId', 'Jvmasterautoid:') !!}
    {!! Form::number('jvMasterAutoId', null, ['class' => 'form-control']) !!}
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

<!-- Percentage Field -->
<div class="form-group col-sm-6">
    {!! Form::label('percentage', 'Percentage:') !!}
    {!! Form::number('percentage', null, ['class' => 'form-control']) !!}
</div>

<!-- Productlineid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('productLineID', 'Productlineid:') !!}
    {!! Form::number('productLineID', null, ['class' => 'form-control']) !!}
</div>

<!-- Productlinecode Field -->
<div class="form-group col-sm-6">
    {!! Form::label('productLineCode', 'Productlinecode:') !!}
    {!! Form::text('productLineCode', null, ['class' => 'form-control']) !!}
</div>

<!-- Allocationmaid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('allocationmaid', 'Allocationmaid:') !!}
    {!! Form::number('allocationmaid', null, ['class' => 'form-control']) !!}
</div>

<!-- Companysystemid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('companySystemID', 'Companysystemid:') !!}
    {!! Form::number('companySystemID', null, ['class' => 'form-control']) !!}
</div>

<!-- Companyid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('companyid', 'Companyid:') !!}
    {!! Form::text('companyid', null, ['class' => 'form-control']) !!}
</div>

<!-- Chartofaccountallocationmasterid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('chartOfAccountAllocationMasterID', 'Chartofaccountallocationmasterid:') !!}
    {!! Form::number('chartOfAccountAllocationMasterID', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('chartOfAccountAllocationDetailHistories.index') !!}" class="btn btn-default">Cancel</a>
</div>
