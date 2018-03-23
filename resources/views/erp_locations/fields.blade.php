<!-- Locationname Field -->
<div class="form-group col-sm-6">
    {!! Form::label('locationName', 'Locationname:') !!}
    {!! Form::text('locationName', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('erpLocations.index') !!}" class="btn btn-default">Cancel</a>
</div>
