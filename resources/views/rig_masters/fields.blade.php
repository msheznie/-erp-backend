<!-- Rigdescription Field -->
<div class="form-group col-sm-6">
    {!! Form::label('RigDescription', 'Rigdescription:') !!}
    {!! Form::text('RigDescription', null, ['class' => 'form-control']) !!}
</div>

<!-- Companyid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('companyID', 'Companyid:') !!}
    {!! Form::text('companyID', null, ['class' => 'form-control']) !!}
</div>

<!-- Oldid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('oldID', 'Oldid:') !!}
    {!! Form::number('oldID', null, ['class' => 'form-control']) !!}
</div>

<!-- Isrig Field -->
<div class="form-group col-sm-6">
    {!! Form::label('isRig', 'Isrig:') !!}
    {!! Form::number('isRig', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('rigMasters.index') !!}" class="btn btn-default">Cancel</a>
</div>
