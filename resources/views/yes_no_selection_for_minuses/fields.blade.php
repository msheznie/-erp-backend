<!-- Selection Field -->
<div class="form-group col-sm-6">
    {!! Form::label('selection', 'Selection:') !!}
    {!! Form::text('selection', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('yesNoSelectionForMinuses.index') !!}" class="btn btn-default">Cancel</a>
</div>
