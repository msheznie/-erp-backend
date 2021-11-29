<!-- Description Field -->
<div class="form-group col-sm-6">
    {!! Form::label('description', 'Description:') !!}
    {!! Form::text('description', null, ['class' => 'form-control']) !!}
</div>

<!-- Isactive Field -->
<div class="form-group col-sm-6">
    {!! Form::label('isActive', 'Isactive:') !!}
    <label class="checkbox-inline">
        {!! Form::hidden('isActive', 0) !!}
        {!! Form::checkbox('isActive', '1', null) !!}
    </label>
</div>


<!-- Isdefault Field -->
<div class="form-group col-sm-6">
    {!! Form::label('isDefault', 'Isdefault:') !!}
    <label class="checkbox-inline">
        {!! Form::hidden('isDefault', 0) !!}
        {!! Form::checkbox('isDefault', '1', null) !!}
    </label>
</div>


<!-- Createddatetime Field -->
<div class="form-group col-sm-6">
    {!! Form::label('createdDateTime', 'Createddatetime:') !!}
    {!! Form::date('createdDateTime', null, ['class' => 'form-control','id'=>'createdDateTime']) !!}
</div>

@section('scripts')
    <script type="text/javascript">
        $('#createdDateTime').datetimepicker({
            format: 'YYYY-MM-DD HH:mm:ss',
            useCurrent: false
        })
    </script>
@endsection

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{{ route('poCategories.index') }}" class="btn btn-default">Cancel</a>
</div>
