<!-- Documentid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('documentID', 'Documentid:') !!}
    {!! Form::text('documentID', null, ['class' => 'form-control']) !!}
</div>

<!-- Documentdescription Field -->
<div class="form-group col-sm-6">
    {!! Form::label('documentDescription', 'Documentdescription:') !!}
    {!! Form::text('documentDescription', null, ['class' => 'form-control']) !!}
</div>

<!-- Departmentid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('departmentID', 'Departmentid:') !!}
    {!! Form::text('departmentID', null, ['class' => 'form-control']) !!}
</div>

<!-- Timestamp Field -->
<div class="form-group col-sm-6">
    {!! Form::label('timeStamp', 'Timestamp:') !!}
    {!! Form::date('timeStamp', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('documentMasters.index') !!}" class="btn btn-default">Cancel</a>
</div>
