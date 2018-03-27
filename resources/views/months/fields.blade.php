<!-- Monthdes Field -->
<div class="form-group col-sm-6">
    {!! Form::label('monthDes', 'Monthdes:') !!}
    {!! Form::text('monthDes', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('months.index') !!}" class="btn btn-default">Cancel</a>
</div>
