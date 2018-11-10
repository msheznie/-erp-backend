<!-- Periodmonth Field -->
<div class="form-group col-sm-6">
    {!! Form::label('periodMonth', 'Periodmonth:') !!}
    {!! Form::text('periodMonth', null, ['class' => 'form-control']) !!}
</div>

<!-- Periodyear Field -->
<div class="form-group col-sm-6">
    {!! Form::label('periodYear', 'Periodyear:') !!}
    {!! Form::number('periodYear', null, ['class' => 'form-control']) !!}
</div>

<!-- Clientmonth Field -->
<div class="form-group col-sm-6">
    {!! Form::label('clientMonth', 'Clientmonth:') !!}
    {!! Form::text('clientMonth', null, ['class' => 'form-control']) !!}
</div>

<!-- Clientstartdate Field -->
<div class="form-group col-sm-6">
    {!! Form::label('clientStartDate', 'Clientstartdate:') !!}
    {!! Form::text('clientStartDate', null, ['class' => 'form-control']) !!}
</div>

<!-- Clientenddate Field -->
<div class="form-group col-sm-6">
    {!! Form::label('clientEndDate', 'Clientenddate:') !!}
    {!! Form::text('clientEndDate', null, ['class' => 'form-control']) !!}
</div>

<!-- Noofdays Field -->
<div class="form-group col-sm-6">
    {!! Form::label('noOfDays', 'Noofdays:') !!}
    {!! Form::number('noOfDays', null, ['class' => 'form-control']) !!}
</div>

<!-- Startdate Field -->
<div class="form-group col-sm-6">
    {!! Form::label('startDate', 'Startdate:') !!}
    {!! Form::date('startDate', null, ['class' => 'form-control']) !!}
</div>

<!-- Enddate Field -->
<div class="form-group col-sm-6">
    {!! Form::label('endDate', 'Enddate:') !!}
    {!! Form::date('endDate', null, ['class' => 'form-control']) !!}
</div>

<!-- Timestamp Field -->
<div class="form-group col-sm-6">
    {!! Form::label('timestamp', 'Timestamp:') !!}
    {!! Form::date('timestamp', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('periodMasters.index') !!}" class="btn btn-default">Cancel</a>
</div>
