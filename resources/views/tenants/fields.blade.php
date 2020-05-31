<!-- Name Field -->
<div class="form-group col-sm-6">
    {!! Form::label('name', 'Name:') !!}
    {!! Form::text('name', null, ['class' => 'form-control']) !!}
</div>

<!-- Sub Domain Field -->
<div class="form-group col-sm-6">
    {!! Form::label('sub_domain', 'Sub Domain:') !!}
    {!! Form::text('sub_domain', null, ['class' => 'form-control']) !!}
</div>

<!-- Database Field -->
<div class="form-group col-sm-6">
    {!! Form::label('database', 'Database:') !!}
    {!! Form::text('database', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{{ route('tenants.index') }}" class="btn btn-default">Cancel</a>
</div>
