<!-- Policydescription Field -->
<div class="form-group col-sm-6">
    {!! Form::label('policyDescription', 'Policydescription:') !!}
    {!! Form::text('policyDescription', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('insurancePolicyTypes.index') !!}" class="btn btn-default">Cancel</a>
</div>
