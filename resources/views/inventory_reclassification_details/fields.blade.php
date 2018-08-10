<!-- Inventoryreclassificationid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('inventoryreclassificationID', 'Inventoryreclassificationid:') !!}
    {!! Form::number('inventoryreclassificationID', null, ['class' => 'form-control']) !!}
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

<!-- Financeglcodebbssystemid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('financeGLcodebBSSystemID', 'Financeglcodebbssystemid:') !!}
    {!! Form::number('financeGLcodebBSSystemID', null, ['class' => 'form-control']) !!}
</div>

<!-- Financeglcodebbs Field -->
<div class="form-group col-sm-6">
    {!! Form::label('financeGLcodebBS', 'Financeglcodebbs:') !!}
    {!! Form::text('financeGLcodebBS', null, ['class' => 'form-control']) !!}
</div>

<!-- Financeglcodeplsystemid Field -->
<div class="form-group col-sm-6">
    {!! Form::label('financeGLcodePLSystemID', 'Financeglcodeplsystemid:') !!}
    {!! Form::number('financeGLcodePLSystemID', null, ['class' => 'form-control']) !!}
</div>

<!-- Financeglcodepl Field -->
<div class="form-group col-sm-6">
    {!! Form::label('financeGLcodePL', 'Financeglcodepl:') !!}
    {!! Form::text('financeGLcodePL', null, ['class' => 'form-control']) !!}
</div>

<!-- Includeplforgrvyn Field -->
<div class="form-group col-sm-6">
    {!! Form::label('includePLForGRVYN', 'Includeplforgrvyn:') !!}
    {!! Form::number('includePLForGRVYN', null, ['class' => 'form-control']) !!}
</div>

<!-- Currentstockqty Field -->
<div class="form-group col-sm-6">
    {!! Form::label('currentStockQty', 'Currentstockqty:') !!}
    {!! Form::number('currentStockQty', null, ['class' => 'form-control']) !!}
</div>

<!-- Unitcostlocal Field -->
<div class="form-group col-sm-6">
    {!! Form::label('unitCostLocal', 'Unitcostlocal:') !!}
    {!! Form::number('unitCostLocal', null, ['class' => 'form-control']) !!}
</div>

<!-- Unitcostrpt Field -->
<div class="form-group col-sm-6">
    {!! Form::label('unitCostRpt', 'Unitcostrpt:') !!}
    {!! Form::number('unitCostRpt', null, ['class' => 'form-control']) !!}
</div>

<!-- Timestamp Field -->
<div class="form-group col-sm-6">
    {!! Form::label('timestamp', 'Timestamp:') !!}
    {!! Form::date('timestamp', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('inventoryReclassificationDetails.index') !!}" class="btn btn-default">Cancel</a>
</div>
