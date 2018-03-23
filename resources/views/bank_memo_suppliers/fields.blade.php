<!-- Memoheader Field -->
<div class="form-group col-sm-6">
    {!! Form::label('memoHeader', 'Memoheader:') !!}
    {!! Form::text('memoHeader', null, ['class' => 'form-control']) !!}
</div>

<!-- Memodetail Field -->
<div class="form-group col-sm-6">
    {!! Form::label('memoDetail', 'Memodetail:') !!}
    {!! Form::text('memoDetail', null, ['class' => 'form-control']) !!}
</div>

<!-- Suppliercodesystem Field -->
<div class="form-group col-sm-6">
    {!! Form::label('supplierCodeSystem', 'Suppliercodesystem:') !!}
    {!! Form::number('supplierCodeSystem', null, ['class' => 'form-control']) !!}
</div>

<!-- Suppliercurrencyid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('supplierCurrencyID', 'Suppliercurrencyid:') !!}
    {!! Form::number('supplierCurrencyID', null, ['class' => 'form-control']) !!}
</div>

<!-- Updatedbyuserid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('updatedByUserID', 'Updatedbyuserid:') !!}
    {!! Form::text('updatedByUserID', null, ['class' => 'form-control']) !!}
</div>

<!-- Updatedbyusername Field -->
<div class="form-group col-sm-6">
    {!! Form::label('updatedByUserName', 'Updatedbyusername:') !!}
    {!! Form::text('updatedByUserName', null, ['class' => 'form-control']) !!}
</div>

<!-- Updateddate Field -->
<div class="form-group col-sm-6">
    {!! Form::label('updatedDate', 'Updateddate:') !!}
    {!! Form::date('updatedDate', null, ['class' => 'form-control']) !!}
</div>

<!-- Timestamp Field -->
<div class="form-group col-sm-6">
    {!! Form::label('timestamp', 'Timestamp:') !!}
    {!! Form::date('timestamp', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('bankMemoSuppliers.index') !!}" class="btn btn-default">Cancel</a>
</div>
