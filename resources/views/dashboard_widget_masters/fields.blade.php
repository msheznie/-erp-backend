<!-- Widgetmastername Field -->
<div class="form-group col-sm-6">
    {!! Form::label('WidgetMasterName', 'Widgetmastername:') !!}
    {!! Form::text('WidgetMasterName', null, ['class' => 'form-control']) !!}
</div>

<!-- Departmentid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('departmentID', 'Departmentid:') !!}
    {!! Form::number('departmentID', null, ['class' => 'form-control']) !!}
</div>

<!-- Sortorder Field -->
<div class="form-group col-sm-6">
    {!! Form::label('sortOrder', 'Sortorder:') !!}
    {!! Form::text('sortOrder', null, ['class' => 'form-control']) !!}
</div>

<!-- Widgetmastericon Field -->
<div class="form-group col-sm-6">
    {!! Form::label('widgetMasterIcon', 'Widgetmastericon:') !!}
    {!! Form::text('widgetMasterIcon', null, ['class' => 'form-control']) !!}
</div>

<!-- Isactive Field -->
<div class="form-group col-sm-6">
    {!! Form::label('isActive', 'Isactive:') !!}
    {!! Form::number('isActive', null, ['class' => 'form-control']) !!}
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
    <a href="{!! route('dashboardWidgetMasters.index') !!}" class="btn btn-default">Cancel</a>
</div>
