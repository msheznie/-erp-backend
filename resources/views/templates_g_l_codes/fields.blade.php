<!-- Templatemasterid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('templateMasterID', 'Templatemasterid:') !!}
    {!! Form::number('templateMasterID', null, ['class' => 'form-control']) !!}
</div>

<!-- Templatesdetailsautoid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('templatesDetailsAutoID', 'Templatesdetailsautoid:') !!}
    {!! Form::number('templatesDetailsAutoID', null, ['class' => 'form-control']) !!}
</div>

<!-- Chartofaccountsystemid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('chartOfAccountSystemID', 'Chartofaccountsystemid:') !!}
    {!! Form::number('chartOfAccountSystemID', null, ['class' => 'form-control']) !!}
</div>

<!-- Glcode Field -->
<div class="form-group col-sm-6">
    {!! Form::label('glCode', 'Glcode:') !!}
    {!! Form::text('glCode', null, ['class' => 'form-control']) !!}
</div>

<!-- Gldescription Field -->
<div class="form-group col-sm-6">
    {!! Form::label('glDescription', 'Gldescription:') !!}
    {!! Form::text('glDescription', null, ['class' => 'form-control']) !!}
</div>

<!-- Timestamp Field -->
<div class="form-group col-sm-6">
    {!! Form::label('timestamp', 'Timestamp:') !!}
    {!! Form::date('timestamp', null, ['class' => 'form-control']) !!}
</div>

<!-- Erp Templatesglcodecol Field -->
<div class="form-group col-sm-6">
    {!! Form::label('erp_templatesglcodecol', 'Erp Templatesglcodecol:') !!}
    {!! Form::text('erp_templatesglcodecol', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('templatesGLCodes.index') !!}" class="btn btn-default">Cancel</a>
</div>
