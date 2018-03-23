<!-- Countrycode Field -->
<div class="form-group col-sm-6">
    {!! Form::label('countryCode', 'Countrycode:') !!}
    {!! Form::text('countryCode', null, ['class' => 'form-control']) !!}
</div>

<!-- Countryname Field -->
<div class="form-group col-sm-6">
    {!! Form::label('countryName', 'Countryname:') !!}
    {!! Form::text('countryName', null, ['class' => 'form-control']) !!}
</div>

<!-- Countryname O Field -->
<div class="form-group col-sm-6">
    {!! Form::label('countryName_O', 'Countryname O:') !!}
    {!! Form::text('countryName_O', null, ['class' => 'form-control']) !!}
</div>

<!-- Nationality Field -->
<div class="form-group col-sm-6">
    {!! Form::label('nationality', 'Nationality:') !!}
    {!! Form::text('nationality', null, ['class' => 'form-control']) !!}
</div>

<!-- Islocal Field -->
<div class="form-group col-sm-6">
    {!! Form::label('isLocal', 'Islocal:') !!}
    {!! Form::number('isLocal', null, ['class' => 'form-control']) !!}
</div>

<!-- Countryflag Field -->
<div class="form-group col-sm-6">
    {!! Form::label('countryFlag', 'Countryflag:') !!}
    {!! Form::text('countryFlag', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('countryMasters.index') !!}" class="btn btn-default">Cancel</a>
</div>
