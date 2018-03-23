<!-- Yesno Field -->
<div class="form-group col-sm-6">
    {!! Form::label('YesNo', 'Yesno:') !!}
    {!! Form::text('YesNo', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('yesNoSelections.index') !!}" class="btn btn-default">Cancel</a>
</div>
