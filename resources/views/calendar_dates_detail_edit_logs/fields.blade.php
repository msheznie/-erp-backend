<!-- Calendar Date Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('calendar_date_id', 'Calendar Date Id:') !!}
    {!! Form::number('calendar_date_id', null, ['class' => 'form-control']) !!}
</div>

<!-- Company Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('company_id', 'Company Id:') !!}
    {!! Form::number('company_id', null, ['class' => 'form-control']) !!}
</div>

<!-- From Date Field -->
<div class="form-group col-sm-6">
    {!! Form::label('from_date', 'From Date:') !!}
    {!! Form::date('from_date', null, ['class' => 'form-control','id'=>'from_date']) !!}
</div>

@section('scripts')
    <script type="text/javascript">
        $('#from_date').datetimepicker({
            format: 'YYYY-MM-DD HH:mm:ss',
            useCurrent: false
        })
    </script>
@endsection

<!-- Master Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('master_id', 'Master Id:') !!}
    {!! Form::number('master_id', null, ['class' => 'form-control']) !!}
</div>

<!-- Modify Type Field -->
<div class="form-group col-sm-6">
    {!! Form::label('modify_type', 'Modify Type:') !!}
    {!! Form::number('modify_type', null, ['class' => 'form-control']) !!}
</div>

<!-- Ref Log Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('ref_log_id', 'Ref Log Id:') !!}
    {!! Form::number('ref_log_id', null, ['class' => 'form-control']) !!}
</div>

<!-- Tender Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('tender_id', 'Tender Id:') !!}
    {!! Form::number('tender_id', null, ['class' => 'form-control']) !!}
</div>

<!-- To Date Field -->
<div class="form-group col-sm-6">
    {!! Form::label('to_date', 'To Date:') !!}
    {!! Form::date('to_date', null, ['class' => 'form-control','id'=>'to_date']) !!}
</div>

@section('scripts')
    <script type="text/javascript">
        $('#to_date').datetimepicker({
            format: 'YYYY-MM-DD HH:mm:ss',
            useCurrent: false
        })
    </script>
@endsection

<!-- Version Id Field -->
<div class="form-group col-sm-6">
    {!! Form::label('version_id', 'Version Id:') !!}
    {!! Form::number('version_id', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{{ route('calendarDatesDetailEditLogs.index') }}" class="btn btn-default">Cancel</a>
</div>
