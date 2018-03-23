<!-- Customercodesystem Field -->
<div class="form-group col-sm-6">
    {!! Form::label('customerCodeSystem', 'Customercodesystem:') !!}
    {!! Form::number('customerCodeSystem', null, ['class' => 'form-control']) !!}
</div>

<!-- Customercode Field -->
<div class="form-group col-sm-6">
    {!! Form::label('customerCode', 'Customercode:') !!}
    {!! Form::text('customerCode', null, ['class' => 'form-control']) !!}
</div>

<!-- Currencyid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('currencyID', 'Currencyid:') !!}
    {!! Form::number('currencyID', null, ['class' => 'form-control']) !!}
</div>

<!-- Isdefault Field -->
<div class="form-group col-sm-6">
    {!! Form::label('isDefault', 'Isdefault:') !!}
    {!! Form::number('isDefault', null, ['class' => 'form-control']) !!}
</div>

<!-- Isassigned Field -->
<div class="form-group col-sm-6">
    {!! Form::label('isAssigned', 'Isassigned:') !!}
    {!! Form::number('isAssigned', null, ['class' => 'form-control']) !!}
</div>

<!-- Createdby Field -->
<div class="form-group col-sm-6">
    {!! Form::label('createdBy', 'Createdby:') !!}
    {!! Form::text('createdBy', null, ['class' => 'form-control']) !!}
</div>

<!-- Createddatetime Field -->
<div class="form-group col-sm-6">
    {!! Form::label('createdDateTime', 'Createddatetime:') !!}
    {!! Form::date('createdDateTime', null, ['class' => 'form-control']) !!}
</div>

<!-- Timestamp Field -->
<div class="form-group col-sm-6">
    {!! Form::label('timestamp', 'Timestamp:') !!}
    {!! Form::date('timestamp', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('customerCurrencies.index') !!}" class="btn btn-default">Cancel</a>
</div>
