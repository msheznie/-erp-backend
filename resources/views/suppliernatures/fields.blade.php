<!-- Naturedescription Field -->
<div class="form-group col-sm-6">
    {!! Form::label('natureDescription', 'Naturedescription:') !!}
    {!! Form::text('natureDescription', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('suppliernatures.index') !!}" class="btn btn-default">Cancel</a>
</div>
