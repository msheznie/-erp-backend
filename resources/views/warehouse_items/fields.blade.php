<!-- Companysystemid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('companySystemID', 'Companysystemid:') !!}
    {!! Form::number('companySystemID', null, ['class' => 'form-control']) !!}
</div>

<!-- Companyid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('companyID', 'Companyid:') !!}
    {!! Form::text('companyID', null, ['class' => 'form-control']) !!}
</div>

<!-- Warehousesystemcode Field -->
<div class="form-group col-sm-6">
    {!! Form::label('warehouseSystemCode', 'Warehousesystemcode:') !!}
    {!! Form::number('warehouseSystemCode', null, ['class' => 'form-control']) !!}
</div>

<!-- Itemsystemcode Field -->
<div class="form-group col-sm-6">
    {!! Form::label('itemSystemCode', 'Itemsystemcode:') !!}
    {!! Form::number('itemSystemCode', null, ['class' => 'form-control']) !!}
</div>

<!-- Itemprimarycode Field -->
<div class="form-group col-sm-6">
    {!! Form::label('itemPrimaryCode', 'Itemprimarycode:') !!}
    {!! Form::text('itemPrimaryCode', null, ['class' => 'form-control']) !!}
</div>

<!-- Itemdescription Field -->
<div class="form-group col-sm-12 col-lg-12">
    {!! Form::label('itemDescription', 'Itemdescription:') !!}
    {!! Form::textarea('itemDescription', null, ['class' => 'form-control']) !!}
</div>

<!-- Unitofmeasure Field -->
<div class="form-group col-sm-6">
    {!! Form::label('unitOfMeasure', 'Unitofmeasure:') !!}
    {!! Form::number('unitOfMeasure', null, ['class' => 'form-control']) !!}
</div>

<!-- Stockqty Field -->
<div class="form-group col-sm-6">
    {!! Form::label('stockQty', 'Stockqty:') !!}
    {!! Form::number('stockQty', null, ['class' => 'form-control']) !!}
</div>

<!-- Maximunqty Field -->
<div class="form-group col-sm-6">
    {!! Form::label('maximunQty', 'Maximunqty:') !!}
    {!! Form::number('maximunQty', null, ['class' => 'form-control']) !!}
</div>

<!-- Minimumqty Field -->
<div class="form-group col-sm-6">
    {!! Form::label('minimumQty', 'Minimumqty:') !!}
    {!! Form::number('minimumQty', null, ['class' => 'form-control']) !!}
</div>

<!-- Rolquantity Field -->
<div class="form-group col-sm-6">
    {!! Form::label('rolQuantity', 'Rolquantity:') !!}
    {!! Form::number('rolQuantity', null, ['class' => 'form-control']) !!}
</div>

<!-- Wacvaluelocalcurrencyid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('wacValueLocalCurrencyID', 'Wacvaluelocalcurrencyid:') !!}
    {!! Form::number('wacValueLocalCurrencyID', null, ['class' => 'form-control']) !!}
</div>

<!-- Wacvaluelocal Field -->
<div class="form-group col-sm-6">
    {!! Form::label('wacValueLocal', 'Wacvaluelocal:') !!}
    {!! Form::number('wacValueLocal', null, ['class' => 'form-control']) !!}
</div>

<!-- Wacvaluereportingcurrencyid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('wacValueReportingCurrencyID', 'Wacvaluereportingcurrencyid:') !!}
    {!! Form::number('wacValueReportingCurrencyID', null, ['class' => 'form-control']) !!}
</div>

<!-- Wacvaluereporting Field -->
<div class="form-group col-sm-6">
    {!! Form::label('wacValueReporting', 'Wacvaluereporting:') !!}
    {!! Form::number('wacValueReporting', null, ['class' => 'form-control']) !!}
</div>

<!-- Totalqty Field -->
<div class="form-group col-sm-6">
    {!! Form::label('totalQty', 'Totalqty:') !!}
    {!! Form::number('totalQty', null, ['class' => 'form-control']) !!}
</div>

<!-- Totalvaluelocal Field -->
<div class="form-group col-sm-6">
    {!! Form::label('totalValueLocal', 'Totalvaluelocal:') !!}
    {!! Form::number('totalValueLocal', null, ['class' => 'form-control']) !!}
</div>

<!-- Totalvaluerpt Field -->
<div class="form-group col-sm-6">
    {!! Form::label('totalValueRpt', 'Totalvaluerpt:') !!}
    {!! Form::number('totalValueRpt', null, ['class' => 'form-control']) !!}
</div>

<!-- Financecategorymaster Field -->
<div class="form-group col-sm-6">
    {!! Form::label('financeCategoryMaster', 'Financecategorymaster:') !!}
    {!! Form::number('financeCategoryMaster', null, ['class' => 'form-control']) !!}
</div>

<!-- Financecategorysub Field -->
<div class="form-group col-sm-6">
    {!! Form::label('financeCategorySub', 'Financecategorysub:') !!}
    {!! Form::number('financeCategorySub', null, ['class' => 'form-control']) !!}
</div>

<!-- Binnumber Field -->
<div class="form-group col-sm-6">
    {!! Form::label('binNumber', 'Binnumber:') !!}
    {!! Form::number('binNumber', null, ['class' => 'form-control']) !!}
</div>

<!-- Todelete Field -->
<div class="form-group col-sm-6">
    {!! Form::label('toDelete', 'Todelete:') !!}
    {!! Form::number('toDelete', null, ['class' => 'form-control']) !!}
</div>

<!-- Timestamp Field -->
<div class="form-group col-sm-6">
    {!! Form::label('timestamp', 'Timestamp:') !!}
    {!! Form::date('timestamp', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('warehouseItems.index') !!}" class="btn btn-default">Cancel</a>
</div>
