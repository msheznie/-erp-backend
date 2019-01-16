<!-- Currencyid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('currencyID', 'Currencyid:') !!}
    {!! Form::number('currencyID', null, ['class' => 'form-control']) !!}
</div>

<!-- Currencycode Field -->
<div class="form-group col-sm-6">
    {!! Form::label('currencyCode', 'Currencycode:') !!}
    {!! Form::text('currencyCode', null, ['class' => 'form-control']) !!}
</div>

<!-- Amount Field -->
<div class="form-group col-sm-6">
    {!! Form::label('amount', 'Amount:') !!}
    {!! Form::number('amount', null, ['class' => 'form-control']) !!}
</div>

<!-- Value Field -->
<div class="form-group col-sm-6">
    {!! Form::label('value', 'Value:') !!}
    {!! Form::number('value', null, ['class' => 'form-control']) !!}
</div>

<!-- Isnote Field -->
<div class="form-group col-sm-6">
    {!! Form::label('isNote', 'Isnote:') !!}
    <label class="checkbox-inline">
        {!! Form::hidden('isNote', false) !!}
        {!! Form::checkbox('isNote', '1', null) !!} 1
    </label>
</div>

<!-- Caption Field -->
<div class="form-group col-sm-6">
    {!! Form::label('caption', 'Caption:') !!}
    {!! Form::text('caption', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('currencyDenominations.index') !!}" class="btn btn-default">Cancel</a>
</div>
