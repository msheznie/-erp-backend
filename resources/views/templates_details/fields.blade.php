<!-- Templatesmasterautoid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('templatesMasterAutoID', 'Templatesmasterautoid:') !!}
    {!! Form::number('templatesMasterAutoID', null, ['class' => 'form-control']) !!}
</div>

<!-- Templatedetaildescription Field -->
<div class="form-group col-sm-6">
    {!! Form::label('templateDetailDescription', 'Templatedetaildescription:') !!}
    {!! Form::text('templateDetailDescription', null, ['class' => 'form-control']) !!}
</div>

<!-- Controlaccountid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('controlAccountID', 'Controlaccountid:') !!}
    {!! Form::text('controlAccountID', null, ['class' => 'form-control']) !!}
</div>

<!-- Controlaccountsubid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('controlAccountSubID', 'Controlaccountsubid:') !!}
    {!! Form::number('controlAccountSubID', null, ['class' => 'form-control']) !!}
</div>

<!-- Sortorder Field -->
<div class="form-group col-sm-6">
    {!! Form::label('sortOrder', 'Sortorder:') !!}
    {!! Form::number('sortOrder', null, ['class' => 'form-control']) !!}
</div>

<!-- Cashflowid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('cashflowid', 'Cashflowid:') !!}
    {!! Form::number('cashflowid', null, ['class' => 'form-control']) !!}
</div>

<!-- Timestamp Field -->
<div class="form-group col-sm-6">
    {!! Form::label('timestamp', 'Timestamp:') !!}
    {!! Form::date('timestamp', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('templatesDetails.index') !!}" class="btn btn-default">Cancel</a>
</div>
