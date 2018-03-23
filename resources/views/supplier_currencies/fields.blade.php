<!-- Suppliercodesystem Field -->
<div class="form-group col-sm-6">
    {!! Form::label('supplierCodeSystem', 'Suppliercodesystem:') !!}
    {!! Form::number('supplierCodeSystem', null, ['class' => 'form-control']) !!}
</div>

<!-- Currencyid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('currencyID', 'Currencyid:') !!}
    {!! Form::number('currencyID', null, ['class' => 'form-control']) !!}
</div>

<!-- Bankmemo Field -->
<div class="form-group col-sm-12 col-lg-12">
    {!! Form::label('bankMemo', 'Bankmemo:') !!}
    {!! Form::textarea('bankMemo', null, ['class' => 'form-control']) !!}
</div>

<!-- Timestamp Field -->
<div class="form-group col-sm-6">
    {!! Form::label('timestamp', 'Timestamp:') !!}
    {!! Form::date('timestamp', null, ['class' => 'form-control']) !!}
</div>

<!-- Isassigned Field -->
<div class="form-group col-sm-6">
    {!! Form::label('isAssigned', 'Isassigned:') !!}
    {!! Form::number('isAssigned', null, ['class' => 'form-control']) !!}
</div>

<!-- Isdefault Field -->
<div class="form-group col-sm-6">
    {!! Form::label('isDefault', 'Isdefault:') !!}
    {!! Form::number('isDefault', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('supplierCurrencies.index') !!}" class="btn btn-default">Cancel</a>
</div>
