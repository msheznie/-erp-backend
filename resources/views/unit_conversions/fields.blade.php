<!-- Masterunitid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('masterUnitID', 'Masterunitid:') !!}
    {!! Form::number('masterUnitID', null, ['class' => 'form-control']) !!}
</div>

<!-- Subunitid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('subUnitID', 'Subunitid:') !!}
    {!! Form::number('subUnitID', null, ['class' => 'form-control']) !!}
</div>

<!-- Conversion Field -->
<div class="form-group col-sm-6">
    {!! Form::label('conversion', 'Conversion:') !!}
    {!! Form::number('conversion', null, ['class' => 'form-control']) !!}
</div>

<!-- Timestamp Field -->
<div class="form-group col-sm-6">
    {!! Form::label('timestamp', 'Timestamp:') !!}
    {!! Form::date('timestamp', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('unitConversions.index') !!}" class="btn btn-default">Cancel</a>
</div>
