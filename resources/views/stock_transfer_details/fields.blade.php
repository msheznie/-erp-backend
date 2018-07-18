<!-- Stocktransferautoid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('stockTransferAutoID', 'Stocktransferautoid:') !!}
    {!! Form::number('stockTransferAutoID', null, ['class' => 'form-control']) !!}
</div>

<!-- Stocktransfercode Field -->
<div class="form-group col-sm-6">
    {!! Form::label('stockTransferCode', 'Stocktransfercode:') !!}
    {!! Form::text('stockTransferCode', null, ['class' => 'form-control']) !!}
</div>

<!-- Itemcodesystem Field -->
<div class="form-group col-sm-6">
    {!! Form::label('itemCodeSystem', 'Itemcodesystem:') !!}
    {!! Form::number('itemCodeSystem', null, ['class' => 'form-control']) !!}
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

<!-- Itemfinancecategoryid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('itemFinanceCategoryID', 'Itemfinancecategoryid:') !!}
    {!! Form::number('itemFinanceCategoryID', null, ['class' => 'form-control']) !!}
</div>

<!-- Itemfinancecategorysubid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('itemFinanceCategorySubID', 'Itemfinancecategorysubid:') !!}
    {!! Form::number('itemFinanceCategorySubID', null, ['class' => 'form-control']) !!}
</div>

<!-- Financeglcodebbs Field -->
<div class="form-group col-sm-6">
    {!! Form::label('financeGLcodebBS', 'Financeglcodebbs:') !!}
    {!! Form::text('financeGLcodebBS', null, ['class' => 'form-control']) !!}
</div>

<!-- Qty Field -->
<div class="form-group col-sm-6">
    {!! Form::label('qty', 'Qty:') !!}
    {!! Form::number('qty', null, ['class' => 'form-control']) !!}
</div>

<!-- Currentstockqty Field -->
<div class="form-group col-sm-6">
    {!! Form::label('currentStockQty', 'Currentstockqty:') !!}
    {!! Form::number('currentStockQty', null, ['class' => 'form-control']) !!}
</div>

<!-- Warehousestockqty Field -->
<div class="form-group col-sm-6">
    {!! Form::label('warehouseStockQty', 'Warehousestockqty:') !!}
    {!! Form::number('warehouseStockQty', null, ['class' => 'form-control']) !!}
</div>

<!-- Localcurrencyid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('localCurrencyID', 'Localcurrencyid:') !!}
    {!! Form::number('localCurrencyID', null, ['class' => 'form-control']) !!}
</div>

<!-- Unitcostlocal Field -->
<div class="form-group col-sm-6">
    {!! Form::label('unitCostLocal', 'Unitcostlocal:') !!}
    {!! Form::number('unitCostLocal', null, ['class' => 'form-control']) !!}
</div>

<!-- Reportingcurrencyid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('reportingCurrencyID', 'Reportingcurrencyid:') !!}
    {!! Form::number('reportingCurrencyID', null, ['class' => 'form-control']) !!}
</div>

<!-- Unitcostrpt Field -->
<div class="form-group col-sm-6">
    {!! Form::label('unitCostRpt', 'Unitcostrpt:') !!}
    {!! Form::number('unitCostRpt', null, ['class' => 'form-control']) !!}
</div>

<!-- Comments Field -->
<div class="form-group col-sm-12 col-lg-12">
    {!! Form::label('comments', 'Comments:') !!}
    {!! Form::textarea('comments', null, ['class' => 'form-control']) !!}
</div>

<!-- Addedtorecieved Field -->
<div class="form-group col-sm-6">
    {!! Form::label('addedToRecieved', 'Addedtorecieved:') !!}
    {!! Form::number('addedToRecieved', null, ['class' => 'form-control']) !!}
</div>

<!-- Stockrecieved Field -->
<div class="form-group col-sm-6">
    {!! Form::label('stockRecieved', 'Stockrecieved:') !!}
    {!! Form::number('stockRecieved', null, ['class' => 'form-control']) !!}
</div>

<!-- Timesreferred Field -->
<div class="form-group col-sm-6">
    {!! Form::label('timesReferred', 'Timesreferred:') !!}
    {!! Form::number('timesReferred', null, ['class' => 'form-control']) !!}
</div>

<!-- Timestamp Field -->
<div class="form-group col-sm-6">
    {!! Form::label('timestamp', 'Timestamp:') !!}
    {!! Form::date('timestamp', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('stockTransferDetails.index') !!}" class="btn btn-default">Cancel</a>
</div>
